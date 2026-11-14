<?php
/**
 * Category Model
 * Project: Teknisi Rumah Tangga
 * Handles CRUD and Queries for Service Categories
 */

class CategoryModel extends Model {
    private $table = 'categories';

    /**
     * Fallback MVP categories if database is not yet migrated/connected
     */
    private $fallbackCategories = [
        [
            'id' => 1,
            'slug' => 'teknisi-listrik',
            'name' => 'Teknisi Listrik',
            'description' => 'Perbaikan korsleting, instalasi panel listrik, stop kontak, lampu rumah, dan cek daya.',
            'status' => 'active',
            'icon' => 'bi-lightning-charge-fill',
            'badge' => 'Paling Dicari',
            'rating' => 4.9,
            'technicians_count' => '45+ Mitra',
            'created_at' => '2026-09-01 09:00:00'
        ],
        [
            'id' => 2,
            'slug' => 'teknisi-ac',
            'name' => 'Teknisi AC',
            'description' => 'Service berkala AC, cuci AC, isi ulang freon, perbaikan AC bocor, dan pasang unit baru.',
            'status' => 'active',
            'icon' => 'bi-snow',
            'badge' => 'Garansi Dingin',
            'rating' => 4.8,
            'technicians_count' => '60+ Mitra',
            'created_at' => '2026-09-01 09:00:00'
        ],
        [
            'id' => 3,
            'slug' => 'tukang-kunci',
            'name' => 'Tukang Kunci',
            'description' => 'Duplikat kunci, buka pintu macet/terkunci darurat 24 jam, ganti master lock, dan smart door lock.',
            'status' => 'active',
            'icon' => 'bi-key-fill',
            'badge' => 'Darurat 24 Jam',
            'rating' => 4.9,
            'technicians_count' => '30+ Mitra',
            'created_at' => '2026-09-01 09:00:00'
        ],
        [
            'id' => 4,
            'slug' => 'teknisi-elektronik',
            'name' => 'Teknisi Elektronik',
            'description' => 'Reparasi TV, kulkas, mesin cuci, microwave, pompa air, dan peralatan elektronik rumah tangga.',
            'status' => 'active',
            'icon' => 'bi-tv-fill',
            'badge' => 'Ahli & Cepat',
            'rating' => 4.7,
            'technicians_count' => '50+ Mitra',
            'created_at' => '2026-09-01 09:00:00'
        ]
    ];

    /**
     * Get all categories (optionally filter by active status)
     *
     * @param bool $onlyActive
     * @return array
     */
    public function getAllCategories($onlyActive = false) {
        if ($this->isDbConnected()) {
            $sql = "SELECT * FROM {$this->table}";
            if ($onlyActive) {
                $sql .= " WHERE status = 'active'";
            }
            $sql .= " ORDER BY id ASC";

            $this->db->query($sql);
            $results = $this->db->resultSet();
            if (!empty($results)) {
                return $results;
            }
        }

        // Mock fallback
        $sessionCategories = $_SESSION['_mock_categories'] ?? $this->fallbackCategories;
        if ($onlyActive) {
            $sessionCategories = array_filter($sessionCategories, function($c) {
                return ($c['status'] ?? 'active') === 'active';
            });
        }

        return array_map(function($item) {
            return (object) $item;
        }, array_values($sessionCategories));
    }

    /**
     * Get active categories only (convenience wrapper)
     *
     * @return array
     */
    public function getActiveCategories() {
        return $this->getAllCategories(true);
    }

    /**
     * Get single category by ID
     *
     * @param int $id
     * @return object|null
     */
    public function getCategoryById($id) {
        $id = (int)$id;

        if ($this->isDbConnected()) {
            $this->db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
            $this->db->bind(':id', $id);
            $result = $this->db->single();
            if ($result) {
                return $result;
            }
        }

        $sessionCategories = $_SESSION['_mock_categories'] ?? $this->fallbackCategories;
        foreach ($sessionCategories as $cat) {
            if ((int)$cat['id'] === $id) {
                return (object) $cat;
            }
        }

        return null;
    }

    /**
     * Get category by slug
     *
     * @param string $slug
     * @return object|null
     */
    public function getCategoryBySlug($slug) {
        if ($this->isDbConnected()) {
            $this->db->query("SELECT * FROM {$this->table} WHERE slug = :slug LIMIT 1");
            $this->db->bind(':slug', $slug);
            $result = $this->db->single();
            if ($result) {
                return $result;
            }
        }

        $sessionCategories = $_SESSION['_mock_categories'] ?? $this->fallbackCategories;
        foreach ($sessionCategories as $cat) {
            if ($cat['slug'] === $slug) {
                return (object) $cat;
            }
        }

        return null;
    }

    /**
     * Create new category
     *
     * @param array $data (name, description, status, icon, badge)
     * @return int|bool
     */
    public function createCategory($data) {
        $name = trim($data['name']);
        $description = trim($data['description']);
        $status = in_array($data['status'], ['active', 'inactive'], true) ? $data['status'] : 'active';
        $icon = !empty($data['icon']) ? trim($data['icon']) : 'bi-tools';
        $badge = !empty($data['badge']) ? trim($data['badge']) : 'Layanan Baru';
        
        // Generate unique slug
        $baseSlug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
        $baseSlug = trim($baseSlug, '-');
        $slug = $baseSlug ?: 'kategori-' . time();

        if ($this->isDbConnected()) {
            $sql = "INSERT INTO {$this->table} (name, slug, description, status, icon, badge) 
                    VALUES (:name, :slug, :description, :status, :icon, :badge)";
            $this->db->query($sql);
            $this->db->bind(':name', $name);
            $this->db->bind(':slug', $slug);
            $this->db->bind(':description', $description);
            $this->db->bind(':status', $status);
            $this->db->bind(':icon', $icon);
            $this->db->bind(':badge', $badge);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        }

        if (!isset($_SESSION['_mock_categories'])) {
            $_SESSION['_mock_categories'] = $this->fallbackCategories;
        }

        $newId = count($_SESSION['_mock_categories']) + 1;
        $newCategory = [
            'id' => $newId,
            'slug' => $slug,
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'icon' => $icon,
            'badge' => $badge,
            'rating' => 5.0,
            'technicians_count' => '0 Mitra',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $_SESSION['_mock_categories'][] = $newCategory;
        return $newId;
    }

    /**
     * Update existing category
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCategory($id, $data) {
        $id = (int)$id;
        $name = trim($data['name']);
        $description = trim($data['description']);
        $status = in_array($data['status'], ['active', 'inactive'], true) ? $data['status'] : 'active';
        $icon = !empty($data['icon']) ? trim($data['icon']) : 'bi-tools';
        $badge = !empty($data['badge']) ? trim($data['badge']) : 'Populer';

        if ($this->isDbConnected()) {
            $sql = "UPDATE {$this->table} 
                    SET name = :name, description = :description, status = :status, icon = :icon, badge = :badge 
                    WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':name', $name);
            $this->db->bind(':description', $description);
            $this->db->bind(':status', $status);
            $this->db->bind(':icon', $icon);
            $this->db->bind(':badge', $badge);
            $this->db->bind(':id', $id);

            return $this->db->execute();
        }

        if (!isset($_SESSION['_mock_categories'])) {
            $_SESSION['_mock_categories'] = $this->fallbackCategories;
        }

        foreach ($_SESSION['_mock_categories'] as &$cat) {
            if ((int)$cat['id'] === $id) {
                $cat['name'] = $name;
                $cat['description'] = $description;
                $cat['status'] = $status;
                $cat['icon'] = $icon;
                $cat['badge'] = $badge;
                return true;
            }
        }

        return false;
    }

    /**
     * Toggle category status between active and inactive
     *
     * @param int $id
     * @return bool
     */
    public function toggleStatus($id) {
        $category = $this->getCategoryById($id);
        if (!$category) {
            return false;
        }

        $newStatus = ($category->status === 'active') ? 'inactive' : 'active';

        if ($this->isDbConnected()) {
            $this->db->query("UPDATE {$this->table} SET status = :status WHERE id = :id");
            $this->db->bind(':status', $newStatus);
            $this->db->bind(':id', $id);
            return $this->db->execute();
        }

        if (!isset($_SESSION['_mock_categories'])) {
            $_SESSION['_mock_categories'] = $this->fallbackCategories;
        }

        foreach ($_SESSION['_mock_categories'] as &$cat) {
            if ((int)$cat['id'] === (int)$id) {
                $cat['status'] = $newStatus;
                return true;
            }
        }

        return false;
    }

    /**
     * Delete a category
     *
     * @param int $id
     * @return bool
     */
    public function deleteCategory($id) {
        $id = (int)$id;

        if ($this->isDbConnected()) {
            $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        }

        if (!isset($_SESSION['_mock_categories'])) {
            $_SESSION['_mock_categories'] = $this->fallbackCategories;
        }

        $_SESSION['_mock_categories'] = array_filter($_SESSION['_mock_categories'], function($cat) use ($id) {
            return (int)$cat['id'] !== $id;
        });

        return true;
    }
}
