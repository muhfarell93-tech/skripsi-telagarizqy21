<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PengeluaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_an_expense(): void
    {
        $outlet = Outlet::create(['kode_outlet' => 'OTL-01', 'nama_outlet' => 'Outlet Uji']);
        $admin = User::create([
            'outlet_id' => $outlet->id,
            'name' => 'Admin Uji',
            'username' => 'admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('a-secure-test-password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post('/pengeluaran', [
            'kategori_pengeluaran' => 'Operasional',
            'keterangan' => 'Biaya listrik',
            'nominal' => 250000,
            'tanggal_pengeluaran' => '2026-11-13T10:00',
        ])->assertRedirect('/pengeluaran')->assertSessionHas('success');

        $this->assertDatabaseHas('pengeluarans', [
            'outlet_id' => $outlet->id,
            'user_id' => $admin->id,
            'kategori_pengeluaran' => 'Operasional',
            'nominal' => '250000.00',
        ]);
        $this->assertSame(1, Pengeluaran::count());
    }
}
