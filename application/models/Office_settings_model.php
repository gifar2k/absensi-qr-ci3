<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Office_settings_model extends CI_Model {

    private $table = 'office_settings';

    public function get(): array {
        $row = $this->db->get_where($this->table, ['id' => 1])->row_array();
        return $row ?: [];
    }

    public function update_settings(array $data): bool {
        $this->db->where('id', 1);
        return $this->db->update($this->table, $data);
    }

    public function get_workday_start(): string {
        $row = $this->get();
        return $row['workday_start'] ?? '06:00:00';
    }

    public function regenerate_secret(int $bytes = 32): string {
        return bin2hex(random_bytes($bytes));
    }

    public function get_qr_refresh_seconds(): int {
        $row = $this->get();
        return (int)($row['qr_refresh_seconds'] ?? 15);
    }

    public function get_token_window_seconds(): int {
    $row = $this->get();
    return (int)($row['token_window_seconds'] ?? 90);
}

}
