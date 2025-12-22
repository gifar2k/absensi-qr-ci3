<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Device_model extends CI_Model {

    private $table = 'user_devices';

   public function get_active_device(int $user_id): ?array
{
    $row = $this->db
        ->from($this->table)
        ->where('user_id', $user_id)
        ->where('is_active', 1)
        ->order_by('id', 'DESC')
        ->limit(1)
        ->get()
        ->row_array();

    return $row ?: null;
}


    public function register_or_touch(int $user_id, string $device_hash, ?string $label = null): array {
        // if active device exists
        $active = $this->get_active_device($user_id);

        // No active device => register this device as active
        if (!$active) {
            $this->db->insert($this->table, [
                'user_id' => $user_id,
                'device_hash' => $device_hash,
                'device_label' => $label,
                'last_seen_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ]);
            return ['ok' => true, 'mode' => 'registered'];
        }

        // Active exists but different device => locked
        if (!hash_equals($active['device_hash'], $device_hash)) {
            return ['ok' => false, 'mode' => 'locked', 'active_device' => $active];
        }

        // Same device => touch last_seen
        $this->db->where('id', $active['id'])->update($this->table, [
            'last_seen_at' => date('Y-m-d H:i:s'),
            'device_label' => $label ?: $active['device_label']
        ]);

        return ['ok' => true, 'mode' => 'touched'];
    }

    /**
     * Admin reset: deactivate all devices for user (or just active).
     */
    public function admin_reset(int $user_id, int $admin_id): bool {
        $this->db->where('user_id', $user_id)->where('is_active', 1);
        return $this->db->update($this->table, [
            'is_active' => 0,
            'reset_by_admin_at' => date('Y-m-d H:i:s'),
            'reset_by_admin_id' => $admin_id
        ]);
    }
}
