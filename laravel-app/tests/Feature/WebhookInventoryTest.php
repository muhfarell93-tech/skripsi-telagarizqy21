<?php

namespace Tests\Feature;

use App\Models\DetailTransaksi;
use App\Models\KategoriProduk;
use App\Models\Outlet;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebhookInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['midtrans.server_key' => 'test-server-key']);
    }

    public function test_replayed_settlement_does_not_reduce_reserved_stock_twice(): void
    {
        [$transaction, $product] = $this->pendingOrder();
        $payload = $this->notification($transaction, 'settlement');

        $this->postJson('/webhook/midtrans', $payload)->assertOk();
        $this->postJson('/webhook/midtrans', $payload)->assertOk();
        $this->postJson('/webhook/midtrans', $this->notification($transaction, 'pending'))->assertOk();

        $this->assertSame(4, $product->fresh()->stok);
        $this->assertSame('lunas', $transaction->fresh()->status_pembayaran);
        $this->assertSame(1, $transaction->fresh()->gatewayLogs()->count());
    }

    public function test_replayed_expiration_releases_reserved_stock_only_once(): void
    {
        [$transaction, $product] = $this->pendingOrder();
        $payload = $this->notification($transaction, 'expire');

        $this->postJson('/webhook/midtrans', $payload)->assertOk();
        $this->postJson('/webhook/midtrans', $payload)->assertOk();

        $this->assertSame(5, $product->fresh()->stok);
        $this->assertSame('batal', $transaction->fresh()->status_pembayaran);
        $this->assertFalse($transaction->fresh()->stok_dikurangi);
    }

    public function test_late_settlement_after_expiration_is_applied_once(): void
    {
        [$transaction, $product] = $this->pendingOrder();

        $this->postJson('/webhook/midtrans', $this->notification($transaction, 'expire'))->assertOk();
        $settlement = $this->notification($transaction, 'settlement');
        $this->postJson('/webhook/midtrans', $settlement)->assertOk();
        $this->postJson('/webhook/midtrans', $settlement)->assertOk();

        $this->assertSame(4, $product->fresh()->stok);
        $this->assertSame('lunas', $transaction->fresh()->status_pembayaran);
        $this->assertTrue($transaction->fresh()->stok_dikurangi);
    }

    public function test_notification_with_wrong_amount_cannot_mark_transaction_paid(): void
    {
        [$transaction, $product] = $this->pendingOrder();
        $payload = $this->notification($transaction, 'settlement');
        $payload['gross_amount'] = '999.00';
        $payload['signature_key'] = hash('sha512', $transaction->kode_invoice.'200999.00test-server-key');

        $this->postJson('/webhook/midtrans', $payload)->assertStatus(422);
        $this->assertSame('belum_lunas', $transaction->fresh()->status_pembayaran);
        $this->assertSame(4, $product->fresh()->stok);
    }

    private function pendingOrder(): array
    {
        $outlet = Outlet::create(['kode_outlet' => 'OTL-01', 'nama_outlet' => 'Outlet Uji']);
        $user = User::create([
            'outlet_id' => $outlet->id, 'name' => 'Kasir Uji', 'username' => 'kasir',
            'email' => 'kasir@example.test', 'password' => Hash::make('a-secure-test-password'),
            'role' => 'kasir', 'is_active' => true,
        ]);
        $category = KategoriProduk::create(['nama_kategori' => 'Produk Uji']);
        $product = Produk::create([
            'outlet_id' => $outlet->id, 'kategori_id' => $category->id, 'kode_produk' => 'PRD-001',
            'nama_produk' => 'Produk Uji', 'harga_modal' => 50, 'harga_jual' => 100,
            'stok' => 4, 'stok_minimum' => 1, 'satuan' => 'unit',
        ]);
        $transaction = Transaksi::create([
            'kode_invoice' => 'INV-TEST-001', 'outlet_id' => $outlet->id, 'user_id' => $user->id,
            'tanggal_transaksi' => now(), 'total_item' => 1, 'total_harga' => 100,
            'total_bayar' => 0, 'kembalian' => 0, 'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'belum_lunas', 'stok_dikurangi' => true,
        ]);
        DetailTransaksi::create([
            'transaksi_id' => $transaction->id, 'produk_id' => $product->id,
            'harga_satuan' => 100, 'kuantitas' => 1, 'subtotal' => 100,
        ]);

        return [$transaction, $product];
    }

    private function notification(Transaksi $transaction, string $status): array
    {
        $grossAmount = '100.00';
        $statusCode = '200';

        return [
            'order_id' => $transaction->kode_invoice,
            'transaction_id' => 'GW-TEST-001',
            'transaction_status' => $status,
            'fraud_status' => 'accept',
            'payment_type' => 'qris',
            'gross_amount' => $grossAmount,
            'status_code' => $statusCode,
            'signature_key' => hash('sha512', $transaction->kode_invoice.$statusCode.$grossAmount.'test-server-key'),
            'settlement_time' => '2026-11-13 10:00:00',
        ];
    }
}
