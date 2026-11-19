<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\PemasukanManual;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);
        [$startDate, $endDate] = $this->dateRange($filters);
        $outletId = Auth::user()->outlet_id;
        $includeDemo = app()->environment(['local', 'testing']);

        $sales = Transaksi::query()->where('outlet_id', $outletId)->where('status_pembayaran', 'lunas')
            ->whereDate('tanggal_transaksi', '>=', $startDate)->whereDate('tanggal_transaksi', '<=', $endDate)
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where('kode_invoice', 'like', '%'.$term.'%'));
        $income = PemasukanManual::query()->where('outlet_id', $outletId)
            ->whereDate('tanggal_pemasukan', '>=', $startDate)->whereDate('tanggal_pemasukan', '<=', $endDate)
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($q) => $q->where('kode_pemasukan', 'like', '%'.$term.'%')->orWhere('kategori_pemasukan', 'like', '%'.$term.'%')->orWhere('keterangan', 'like', '%'.$term.'%')));
        $expenses = Pengeluaran::query()->where('outlet_id', $outletId)
            ->whereDate('tanggal_pengeluaran', '>=', $startDate)->whereDate('tanggal_pengeluaran', '<=', $endDate)
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($q) => $q->where('kode_pengeluaran', 'like', '%'.$term.'%')->orWhere('kategori_pengeluaran', 'like', '%'.$term.'%')->orWhere('keterangan', 'like', '%'.$term.'%')));

        $totalPenjualan = (clone $sales)->sum('total_harga');
        $totalPemasukanLain = (clone $income)->sum('nominal');
        $totalPemasukan = $totalPenjualan + $totalPemasukanLain;
        $totalPengeluaran = (clone $expenses)->sum('nominal');
        $labaBersih = $totalPemasukan - $totalPengeluaran;
        $jumlahTransaksi = (clone $sales)->count();
        $jumlahPemasukanLain = (clone $income)->count();
        $jumlahPengeluaran = (clone $expenses)->count();
        $transaksis = (clone $sales)->with(['user', 'details.produk'])->latest('tanggal_transaksi')->paginate(15, ['*'], 'sales_page')->withQueryString();
        $pemasukans = (clone $income)->with('user')->latest('tanggal_pemasukan')->paginate(15, ['*'], 'income_page')->withQueryString();
        $pengeluarans = (clone $expenses)->with('user')->latest('tanggal_pengeluaran')->paginate(15, ['*'], 'expenses_page')->withQueryString();
        $adaDataDemo = (clone $sales)->where('is_demo', true)->exists() || (clone $income)->where('is_demo', true)->exists() || (clone $expenses)->where('is_demo', true)->exists();

        return view('laporan.index', compact(
            'transaksis',
            'pengeluarans',
            'totalPenjualan',
            'totalPengeluaran',
            'totalPemasukan',
            'totalPemasukanLain',
            'jumlahTransaksi', 'jumlahPemasukanLain', 'jumlahPengeluaran', 'pemasukans',
            'labaBersih',
            'startDate',
            'endDate', 'filters', 'adaDataDemo'
        ));
    }

    public function export(Request $request)
    {
        $filters = $this->validatedFilters($request);
        [$startDate, $endDate] = $this->dateRange($filters);
        $outletId = Auth::user()->outlet_id;
        $includeDemo = app()->environment(['local', 'testing']);
        $rows = collect();

        $sales = Transaksi::with('user')->where('outlet_id', $outletId)->where('status_pembayaran', 'lunas')
            ->whereDate('tanggal_transaksi', '>=', $startDate)->whereDate('tanggal_transaksi', '<=', $endDate)
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where('kode_invoice', 'like', '%'.$term.'%'))->get();
        foreach ($sales as $sale) $rows->push([$sale->tanggal_transaksi->format('Y-m-d H:i:s'), 'Penjualan', $sale->kode_invoice, $sale->user->name ?? '', $sale->metode_pembayaran, $sale->status_pembayaran, $sale->total_harga, $sale->is_demo ? 'DEMO' : '']);

        $incomes = PemasukanManual::with('user')->where('outlet_id', $outletId)->whereDate('tanggal_pemasukan', '>=', $startDate)->whereDate('tanggal_pemasukan', '<=', $endDate)
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($q) => $q->where('kode_pemasukan', 'like', '%'.$term.'%')->orWhere('kategori_pemasukan', 'like', '%'.$term.'%')->orWhere('keterangan', 'like', '%'.$term.'%')))->get();
        foreach ($incomes as $item) $rows->push([$item->tanggal_pemasukan->format('Y-m-d H:i:s'), 'Pemasukan lain: '.$item->kategori_pemasukan, $item->kode_pemasukan, $item->user->name ?? '', '—', 'tercatat', $item->nominal, $item->is_demo ? 'DEMO' : '']);

        $expenses = Pengeluaran::with('user')->where('outlet_id', $outletId)->whereDate('tanggal_pengeluaran', '>=', $startDate)->whereDate('tanggal_pengeluaran', '<=', $endDate)
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($q) => $q->where('kode_pengeluaran', 'like', '%'.$term.'%')->orWhere('kategori_pengeluaran', 'like', '%'.$term.'%')->orWhere('keterangan', 'like', '%'.$term.'%')))->get();
        foreach ($expenses as $item) $rows->push([$item->tanggal_pengeluaran->format('Y-m-d H:i:s'), 'Pengeluaran: '.$item->kategori_pengeluaran, $item->kode_pengeluaran, $item->user->name ?? '', '—', 'tercatat', -$item->nominal, $item->is_demo ? 'DEMO' : '']);

        $filename = 'laporan-keuangan-'.$startDate.'-'.$endDate.'.csv';
        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Tanggal', 'Jenis', 'Kode', 'Pengguna', 'Metode', 'Status', 'Nominal (IDR)', 'Penanda']);
            foreach ($rows as $row) {
                foreach ([1, 2, 3, 4, 5, 7] as $index) {
                    if (isset($row[$index]) && is_string($row[$index]) && preg_match('/^[=+@-]/', $row[$index])) {
                        $row[$index] = "'".$row[$index];
                    }
                }
                fputcsv($handle, $row, ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'periode' => ['nullable', 'in:hari_ini,minggu_ini,bulan_ini,tahun_ini,kustom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
    }

    private function dateRange(array $filters): array
    {
        $today = Carbon::today();
        $period = $filters['periode'] ?? 'bulan_ini';
        [$start, $end] = match ($period) {
            'hari_ini' => [$today->copy(), $today->copy()],
            'minggu_ini' => [$today->copy()->startOfWeek(Carbon::MONDAY), $today->copy()->endOfWeek(Carbon::SUNDAY)],
            'tahun_ini' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            'kustom' => [Carbon::parse($filters['start_date'] ?? $today->copy()->startOfMonth()), Carbon::parse($filters['end_date'] ?? $today)],
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
        };
        return [$start->toDateString(), $end->toDateString()];
    }
}

