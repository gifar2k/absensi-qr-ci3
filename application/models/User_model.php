<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    private $table = 'users';

    public function get_by_email(string $email): ?array {
        $row = $this->db->get_where($this->table, [
            'email' => strtolower(trim($email)),
            'is_active' => 1
        ])->row_array();

        return $row ?: null;
    }

    public function get_active_pegawai(): array {
        return $this->db
            ->where('role', 'pegawai')
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get($this->table)
            ->result_array();
    }

    public function get_staff_rows(): array {
        return $this->db
            ->select('id,name,email,role,is_active,created_at,updated_at')
            ->from($this->table)
            ->where_not_in('role', ['admin','superadmin'])
            ->order_by('is_active', 'DESC')
            ->order_by('name', 'ASC')
            ->get()->result_array();
    }

    public function get_active_device_map(): array {
        $rows = $this->db
            ->select('id,user_id,device_hash,device_label,last_seen_at,is_active,reset_by_admin_at,reset_by_admin_id,created_at')
            ->from('user_devices')
            ->where('is_active', 1)
            ->get()->result_array();

        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['user_id']] = $r;
        }
        return $map;
    }

    public function toggle_active(int $id): bool {
        $row = $this->db->select('is_active')->from($this->table)->where('id', $id)->get()->row_array();
        if (!$row) return false;

        $new = ((int)$row['is_active'] === 1) ? 0 : 1;
        $this->db->set('is_active', $new);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        return (bool)$this->db->where('id', $id)->update($this->table);
    }

    public function reset_device_soft(int $user_id, int $admin_id): bool {
        $this->db->set('is_active', 0);
        $this->db->set('reset_by_admin_at', date('Y-m-d H:i:s'));
        $this->db->set('reset_by_admin_id', $admin_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('is_active', 1);
        $this->db->limit(1);
        $this->db->update('user_devices');

        return $this->db->affected_rows() > 0;
    }

    public function create_staff(string $name, string $email, int $is_active = 1): array {
        $email = strtolower(trim($email));
        $name  = trim($name);

        if ($name === '' || $email === '') {
            return ['ok' => false, 'msg' => 'Nama dan email wajib diisi.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'msg' => 'Format email tidak valid.'];
        }

        $exists = $this->db->select('id')->from($this->table)->where('email', $email)->limit(1)->get()->row_array();
        if ($exists) {
            return ['ok' => false, 'msg' => 'Email sudah terdaftar.'];
        }

        $data = [
            'name'          => $name,
            'email'         => $email,
            'password_hash' => null,
            'role'          => 'pegawai',           // ✅ konsisten
            'is_active'     => $is_active ? 1 : 0,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() > 0) {
            return ['ok' => true, 'msg' => 'Pegawai berhasil ditambahkan.'];
        }
        return ['ok' => false, 'msg' => 'Gagal menambahkan pegawai.'];
    }

    // ✅ INI yang tadi error karena belum ada
    public function update_staff(int $id, string $name, string $email, int $is_active): array {
        $name  = trim($name);
        $email = strtolower(trim($email));

        $row = $this->db->select('id,role')
            ->from($this->table)
            ->where('id', $id)
            ->limit(1)->get()->row_array();

        if (!$row) {
            return ['ok' => false, 'msg' => 'User tidak ditemukan.'];
        }

        if (in_array((string)$row['role'], ['admin','superadmin'], true)) {
            return ['ok' => false, 'msg' => 'Tidak boleh edit akun admin dari menu pegawai.'];
        }

        $exists = $this->db->select('id')
            ->from($this->table)
            ->where('email', $email)
            ->where('id !=', $id)
            ->limit(1)->get()->row_array();

        if ($exists) {
            return ['ok' => false, 'msg' => 'Email sudah dipakai user lain.'];
        }

        $this->db->where('id', $id)->update($this->table, [
            'name'       => $name,
            'email'      => $email,
            'is_active'  => (int)$is_active,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return ['ok' => true, 'msg' => 'User berhasil diupdate.'];
    }

    public function deactivate_staff(int $id): array {
        $row = $this->db->select('id,role')
            ->from($this->table)
            ->where('id', $id)
            ->limit(1)->get()->row_array();

        if (!$row) {
            return ['ok' => false, 'msg' => 'User tidak ditemukan.'];
        }

        if (in_array((string)$row['role'], ['admin','superadmin'], true)) {
            return ['ok' => false, 'msg' => 'Tidak boleh menonaktifkan admin dari menu pegawai.'];
        }

        $this->db->where('id', $id)->update($this->table, [
            'is_active'  => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return ['ok' => true, 'msg' => 'User dinonaktifkan.'];
    }

    public function list_staff_active(): array {
        return $this->db
            ->select('id,name,email')
            ->from($this->table)
            ->where('is_active', 1)
            ->where_not_in('role', ['admin','superadmin'])
            ->order_by('name', 'ASC')
            ->get()->result_array();
    }
}
