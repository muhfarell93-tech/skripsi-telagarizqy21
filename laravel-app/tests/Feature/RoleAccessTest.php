<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_use_pos_but_cannot_open_admin_pages(): void
    {
        $cashier = $this->userWithRole('kasir');

        $this->actingAs($cashier)->get('/pos')->assertOk();
        $this->actingAs($cashier)->get('/')->assertForbidden();
        $this->actingAs($cashier)->get('/produk')->assertForbidden();
        $this->actingAs($cashier)->get('/pengeluaran')->assertForbidden();
        $this->actingAs($cashier)->get('/laporan')->assertForbidden();
    }

    private function userWithRole(string $role): User
    {
        $outlet = Outlet::create(['kode_outlet' => 'OTL-01', 'nama_outlet' => 'Outlet Uji']);

        return User::create([
            'outlet_id' => $outlet->id,
            'name' => ucfirst($role),
            'username' => $role,
            'email' => $role.'@example.test',
            'password' => Hash::make('a-secure-test-password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
