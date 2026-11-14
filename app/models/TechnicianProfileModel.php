<?php
/**
 * Technician Profile Model
 * Project: Teknisi Rumah Tangga
 */

class TechnicianProfileModel extends Model {
    private $table = 'technician_profiles';

    /**
     * Fallback mock technician profiles
     */
    private $fallbackProfiles = [
        3 => [
            'id' => 1,
            'user_id' => 3,
            'name' => 'Bambang Santoso',
            'email' => 'bambang.teknisi@gmail.com',
            'whatsapp' => '081288992211',
            'category_id' => 1,
            'category_name' => 'Teknisi Listrik',
            'category_icon' => 'bi-lightning-charge-fill',
            'description' => 'Spesialis perbaikan korsleting listrik MCB, instalasi panel baru, stop kontak, & grounding rumah aman.',
            'location' => 'Jakarta Selatan & Depok',
            'rating' => 4.95,
            'completed_jobs' => 164
        ],
        4 => [
            'id' => 2,
            'user_id' => 4,
            'name' => 'Hendra Pratama',
            'email' => 'hendra.ac@gmail.com',
            'whatsapp' => '081399887766',
            'category_id' => 2,
            'category_name' => 'Teknisi AC',
            'category_icon' => 'bi-snow',
            'description' => 'Ahli cuci AC split & inverter, pengisian freon R32/R410A, perbaikan kompresor berisik, & AC bocor netes.',
            'location' => 'Jakarta Barat & Tangerang',
            'rating' => 4.92,
            'completed_jobs' => 218
        ],
        5 => [
            'id' => 3,
            'user_id' => 5,
            'name' => 'Agus Setiawan',
            'email' => 'agus.kunci@gmail.com',
            'whatsapp' => '081577665544',
            'category_id' => 3,
            'category_name' => 'Tukang Kunci 24 Jam',
            'category_icon' => 'bi-key-fill',
            'description' => 'Layanan darurat kunci pintu macet, kunci tertinggal, duplikat master key, & pemasangan smart digital lock.',
            'location' => 'Jakarta Timur & Bekasi',
            'rating' => 4.98,
            'completed_jobs' => 189
        ],
        6 => [
            'id' => 4,
            'user_id' => 6,
            'name' => 'Rahmat Hidayat',
            'email' => 'rahmat.elektronik@gmail.com',
            'whatsapp' => '081911223344',
            'category_id' => 4,
            'category_name' => 'Teknisi Elektronik',
            'category_icon' => 'bi-tv-fill',
            'description' => 'Reparasi mesin cuci front/top load, kulkas inverter tidak beku, TV LED layar redup, & microwave.',
            'location' => 'Jakarta Pusat & sekitarnya',
            'rating' => 4.89,
            'completed_jobs' => 143
        ]
    ];

    /**
     * Get technician profile by User ID
     *
     * @param int $userId
     * @return object|null
     */
    public function getProfileByUserId($userId) {
        if ($this->isDbConnected()) {
            $sql = "SELECT tp.*, u.name, u.email, u.whatsapp, u.status, c.name as category_name, c.icon as category_icon, c.slug as category_slug
                    FROM {$this->table} tp
                    JOIN users u ON tp.user_id = u.id
                    LEFT JOIN categories c ON tp.category_id = c.id
                    WHERE tp.user_id = :user_id LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId);
            $profile = $this->db->single();
            if ($profile) {
                $profile->completed_jobs = $this->getCompletedJobsCount($userId);
                return $profile;
            }
        }

        // Search in fallback or session
        $sessionProfiles = $_SESSION['_mock_tech_profiles'] ?? [];
        if (isset($sessionProfiles[$userId])) {
            $prof = (object) $sessionProfiles[$userId];
            $prof->completed_jobs = $this->getCompletedJobsCount($userId);
            return $prof;
        }

        if (isset($this->fallbackProfiles[$userId])) {
            $prof = (object) $this->fallbackProfiles[$userId];
            $prof->completed_jobs = $this->getCompletedJobsCount($userId);
            return $prof;
        }

        // Default skeleton if not registered yet
        return (object) [
            'id' => null,
            'user_id' => $userId,
            'category_id' => 2,
            'category_name' => 'Teknisi AC',
            'category_icon' => 'bi-snow',
            'description' => 'Mitra teknisi profesional siap melayani perbaikan rumah.',
            'location' => 'Jabodetabek',
            'rating' => 5.00,
            'completed_jobs' => $this->getCompletedJobsCount($userId)
        ];
    }

    /**
     * Get count of completed jobs for technician
     *
     * @param int $userId
     * @return int
     */
    public function getCompletedJobsCount($userId) {
        if ($this->isDbConnected()) {
            $this->db->query("SELECT COUNT(*) as total FROM technician_responses tr 
                              JOIN posts p ON tr.post_id = p.id 
                              WHERE tr.technician_id = :technician_id AND tr.status = 'selected' AND p.status = 'Selesai'");
            $this->db->bind(':technician_id', (int)$userId);
            $res = $this->db->single();
            return $res ? (int)$res->total : 0;
        }
        return 0;
    }

    /**
     * Get list of recommended technicians (Real database data only, no dummy fallback)
     *
     * @param int $limit
     * @return array
     */
    public function getRecommendedTechnicians($limit = 4) {
        if ($this->isDbConnected()) {
            $sql = "SELECT u.id as user_id, u.name, u.email, u.whatsapp,
                           COALESCE(tp.id, 0) as id,
                           COALESCE(tp.category_id, 1) as category_id,
                           COALESCE(c.name, 'Teknisi Umum') as category_name,
                           COALESCE(c.icon, 'bi-tools') as category_icon,
                           COALESCE(tp.description, 'Mitra teknisi profesional terverifikasi BANTUIN.') as description,
                           COALESCE(tp.location, 'Jabodetabek') as location,
                           COALESCE(tp.rating, 5.0) as rating
                    FROM users u
                    LEFT JOIN {$this->table} tp ON u.id = tp.user_id
                    LEFT JOIN categories c ON tp.category_id = c.id
                    WHERE u.role = 'technician' AND u.status = 'active'
                    ORDER BY tp.rating DESC, u.id ASC
                    LIMIT :limit";
            $this->db->query($sql);
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $results = $this->db->resultSet();
            if (!empty($results)) {
                return $results;
            }
        }

        // Return strictly empty array when no real technicians exist in DB
        return [];
    }

    /**
     * Save or Update technician profile
     *
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function upsertProfile($userId, $data) {
        $categoryId = (int) $data['category_id'];
        $description = trim($data['description']);
        $location = trim($data['location']);

        if ($this->isDbConnected()) {
            $sql = "INSERT INTO {$this->table} (user_id, category_id, description, location)
                    VALUES (:user_id, :category_id, :description, :location)
                    ON DUPLICATE KEY UPDATE
                    category_id = VALUES(category_id),
                    description = VALUES(description),
                    location = VALUES(location)";
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':category_id', $categoryId);
            $this->db->bind(':description', $description);
            $this->db->bind(':location', $location);
            return $this->db->execute();
        }

        if (!isset($_SESSION['_mock_tech_profiles'])) {
            $_SESSION['_mock_tech_profiles'] = [];
        }

        $_SESSION['_mock_tech_profiles'][$userId] = [
            'id' => 99,
            'user_id' => $userId,
            'category_id' => $categoryId,
            'description' => $description,
            'location' => $location,
            'rating' => 5.00
        ];

        return true;
    }

    /**
     * Count total technicians
     *
     * @return int
     */
    public function getTechnicianCount() {
        if ($this->isDbConnected()) {
            $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'technician' AND status = 'active'");
            $res = $this->db->single();
            return $res ? (int)$res->total : 0;
        }

        return count($this->fallbackProfiles);
    }
}
