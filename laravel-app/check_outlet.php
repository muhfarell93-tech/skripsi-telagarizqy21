<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== USERS ===\n";
foreach (App\Models\User::all() as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Role: {$u->role} | Outlet ID: {$u->outlet_id}\n";
}

echo "\n=== OUTLETS ===\n";
foreach (App\Models\Outlet::all() as $o) {
    echo "ID: {$o->id} | Kode: {$o->kode_outlet} | Nama: {$o->nama_outlet}\n";
}

echo "\n=== PRODUKS ===\n";
foreach (App\Models\Produk::all() as $p) {
    echo "ID: {$p->id} | Nama: {$p->nama_produk} | Outlet ID: {$p->outlet_id}\n";
}

