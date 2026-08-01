<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PurchaseReportController extends Controller
{
    private const REPORTS = [
        'rppo' => ['table' => 'pesanan_pembelian as dok', 'date' => 'dok.tanggal', 'supplier' => 'dok.pemasok_id', 'currency' => 'dok.mata_uang_id', 'number' => 'dok.nomor', 'supplier_name' => 'dok.nama_pemasok_snapshot', 'total' => 'dok.total_akhir', 'statuses' => ['draf', 'terbuka', 'diproses', 'selesai', 'dibatalkan']],
        'rppnb' => ['table' => 'penerimaan_barang as dok', 'date' => 'dok.tanggal_penerimaan', 'supplier' => 'dok.pemasok_id', 'currency' => null, 'number' => 'dok.nomor_form', 'supplier_name' => 'p.nama', 'total' => null, 'statuses' => ['draf', 'diterima', 'difakturkan', 'dibatalkan']],
        'rpfkb' => ['table' => 'faktur_pembelian as dok', 'date' => 'dok.tanggal_faktur', 'supplier' => 'dok.pemasok_id', 'currency' => 'dok.mata_uang_id', 'number' => 'dok.nomor_form', 'supplier_name' => 'p.nama', 'total' => 'dok.total_akhir', 'statuses' => ['draf', 'terutang', 'dibayar_sebagian', 'lunas', 'dibatalkan']],
        'rppby' => ['table' => 'pembayaran_pembelian as dok', 'date' => 'dok.tanggal_pembayaran', 'supplier' => 'dok.pemasok_id', 'currency' => 'dok.mata_uang_id', 'number' => 'dok.nomor_bukti', 'supplier_name' => 'p.nama', 'total' => 'dok.nilai_pembayaran', 'statuses' => ['draf', 'diposting', 'dibatalkan']],
    ];

    public function page(array $data, string $report)
    {
        abort_unless(array_key_exists($report, self::REPORTS), 404);

        // Sediakan pilihan filter untuk halaman manual tanpa mengubah dispatcher framework.
        $data['report'] = $report;
        $data['statuses'] = self::REPORTS[$report]['statuses'];
        $data['suppliers'] = DB::table('pemasok')->where('isactive', '1')->orderBy('nama')->get(['id', 'nama']);
        $data['currencies'] = DB::table('mata_uang')->where('isactive', '1')->orderBy('kode')->get(['id', 'kode']);

        return view("report.{$report}.filter", $data);
    }

    public function data(Request $request, string $report)
    {
        $filters = $this->filters($request, $report);
        $this->authorizeReport($report, 'view');
        $query = $this->query($report, $filters);
        $totalFilters = $filters;
        foreach (['supplier_id', 'status', 'keyword', 'currency_id', 'unit'] as $key) {
            $totalFilters[$key] = null;
        }
        $total = $this->query($report, $totalFilters)->count();
        $filtered = (clone $query)->count();
        $sort = $filters['sort'];
        $rows = $query->orderBy($sort, $filters['direction'])->offset($filters['start'])->limit($filters['length'])->get();

        return response()->json(['draw' => $filters['draw'], 'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'data' => $rows, 'summary' => $this->summary($report, $filters)]);
    }

    public function export(Request $request, string $report, string $format)
    {
        abort_unless(in_array($format, ['excel', 'pdf', 'print'], true), 404);
        $filters = $this->filters($request, $report);
        $this->authorizeReport($report, $format);
        $rows = $this->query($report, $filters)->orderBy($filters['sort'], $filters['direction'])->get();
        $title = 'Laporan Pembelian';

        if ($format === 'print') {
            return view('report.partials.export', compact('rows', 'title', 'report'));
        }
        if ($format === 'pdf') {
            $pdf = new Mpdf(['format' => 'A4-L']);
            $pdf->WriteHTML(view('report.partials.export', compact('rows', 'title', 'report'))->render());

            return response($pdf->Output('', 'S'), 200, ['Content-Type' => 'application/pdf']);
        }
        $sheet = (new Spreadsheet)->getActiveSheet();
        $sheet->fromArray(['Nomor', 'Tanggal', 'Pemasok', 'Mata Uang', 'Nilai', 'Status'], null, 'A1');
        foreach ($rows as $i => $row) {
            $sheet->fromArray([$row->nomor, $row->tanggal, $row->pemasok, $row->mata_uang, $row->nilai, $row->status], null, 'A'.($i + 2));
        }

        return response()->streamDownload(fn () => (new Xlsx($sheet->getParent()))->save('php://output'), "{$report}.xlsx");
    }

    private function query(string $report, array $filters)
    {
        $config = self::REPORTS[$report] ?? abort(404);
        $query = DB::table($config['table'])->leftJoin('pemasok as p', 'p.id', '=', 'dok.pemasok_id')->leftJoin('mata_uang as mu', 'mu.id', '=', 'dok.mata_uang_id')
            ->select('dok.id', DB::raw("{$config['number']} as nomor"), DB::raw("{$config['date']} as tanggal"), DB::raw("{$config['supplier_name']} as pemasok"), DB::raw('COALESCE(mu.kode, \'-\') as mata_uang'), 'dok.status');
        if ($config['total']) {
            $query->addSelect(DB::raw("{$config['total']} as nilai"));
        } else {
            $query->addSelect(DB::raw('NULL as nilai'));
        }
        if ($report === 'rppnb') {
            $query->addSelect(DB::raw('(select count(*) from rincian_penerimaan_barang r where r.penerimaan_barang_id = dok.id) as jumlah_baris'));
        }
        $query->whereBetween($config['date'], [$filters['date_from'], $filters['date_to']]);
        foreach (['supplier_id' => $config['supplier'], 'status' => 'dok.status'] as $key => $column) {
            if ($filters[$key] !== null) {
                $query->where($column, $filters[$key]);
            }
        }
        if ($filters['currency_id'] !== null) {
            $query->where($config['currency'], $filters['currency_id']);
        }
        if ($filters['unit'] !== null) {
            $query->whereExists(fn ($q) => $q->selectRaw(1)->from('rincian_penerimaan_barang as r')->whereColumn('r.penerimaan_barang_id', 'dok.id')->where('r.satuan_snapshot', $filters['unit']));
        }
        if ($filters['keyword'] !== null) {
            $query->where(fn ($q) => $q->where($config['number'], 'like', "%{$filters['keyword']}%")->orWhere($config['supplier_name'], 'like', "%{$filters['keyword']}%"));
        }

        return $query;
    }

    private function filters(Request $request, string $report): array
    {
        $config = self::REPORTS[$report] ?? abort(404);
        $now = now();
        $data = Validator::make($request->all(), ['date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'], 'supplier_id' => ['nullable', 'integer', 'exists:pemasok,id'], 'status' => ['nullable', Rule::in($config['statuses'])], 'keyword' => ['nullable', 'string', 'max:100'], 'currency_id' => ['nullable', 'integer', 'exists:mata_uang,id'], 'unit' => ['nullable', 'string', 'max:100'], 'start' => ['nullable', 'integer', 'min:0'], 'length' => ['nullable', Rule::in([10, 25, 50, 100])], 'draw' => ['nullable', 'integer', 'min:0'], 'sort' => ['nullable', Rule::in(['nomor', 'tanggal', 'pemasok', 'mata_uang', 'nilai', 'status'])], 'direction' => ['nullable', Rule::in(['asc', 'desc'])]])->validate();
        abort_if($data['currency_id'] ?? null, $config['currency'] === null, 422);
        abort_if($data['unit'] ?? null, $report !== 'rppnb', 422);

        return array_filter($data, static fn ($value) => $value !== null) + ['date_from' => $now->copy()->startOfMonth()->toDateString(), 'date_to' => $now->copy()->endOfMonth()->toDateString(), 'supplier_id' => null, 'status' => null, 'keyword' => null, 'currency_id' => null, 'unit' => null, 'start' => 0, 'length' => 25, 'draw' => 0, 'sort' => 'tanggal', 'direction' => 'desc'];
    }

    private function summary(string $report, array $filters): array
    {
        $query = $this->query($report, $filters);
        $statuses = (clone $query)->select('dok.status', DB::raw('count(*) as count'))->groupBy('dok.status')->pluck('count', 'status');
        $summary = ['document_count' => (clone $query)->count(), 'statuses' => $statuses];
        $config = self::REPORTS[$report];
        if ($config['total'] && $filters['currency_id']) {
            $summary['total'] = (clone $query)->sum($config['total']);
        }
        if ($report === 'rppnb' && $filters['unit']) {
            $summary['quantity'] = DB::table('rincian_penerimaan_barang as r')->join('penerimaan_barang as dok', 'dok.id', '=', 'r.penerimaan_barang_id')->whereBetween('dok.tanggal_penerimaan', [$filters['date_from'], $filters['date_to']])->where('r.satuan_snapshot', $filters['unit'])->sum('r.kuantitas');
        }

        return $summary;
    }

    private function authorizeReport(string $report, string $ability): void
    {
        $roles = array_filter(explode(',', (string) auth()->user()?->idroles));
        $column = $ability === 'view' ? 'dmenu' : $ability;
        abort_unless($roles && DB::table('sys_auth')->where('dmenu', $report)->whereIn('idroles', $roles)->where('isactive', '1')->when($ability !== 'view', fn ($q) => $q->where($column, '1'))->exists(), 403);
    }
}
