<?php
/**
 * User Model
 * Project: Teknisi Rumah Tangga
 */

class UserModel extends Model {
    private $table = 'users';

    /**
     * Fallback demo accounts if database is not yet migrated/connected
     */
    private $fallbackUsers = [
        [
            'id' => 1,
            'name' => 'Yoga Admin',
            'email' => 'yogaadmin@gmail.com',
            'password' => '$2y$10$bHhrZIXwDC6me0lh7Lzlk.H9tcLehbb2PP9o0rizffpy6q0cbcM4q', // password123
            'whatsapp' => '081234567890',
            'role' => 'admin',
            'status' => 'active',
            'created_at' => '2026-09-01 10:00:00'
        ]
    ];

    /**
     * Helper to check if a user ID was deleted in session
     */
    private function isUserDeleted($id) {
        $deleted = $_SESSION['_deleted_user_ids'] ?? [];
        return in_array((int)$id, $deleted, true);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return object|false
     */
    public function findByEmail($email) {
        $email = trim(strtolower($email));

        if ($this->isDbConnected()) {
            $this->db->query("SELECT * FROM {$this->table} WHERE LOWER(email) = :email LIMIT 1");
            $this->db->bind(':email', $email);
            $user = $this->db->single();
            return $user ?: false;
        }

        $sessionUsers = $_SESSION['_mock_users'] ?? [];
        $allUsers = array_merge($this->fallbackUsers, $sessionUsers);

        foreach ($allUsers as $u) {
            if ($this->isUserDeleted($u['id'])) {
                continue;
            }
            if (strtolower($u['email']) === $email) {
                return (object) $u;
            }
        }

        return false;
    }

    /**
     * Find user by ID
     *
     * @param int $id
     * @return object|false
     */
    public function findById($id) {
        if ($this->isDbConnected()) {
            $this->db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
            $this->db->bind(':id', (int)$id);
            $user = $this->db->single();
            return $user ?: false;
        }

        if ($this->isUserDeleted($id)) {
            return false;
        }

        $sessionUsers = $_SESSION['_mock_users'] ?? [];
        $allUsers = array_merge($this->fallbackUsers, $sessionUsers);

        foreach ($allUsers as $u) {
            if ($this->isUserDeleted($u['id'])) {
                continue;
            }
            if ((int)$u['id'] === (int)$id) {
                return (object) $u;
            }
        }

        return false;
    }

    /**
     * Check if email already registered
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function isEmailExists($email, $excludeId = null) {
        $email = trim(strtolower($email));

        if ($this->isDbConnected()) {
            $sql = "SELECT id FROM {$this->table} WHERE LOWER(email) = :email";
            if ($excludeId) {
                $sql .= " AND id != :excludeId";
            }
            $sql .= " LIMIT 1";

            $this->db->query($sql);
            $this->db->bind(':email', $email);
            if ($excludeId) {
                $this->db->bind(':excludeId', $excludeId);
            }
            $user = $this->db->single();
            return (bool) $user;
        }

        $sessionUsers = $_SESSION['_mock_users'] ?? [];
        $allUsers = array_merge($this->fallbackUsers, $sessionUsers);

        foreach ($allUsers as $u) {
            if (strtolower($u['email']) === $email && (!$excludeId || $u['id'] != $excludeId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register new user
     *
     * @param array $data
     * @return int|bool
     */
    public function register($data) {
        $name = trim($data['name']);
        $email = trim(strtolower($data['email']));
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $whatsapp = preg_replace('/[^0-9]/', '', $data['whatsapp']);
        $role = in_array($data['role'], ['admin', 'customer', 'technician'], true) ? $data['role'] : 'customer';
        $status = 'active';

        if ($this->isDbConnected()) {
            $sql = "INSERT INTO {$this->table} (name, email, password, whatsapp, role, status) 
                    VALUES (:name, :email, :password, :whatsapp, :role, :status)";
            
            $this->db->query($sql);
            $this->db->bind(':name', $name);
            $this->db->bind(':email', $email);
            $this->db->bind(':password', $password);
            $this->db->bind(':whatsapp', $whatsapp);
            $this->db->bind(':role', $role);
            $this->db->bind(':status', $status);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        }

        if (!isset($_SESSION['_mock_users'])) {
            $_SESSION['_mock_users'] = [];
        }

        $newId = count($this->fallbackUsers) + count($_SESSION['_mock_users']) + 1;
        $newUser = [
            'id' => $newId,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'whatsapp' => $whatsapp,
            'role' => $role,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $_SESSION['_mock_users'][] = $newUser;
        return $newId;
    }

    /**
     * Update user basic profile
     *
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateProfile($userId, $data) {
        $name = trim($data['name']);
        $whatsapp = preg_replace('/[^0-9]/', '', $data['whatsapp']);

        if ($this->isDbConnected()) {
            $sql = "UPDATE {$this->table} SET name = :name, whatsapp = :whatsapp WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':name', $name);
            $this->db->bind(':whatsapp', $whatsapp);
            $this->db->bind(':id', $userId);
            return $this->db->execute();
        }

        // Mock update
        if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $userId) {
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['whatsapp'] = $whatsapp;
        }

        return true;
    }

    /**
     * Get user counts grouped by role
     *
     * @return array
     */
    public function getUserCounts() {
        if ($this->isDbConnected()) {
            $this->db->query("SELECT 
                COUNT(CASE WHEN role = 'customer' THEN 1 END) as total_customers,
                COUNT(CASE WHEN role = 'technician' THEN 1 END) as total_technicians,
                COUNT(*) as total_users
                FROM {$this->table} WHERE status = 'active'");
            $res = $this->db->single();
            if ($res) {
                return [
                    'customers' => (int) $res->total_customers,
                    'technicians' => (int) $res->total_technicians,
                    'total' => (int) $res->total_users
                ];
            }
        }

        $sessionUsers = $_SESSION['_mock_users'] ?? [];
        $all = array_merge($this->fallbackUsers, $sessionUsers);
        $all = array_filter($all, function($u) {
            return !$this->isUserDeleted($u['id']);
        });

        $cust = 0; $tech = 0;
        foreach ($all as $u) {
            if ($u['role'] === 'customer') $cust++;
            if ($u['role'] === 'technician') $tech++;
        }

        return [
            'customers' => $cust,
            'technicians' => $tech,
            'total' => count($all)
        ];
    }

    /**
     * Get all users with optional role filtering
     *
     * @param string|null $role
     * @return array
     */
    public function getAllUsers($role = null) {
        if ($this->isDbConnected()) {
            $sql = "SELECT u.*, 
                    (SELECT COUNT(*) FROM posts p WHERE p.customer_id = u.id) as total_posts,
                    (SELECT COUNT(*) FROM technician_responses tr WHERE tr.technician_id = u.id) as total_responses
                    FROM {$this->table} u";
            if (!empty($role) && $role !== 'all') {
                $sql .= " WHERE u.role = :role";
            }
            $sql .= " ORDER BY u.id DESC";

            $this->db->query($sql);
            if (!empty($role) && $role !== 'all') {
                $this->db->bind(':role', $role);
            }
            $results = $this->db->resultSet();
            return is_array($results) ? $results : [];
        }

        $sessionUsers = $_SESSION['_mock_users'] ?? [];
        $all = array_merge($this->fallbackUsers, $sessionUsers);
        $all = array_filter($all, function($u) {
            return !$this->isUserDeleted($u['id']);
        });

        if (!empty($role) && $role !== 'all') {
            $all = array_filter($all, function($u) use ($role) {
                return $u['role'] === $role;
            });
        }

        return array_map(function($u) {
            $item = (object) $u;
            $item->total_posts = ($u['role'] === 'customer') ? 2 : 0;
            $item->total_responses = ($u['role'] === 'technician') ? 3 : 0;
            return $item;
        }, array_values($all));
    }

    /**
     * Toggle User status between 'active' and 'inactive'
     *
     * @param int $userId
     * @return bool
     */
    public function toggleUserStatus($userId) {
        $userId = (int) $userId;
        $user = $this->findById($userId);
        if (!$user) {
            return false;
        }

        $newStatus = ($user->status === 'active') ? 'inactive' : 'active';

        if ($this->isDbConnected()) {
            $this->db->query("UPDATE {$this->table} SET status = :status WHERE id = :id");
            $this->db->bind(':status', $newStatus);
            $this->db->bind(':id', $userId);
            return $this->db->execute();
        }

        // Mock update
        if (isset($_SESSION['_mock_users'])) {
            foreach ($_SESSION['_mock_users'] as &$u) {
                if ($u['id'] == $userId) {
                    $u['status'] = $newStatus;
                }
            }
        }
        foreach ($this->fallbackUsers as &$u) {
            if ($u['id'] == $userId) {
                $u['status'] = $newStatus;
            }
        }

        return true;
    }

    /**
     * Get Customers list with their post counts
     *
     * @return array
     */
    public function getCustomersList() {
        if ($this->isDbConnected()) {
            $sql = "SELECT u.*, 
                    (SELECT COUNT(*) FROM posts p WHERE p.customer_id = u.id) as total_posts,
                    (SELECT COUNT(*) FROM consultations c WHERE c.customer_id = u.id) as total_consultations
                    FROM {$this->table} u
                    WHERE u.role = 'customer'
                    ORDER BY u.id DESC";
            $this->db->query($sql);
            $results = $this->db->resultSet();
            return is_array($results) ? $results : [];
        }

        $all = $this->getAllUsers('customer');
        foreach ($all as &$c) {
            $c->total_consultations = 1;
        }
        return $all;
    }

    /**
     * Get Technicians list with their categories, rating, and status
     *
     * @return array
     */
    public function getTechniciansListWithSkills() {
        if ($this->isDbConnected()) {
            $sql = "SELECT u.*, tp.rating, tp.completed_jobs, tp.location, tp.description as bio,
                    c.name as category_name, c.icon as category_icon,
                    (SELECT COUNT(*) FROM technician_responses tr WHERE tr.technician_id = u.id) as total_offers,
                    (SELECT COUNT(*) FROM technician_responses tr WHERE tr.technician_id = u.id AND tr.status = 'selected') as selected_offers
                    FROM {$this->table} u
                    LEFT JOIN technician_profiles tp ON u.id = tp.user_id
                    LEFT JOIN categories c ON tp.category_id = c.id
                    WHERE u.role = 'technician'
                    ORDER BY u.id DESC";
            $this->db->query($sql);
            $results = $this->db->resultSet();
            return is_array($results) ? $results : [];
        }

        $techs = $this->getAllUsers('technician');
        $catNames = [3 => 'Teknisi AC', 4 => 'Teknisi Listrik', 5 => 'Tukang Kunci'];
        $ratings = [3 => 4.90, 4 => 4.85, 5 => 4.95];
        $locations = [3 => 'Jakarta Selatan & Sekitarnya', 4 => 'Jakarta Barat & Tangerang', 5 => 'Jakarta Timur & Bekasi'];

        foreach ($techs as &$t) {
            $t->category_name = $catNames[$t->id] ?? 'Teknisi Umum';
            $t->category_icon = 'bi-tools';
            $t->rating = $ratings[$t->id] ?? 5.0;
            $t->location = $locations[$t->id] ?? 'Jabodetabek';
            $t->completed_jobs = 24;
            $t->total_offers = 4;
            $t->selected_offers = 2;
            $t->bio = 'Mitra teknisi berpengalaman siap melayani kebutuhan perbaikan rumah tangga.';
        }

        return $techs;
    }

    /**
     * Delete user permanently by ID
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUser($userId) {
        $userId = (int)$userId;
        if (!$userId) {
            return false;
        }

        if ($this->isDbConnected()) {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $userId);
            $deleted = $this->db->execute();
            if (isset($_SESSION['_deleted_user_ids'])) {
                $_SESSION['_deleted_user_ids'] = array_values(array_diff($_SESSION['_deleted_user_ids'], [$userId]));
            }
            return $deleted;
        }

        if (!isset($_SESSION['_deleted_user_ids'])) {
            $_SESSION['_deleted_user_ids'] = [];
        }
        if (!in_array($userId, $_SESSION['_deleted_user_ids'], true)) {
            $_SESSION['_deleted_user_ids'][] = $userId;
        }

        // Mock session fallback
        if (isset($_SESSION['_mock_users'])) {
            foreach ($_SESSION['_mock_users'] as $k => $u) {
                if ($u['id'] == $userId) {
                    unset($_SESSION['_mock_users'][$k]);
                }
            }
            $_SESSION['_mock_users'] = array_values($_SESSION['_mock_users']);
        }
        return true;
    }
}

