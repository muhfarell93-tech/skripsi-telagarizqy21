<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FinancialOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_checkout_updates_transaction_stock_and_financial_report(): void
    {
        [$admin, $cashier, $product] = $this->fixtures();

        $checkout = $this->actingAs($cashier)->postJson('/pos/checkout', [
            'items' => [['id' => $product->id, 'qty' => 2]],
            'metode_pembayaran' => 'tunai',
            'total_bayar' => 25000,
        ])->assertOk()->assertJsonPath('success', true);

        $invoice = $checkout->json('invoice');
        $this->assertDatabaseHas('transaksis', [
            'kode_invoice' => $invoice, 'status_pembayaran' => 'lunas', 'total_harga' => '20000.00',
        ]);
        $this->assertSame(8, $product->fresh()->stok);

        $this->actingAs($admin)->post('/pemasukan', [
            'kategori_pemasukan' => 'Pendapatan lain', 'keterangan' => 'Penerimaan administrasi',
            'nominal' => 50000, 'tanggal_pemasukan' => now()->format('Y-m-d\\TH:i'),
        ])->assertRedirect('/pemasukan');
        $this->actingAs($admin)->post('/pengeluaran', [
            'kategori_pengeluaran' => 'Operasional', 'keterangan' => 'Biaya listrik',
            'nominal' => 10000, 'tanggal_pengeluaran' => now()->format('Y-m-d\\TH:i'),
        ])->assertRedirect('/pengeluaran');

        $this->actingAs($admin)->get('/laporan?periode=bulan_ini')->assertOk()->assertSee('Rp 70.000');
        $this->actingAs($admin)->get('/laporan/export?periode=bulan_ini')->assertDownload();
        $this->assertSame(1, Transaksi::where('status_pembayaran', 'lunas')->count());
    }

    public function test_qris_checkout_remains_pending_until_gateway_confirms_payment(): void
    {
        [, $cashier, $product] = $this->fixtures();
        config(['midtrans.server_key' => 'sandbox-test-key', 'midtrans.snap_api_url' => 'https://example.test/snap']);
        Http::fake(['https://example.test/snap' => Http::response(['token' => 'snap-test-token', 'redirect_url' => 'https://example.test/pay'], 201)]);

        $response = $this->actingAs($cashier)->postJson('/pos/checkout', [
            'items' => [['id' => $product->id, 'qty' => 1]], 'metode_pembayaran' => 'qris',
        ])->assertOk()->assertJsonPath('success', true)->assertJsonPath('snap_token', 'snap-test-token');

        $transaction = Transaksi::where('kode_invoice', $response->json('invoice'))->firstOrFail();
        $this->assertSame('belum_lunas', $transaction->status_pembayaran);
        $this->assertFalse($transaction->is_demo);
        $this->assertSame(9, $product->fresh()->stok);
    }

    public function test_admin_can_manage_categories_and_cashier_accounts(): void
    {
        [$admin] = $this->fixtures();
        $this->actingAs($admin)->post('/kategori', ['nama_kategori' => 'Produk baru'])->assertRedirect('/kategori');
        $this->assertDatabaseHas('kategori_produks', ['nama_kategori' => 'Produk baru']);

        $this->actingAs($admin)->post('/kasir', [
            'name' => 'Kasir Baru', 'username' => 'kasir_baru', 'email' => 'baru@example.test',
            'password' => 'uji1234', 'password_confirmation' => 'uji1234',
        ])->assertRedirect('/kasir');
        $this->assertDatabaseHas('users', ['username' => 'kasir_baru', 'role' => 'kasir', 'is_active' => 1]);
    }

    public function test_admin_financial_pages_render_from_database(): void
    {
        [$admin] = $this->fixtures();
        foreach (['/', '/produk', '/kategori', '/kasir', '/pemasukan', '/pengeluaran', '/pembayaran', '/laporan'] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }
    }

    public function test_demo_seeder_never_marks_illustrative_sales_as_paid(): void
    {
        [, , $product] = $this->fixtures();
        $this->seed(\Database\Seeders\DemoDataSeeder::class);

        $this->assertGreaterThan(0, Transaksi::where('is_demo', true)->count());
        $this->assertSame(0, Transaksi::where('is_demo', true)->where('status_pembayaran', 'lunas')->count());
        $this->assertSame(10, $product->fresh()->stok);
    }

    private function fixtures(): array
    {
        $outlet = Outlet::create(['kode_outlet' => 'OTL-01', 'nama_outlet' => 'Outlet Uji']);
        $admin = $this->user($outlet->id, 'admin', 'admin');
        $cashier = $this->user($outlet->id, 'kasir', 'kasir');
        $category = KategoriProduk::create(['nama_kategori' => 'Produk Uji']);
        $product = Produk::create([
            'outlet_id' => $outlet->id, 'kategori_id' => $category->id, 'kode_produk' => 'PRD-001',
            'nama_produk' => 'Susu Uji', 'harga_modal' => 5000, 'harga_jual' => 10000,
            'stok' => 10, 'stok_minimum' => 1, 'satuan' => 'kotak',
        ]);

        return [$admin, $cashier, $product];
    }

    private function user(int $outletId, string $role, string $username): User
    {
        return User::create([
            'outlet_id' => $outletId, 'name' => ucfirst($role), 'username' => $username,
            'email' => $username.'@example.test', 'password' => Hash::make('a-secure-test-password'),
            'role' => $role, 'is_active' => true,
        ]);
    }
}
