<?php

namespace App\Models;

use CodeIgniter\Model;

class MutabaahLogModel extends Model
{
    protected $table            = 'mutabaah_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['member_id', 'tanggal', 'catatan'];
    protected $useTimestamps = true;

    /**
     * Get log record for a member on a specific date, including items values
     */
    public function getLogForDate(int $memberId, string $tanggal): ?array
    {
        $log = $this->where('member_id', $memberId)
                    ->where('tanggal', $tanggal)
                    ->first();

        if (!$log) {
            return null;
        }

        $db = \Config\Database::connect();
        $items = $db->table('mutabaah_log_items')
                    ->where('mutabaah_log_id', $log['id'])
                    ->get()
                    ->getResultArray();

        $log['items'] = [];
        foreach ($items as $item) {
            $log['items'][$item['mutabaah_item_id']] = $item['nilai'];
        }

        return $log;
    }

    /**
     * Save or update daily mutabaah log and items
     */
    public function saveDailyLog(int $memberId, string $tanggal, ?string $catatan, array $itemValues): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $existing = $this->where('member_id', $memberId)
                         ->where('tanggal', $tanggal)
                         ->first();

        if ($existing) {
            $logId = $existing['id'];
            $this->update($logId, [
                'catatan' => $catatan,
            ]);
        } else {
            $this->insert([
                'member_id' => $memberId,
                'tanggal'   => $tanggal,
                'catatan'   => $catatan,
            ]);
            $logId = $this->insertID();
        }

        // Save item values
        $logItemTable = $db->table('mutabaah_log_items');
        foreach ($itemValues as $itemId => $val) {
            $valStr = (string)$val;
            $existItem = $logItemTable->where('mutabaah_log_id', $logId)
                                      ->where('mutabaah_item_id', $itemId)
                                      ->get()
                                      ->getRowArray();

            if ($existItem) {
                $logItemTable->where('id', $existItem['id'])
                             ->update(['nilai' => $valStr]);
            } else {
                $logItemTable->insert([
                    'mutabaah_log_id'  => $logId,
                    'mutabaah_item_id' => $itemId,
                    'nilai'            => $valStr,
                ]);
            }
        }

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Get monthly recap for a member
     */
    public function getMonthlyRecap(int $memberId, int $tahun, int $bulan): array
    {
        $db = \Config\Database::connect();
        $startDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $endDate = date('Y-m-t', strtotime($startDate));
        $daysInMonth = (int)date('t', strtotime($startDate));

        $logs = $this->where('member_id', $memberId)
                     ->where('tanggal >=', $startDate)
                     ->where('tanggal <=', $endDate)
                     ->orderBy('tanggal', 'ASC')
                     ->findAll();

        $loggedDays = [];
        $logIds = [];
        foreach ($logs as $l) {
            $loggedDays[$l['tanggal']] = $l;
            $logIds[] = $l['id'];
        }

        // Get all active mutabaah items
        $itemModel = new \App\Models\MutabaahItemModel();
        $items = $itemModel->where('is_active', 1)
                           ->orderBy('urutan', 'ASC')
                           ->findAll();

        // Get all log items
        $itemStats = [];
        foreach ($items as $it) {
            $itemStats[$it['id']] = [
                'item'         => $it,
                'total_terisi' => 0,
                'total_nilai'  => 0,
            ];
        }

        if (!empty($logIds)) {
            $logItems = $db->table('mutabaah_log_items')
                           ->whereIn('mutabaah_log_id', $logIds)
                           ->get()
                           ->getResultArray();

            foreach ($logItems as $li) {
                $itId = $li['mutabaah_item_id'];
                if (isset($itemStats[$itId])) {
                    if ($itemStats[$itId]['item']['tipe_input'] === 'boolean') {
                        if ($li['nilai'] === '1' || strtolower($li['nilai']) === 'ya') {
                            $itemStats[$itId]['total_terisi']++;
                        }
                    } else {
                        // numeric / scale
                        $val = (float)$li['nilai'];
                        if ($val > 0) {
                            $itemStats[$itId]['total_terisi']++;
                            $itemStats[$itId]['total_nilai'] += $val;
                        }
                    }
                }
            }
        }

        $daysFilledCount = count($loggedDays);
        $percentageFilled = round(($daysFilledCount / $daysInMonth) * 100, 1);

        return [
            'tahun'             => $tahun,
            'bulan'             => $bulan,
            'days_in_month'     => $daysInMonth,
            'days_filled_count' => $daysFilledCount,
            'percentage_filled' => $percentageFilled,
            'logged_days'       => $loggedDays,
            'item_stats'        => $itemStats,
            'items'             => $items,
        ];
    }
}
