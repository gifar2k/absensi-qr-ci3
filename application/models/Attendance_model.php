<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_model extends CI_Model {

    private $table = 'attendance_logs';

    public function get_today_logs(string $workday_date): array {
        return $this->db
            ->select('attendance_logs.*, users.name, users.email')
            ->from($this->table)
            ->join('users', 'users.id = attendance_logs.user_id', 'left')
            ->where('attendance_logs.workday_date', $workday_date)
            ->order_by('attendance_logs.taken_at', 'ASC')
            ->get()->result_array();
    }

    public function get_last_status(int $user_id, string $workday_date): ?string {
        $row = $this->db
            ->select('status')
            ->where('user_id', $user_id)
            ->where('workday_date', $workday_date)
            ->order_by('taken_at', 'DESC')
            ->limit(1)
            ->get($this->table)->row_array();

        return $row['status'] ?? null;
    }

    public function has_in(int $user_id, string $workday_date): bool {
        return $this->db->where([
            'user_id' => $user_id,
            'workday_date' => $workday_date,
            'status' => 'IN'
        ])->count_all_results($this->table) > 0;
    }

    public function has_out(int $user_id, string $workday_date): bool {
        return $this->db->where([
            'user_id' => $user_id,
            'workday_date' => $workday_date,
            'status' => 'OUT'
        ])->count_all_results($this->table) > 0;
    }

    /**
     * Insert attendance record (real-time taken_at by DB default OR set manually).
     */
    public function insert_log(array $data): bool {
        // recommended minimal keys:
        // workday_date, user_id, status, lat,lng,accuracy_m,distance_m,source,note
        return $this->db->insert($this->table, $data);
    }

  public function get_monitor_rows($workday)
{
    $sql = "
    SELECT 
      u.id AS user_id,
      u.name,
      u.email,

      MAX(CASE WHEN a.status='IN'  THEN a.taken_at END) AS jam_masuk_dt,
      MAX(CASE WHEN a.status='OUT' THEN a.taken_at END) AS jam_pulang_dt,

      MAX(a.taken_at) AS last_activity
    FROM users u
    LEFT JOIN attendance_logs a
      ON a.user_id = u.id AND a.workday_date = ?
    WHERE u.is_active = 1
      AND u.role NOT IN ('admin','superadmin')
    GROUP BY u.id, u.name, u.email
    ORDER BY
      (MAX(a.taken_at) IS NULL) ASC,   -- yang belum ada aktivitas taruh bawah
      MAX(a.taken_at) DESC,            -- yang paling baru di atas
      u.name ASC
    ";

    $q = $this->db->query($sql, [$workday]);
    $rows = $q->result_array();

    foreach ($rows as &$r) {
        $in  = $r['jam_masuk_dt'] ?? null;
        $out = $r['jam_pulang_dt'] ?? null;

        if ($in && !$out) $r['status'] = 'BELUM PULANG';
        elseif ($in && $out) $r['status'] = 'PULANG';
        else $r['status'] = 'BELUM MASUK';

        // tampil jam saja
        $r['jam_masuk']  = $in  ? date('H:i:s', strtotime($in))  : null;
        $r['jam_pulang'] = $out ? date('H:i:s', strtotime($out)) : null;

        // optional: kalau mau dipakai front-end
        $r['last_activity'] = $r['last_activity'] ? date('H:i:s', strtotime($r['last_activity'])) : null;

        // buang kolom dt biar rapih
        unset($r['jam_masuk_dt'], $r['jam_pulang_dt']);
    }

    return $rows;
}

public function get_logs(array $f = []): array
{
    $date_from = trim((string)($f['date_from'] ?? ''));
    $date_to   = trim((string)($f['date_to'] ?? ''));
    $user_id   = (int)($f['user_id'] ?? 0);
    $status    = strtoupper(trim((string)($f['status'] ?? ''))); // IN/OUT
    $q         = trim((string)($f['q'] ?? ''));
    $limit     = (int)($f['limit'] ?? 200);
    $offset    = (int)($f['offset'] ?? 0);

    $this->db->select('l.*, u.name, u.email, u.role');
    $this->db->from('attendance_logs l');
    $this->db->join('users u', 'u.id = l.user_id', 'left');

    // default: exclude admin roles dari logs list (biar rapih)
    $this->db->where('u.is_active', 1);
    $this->db->where_not_in('u.role', ['admin','superadmin']);

    if ($date_from !== '') $this->db->where('l.workday_date >=', $date_from);
    if ($date_to !== '')   $this->db->where('l.workday_date <=', $date_to);
    if ($user_id > 0)      $this->db->where('l.user_id', $user_id);
    if (in_array($status, ['IN','OUT'], true)) $this->db->where('l.status', $status);

    if ($q !== '') {
        $this->db->group_start();
        $this->db->like('u.name', $q);
        $this->db->or_like('u.email', $q);
        $this->db->group_end();
    }

    $this->db->order_by('l.taken_at', 'DESC');
    $this->db->limit($limit, $offset);

    return $this->db->get()->result_array();
}

public function count_logs(array $f = []): int
{
    $date_from = trim((string)($f['date_from'] ?? ''));
    $date_to   = trim((string)($f['date_to'] ?? ''));
    $user_id   = (int)($f['user_id'] ?? 0);
    $status    = strtoupper(trim((string)($f['status'] ?? '')));
    $q         = trim((string)($f['q'] ?? ''));

    $this->db->from('attendance_logs l');
    $this->db->join('users u', 'u.id = l.user_id', 'left');
    $this->db->where('u.is_active', 1);
    $this->db->where_not_in('u.role', ['admin','superadmin']);

    if ($date_from !== '') $this->db->where('l.workday_date >=', $date_from);
    if ($date_to !== '')   $this->db->where('l.workday_date <=', $date_to);
    if ($user_id > 0)      $this->db->where('l.user_id', $user_id);
    if (in_array($status, ['IN','OUT'], true)) $this->db->where('l.status', $status);

    if ($q !== '') {
        $this->db->group_start();
        $this->db->like('u.name', $q);
        $this->db->or_like('u.email', $q);
        $this->db->group_end();
    }

    return (int)$this->db->count_all_results();
}

public function rekap_harian(string $date_from, string $date_to): array
{
    // 1) total IN/OUT per workday
    $sql = "
        SELECT 
            l.workday_date,
            SUM(CASE WHEN l.status='IN'  THEN 1 ELSE 0 END) AS total_in,
            SUM(CASE WHEN l.status='OUT' THEN 1 ELSE 0 END) AS total_out
        FROM attendance_logs l
        INNER JOIN users u ON u.id = l.user_id
        WHERE u.is_active = 1
          AND u.role NOT IN ('admin','superadmin')
          AND l.workday_date BETWEEN ? AND ?
        GROUP BY l.workday_date
        ORDER BY l.workday_date DESC
    ";
    $rows = $this->db->query($sql, [$date_from, $date_to])->result_array();

    // 2) total pegawai aktif (buat hitung belum masuk/belum pulang)
    $total_staff = (int)$this->db
        ->from('users')
        ->where('is_active', 1)
        ->where_not_in('role', ['admin','superadmin'])
        ->count_all_results();

    // 3) enrich: belum masuk & belum pulang
    foreach ($rows as &$r) {
        $in  = (int)$r['total_in'];
        $out = (int)$r['total_out'];

        // asumsi: 1 user max 1 IN & 1 OUT per workday (udah kamu enforce)
        $r['total_staff'] = $total_staff;
        $r['belum_masuk'] = max(0, $total_staff - $in);
        $r['belum_pulang'] = max(0, $in - $out);
    }
    unset($r);

    return $rows;
}

public function rekap_bulanan(string $month): array
{
    // month format: YYYY-MM
    $start = $month . '-01';
    $end = date('Y-m-t', strtotime($start));

    // DETAIL per user per workday_date + jam masuk/pulang
    $sql = "
        SELECT
            u.id,
            u.name,
            u.email,
            l.workday_date,

            MIN(CASE WHEN l.status='IN'  THEN l.taken_at END) AS jam_masuk_dt,
            MAX(CASE WHEN l.status='OUT' THEN l.taken_at END) AS jam_pulang_dt,

            SUM(CASE WHEN l.status='IN'  THEN 1 ELSE 0 END) AS total_in,
            SUM(CASE WHEN l.status='OUT' THEN 1 ELSE 0 END) AS total_out
        FROM users u
        LEFT JOIN attendance_logs l
          ON l.user_id = u.id
         AND l.workday_date BETWEEN ? AND ?
        WHERE u.is_active = 1
          AND u.role NOT IN ('admin','superadmin')
        GROUP BY u.id, u.name, u.email, l.workday_date
        HAVING l.workday_date IS NOT NULL
        ORDER BY l.workday_date ASC, u.name ASC
    ";

    $rows = $this->db->query($sql, [$start, $end])->result_array();

    // rapihin output + status
    foreach ($rows as &$r) {
        $in  = $r['jam_masuk_dt'] ?? null;
        $out = $r['jam_pulang_dt'] ?? null;

        $r['jam_masuk']  = $in  ? date('H:i:s', strtotime($in))  : null;
        $r['jam_pulang'] = $out ? date('H:i:s', strtotime($out)) : null;

        if ($in && !$out) $r['status_hari'] = 'BELUM PULANG';
        elseif ($in && $out) $r['status_hari'] = 'PULANG';
        else $r['status_hari'] = 'BELUM MASUK';

        unset($r['jam_masuk_dt'], $r['jam_pulang_dt']);
    }
    unset($r);

    // SUMMARY per user (optional cards)
    $summary = [];
    foreach ($rows as $r) {
        $uid = (int)$r['id'];
        if (!isset($summary[$uid])) {
            $summary[$uid] = [
                'id' => $uid,
                'name' => $r['name'],
                'email' => $r['email'],
                'hadir_hari' => 0,
                'pulang_hari' => 0,
                'belum_pulang_hari' => 0,
            ];
        }
        // 1 IN per hari (karena kamu enforce)
        if (!empty($r['jam_masuk'])) $summary[$uid]['hadir_hari']++;
        if (!empty($r['jam_pulang'])) $summary[$uid]['pulang_hari']++;
    }
    foreach ($summary as &$s) {
        $s['belum_pulang_hari'] = max(0, $s['hadir_hari'] - $s['pulang_hari']);
    }
    unset($s);

    return [
        'month' => $month,
        'date_from' => $start,
        'date_to' => $end,
        'rows' => $rows,                 // detail per hari
        'summary' => array_values($summary) // ringkas per user (opsional)
    ];
}





}
