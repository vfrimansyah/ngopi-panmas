<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanAbsensiModel extends Model
{
    protected $table         = 'kegiatan_absensi';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'kegiatan_id',
        'member_id',
        'status',
        'catatan',
        'recorded_by',
    ];

    protected $validationRules = [
        'kegiatan_id' => 'required|integer',
        'member_id'   => 'required|integer',
        'status'      => 'required|in_list[hadir,izin,sakit,alfa]',
    ];

    /**
     * Get all attendance records for a kegiatan, joined with member info.
     */
    public function getByKegiatan(int $kegiatanId): array
    {
        return $this->db->table('kegiatan_absensi a')
            ->select('a.*, m.nama_lengkap, m.kode_upa, m.jenis_kelamin')
            ->join('members m', 'm.id = a.member_id', 'left')
            ->where('a.kegiatan_id', $kegiatanId)
            ->orderBy('m.nama_lengkap', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Get all kegiatan records attended by a member, joined with kegiatan info.
     */
    public function getByMember(int $memberId): array
    {
        return $this->db->table('kegiatan_absensi a')
            ->select('a.status, a.catatan, a.created_at as absen_at,
                      k.id as kegiatan_id, k.nama_kegiatan, k.tanggal, k.jam_mulai, k.jam_selesai,
                      k.lokasi_kegiatan, k.penyelenggara, k.dpra,
                      j.nama_kegiatan as jenis_kegiatan_nama')
            ->join('kegiatan_list k', 'k.id = a.kegiatan_id', 'left')
            ->join('kegiatan_jenis j', 'j.id = k.jenis_kegiatan_id', 'left')
            ->where('a.member_id', $memberId)
            ->orderBy('k.tanggal', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Save batch attendance for a kegiatan.
     * Deletes existing records and re-inserts.
     */
    public function saveAbsensi(int $kegiatanId, array $attendanceData, ?int $recordedBy = null): bool
    {
        $this->db->transStart();

        // Delete existing attendance for this kegiatan
        $this->where('kegiatan_id', $kegiatanId)->delete();

        // Insert new attendance records
        if (!empty($attendanceData)) {
            $now = date('Y-m-d H:i:s');
            $batch = [];
            foreach ($attendanceData as $row) {
                $batch[] = [
                    'kegiatan_id' => $kegiatanId,
                    'member_id'   => (int) $row['member_id'],
                    'status'      => $row['status'] ?? 'hadir',
                    'catatan'     => $row['catatan'] ?? null,
                    'recorded_by' => $recordedBy,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
            $this->db->table('kegiatan_absensi')->insertBatch($batch);
        }

        // Update jumlah_peserta on kegiatan_list
        $hadirCount = count(array_filter($attendanceData, fn($r) => ($r['status'] ?? 'hadir') === 'hadir'));
        $this->db->table('kegiatan_list')
            ->where('id', $kegiatanId)
            ->update(['jumlah_peserta' => $hadirCount]);

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Count attendance stats for a kegiatan.
     */
    public function getStats(int $kegiatanId): array
    {
        $rows = $this->db->table('kegiatan_absensi')
            ->select('status, COUNT(*) as total')
            ->where('kegiatan_id', $kegiatanId)
            ->groupBy('status')
            ->get()->getResultArray();

        $stats = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0, 'total' => 0];
        foreach ($rows as $r) {
            $stats[$r['status']] = (int) $r['total'];
            $stats['total'] += (int) $r['total'];
        }
        return $stats;
    }
}
