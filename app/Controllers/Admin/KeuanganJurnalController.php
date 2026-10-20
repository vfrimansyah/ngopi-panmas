<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FinJurnalModel;

class KeuanganJurnalController extends BaseController
{
    protected $model;
    public function __construct() { $this->model = new FinJurnalModel(); }

    public function index()
    {
        $filter = [
            'bulan' => $this->request->getGet('bulan') ?? date('n'),
            'tahun' => $this->request->getGet('tahun') ?? date('Y'),
        ];

        $jurnals = $this->model->getList($filter);

        // Ambil total debit/kredit per jurnal di controller (bukan di view)
        $db = \Config\Database::connect();
        $jurnalTotals = [];
        foreach ($jurnals as $j) {
            $tot = $db->query(
                "SELECT COALESCE(SUM(debit),0) AS d, COALESCE(SUM(kredit),0) AS k FROM fin_jurnal_detail WHERE jurnal_id = ?",
                [$j['id']]
            )->getRowArray();
            $jurnalTotals[$j['id']] = $tot;
        }

        // Total keseluruhan periode
        $totals = $db->query("
            SELECT COALESCE(SUM(jd.debit),0) AS total_debit, COALESCE(SUM(jd.kredit),0) AS total_kredit
            FROM fin_jurnal j
            JOIN fin_jurnal_detail jd ON jd.jurnal_id = j.id
            WHERE MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?
        ", [$filter['bulan'], $filter['tahun']])->getRowArray();

        return view('admin/keuangan/jurnal/index', [
            'title'        => 'Jurnal Umum',
            'jurnals'      => $jurnals,
            'jurnalTotals' => $jurnalTotals,
            'filter'       => $filter,
            'totalDebit'   => (float) ($totals['total_debit']  ?? 0),
            'totalKredit'  => (float) ($totals['total_kredit'] ?? 0),
        ]);
    }

    public function show(int $id)
    {
        $jurnal = $this->model->getWithDetail($id);
        if (!$jurnal) return redirect()->to(base_url('admin/keuangan/jurnal'))->with('error', 'Jurnal tidak ditemukan.');

        return view('admin/keuangan/jurnal/show', [
            'title'  => 'Detail Jurnal — ' . $jurnal['nomor_jurnal'],
            'jurnal' => $jurnal,
        ]);
    }
}
