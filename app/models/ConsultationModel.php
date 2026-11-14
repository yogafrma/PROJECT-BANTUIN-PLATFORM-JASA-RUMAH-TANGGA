<?php
/**
 * Consultation Model
 * Project: Teknisi Rumah Tangga
 * Handles storing and retrieving AI Consultations
 */

class ConsultationModel extends Model {
    private $table = 'consultations';

    /**
     * Fallback mock consultations
     */
    private $fallbackConsultations = [
        [
            'id' => 1,
            'customer_id' => 2,
            'image' => null,
            'damage_detail' => 'AC mengeluarkan angin biasa dan terdengar suara desisan pelan di pipa tembaga luar.',
            'chronology' => 'Sejak 2 hari lalu setelah pemakaian seharian, suhu ruangan tidak turun dan pipa ada embun es.',
            'ai_result' => '{"possible_damage":"Kemungkinan kebocoran halus pada sambungan pipa refrigrant (freon) atau kondensor kotor tersumbat debu.","category_name":"Teknisi AC","category_slug":"teknisi-ac","estimated_min_price":85000,"estimated_max_price":250000,"explanation":"Berdasarkan informasi yang diberikan, gejala angin tidak dingin disertai desisan dan es di pipa, terdapat indikasi tekanan freon menurun akibat kebocoran pada flare nut sambungan atau evaporator unit indoor yang kotor. Disarankan dilakukan pemeriksaan kebocoran, pembersihan cuci steam, dan pengisian ulang freon sesuai kapasitas unit.","draft_post_title":"Perbaikan AC Tidak Dingin & Cek Kebocoran Freon","draft_post_description":"Membutuhkan teknisi AC untuk cek unit AC split 1 PK yang hanya keluar angin dan indikasi freon berkurang di area Jakarta Selatan."}',
            'estimated_min_price' => 85000,
            'estimated_max_price' => 250000,
            'created_at' => '2 hari yang lalu'
        ]
    ];

    /**
     * Create a new consultation record
     *
     * @param array $data
     * @return int|bool Inserted ID
     */
    public function createConsultation($data) {
        $customerId = (int) $data['customer_id'];
        $image = !empty($data['image']) ? $data['image'] : null;
        $damageDetail = trim($data['damage_detail']);
        $chronology = trim($data['chronology']);
        $aiResult = is_array($data['ai_result']) ? json_encode($data['ai_result'], JSON_UNESCAPED_UNICODE) : $data['ai_result'];
        $minPrice = (int) ($data['estimated_min_price'] ?? 0);
        $maxPrice = (int) ($data['estimated_max_price'] ?? 0);

        if ($this->isDbConnected()) {
            // Ensure customer exists in database
            $this->db->query("SELECT id FROM users WHERE id = :id LIMIT 1");
            $this->db->bind(':id', $customerId);
            if (!$this->db->single() && isset($_SESSION['user']) && (int)($_SESSION['user']['id'] ?? 0) === $customerId) {
                $u = (object)$_SESSION['user'];
                $insertUser = "INSERT INTO users (id, name, email, password, whatsapp, role, status) 
                               VALUES (:id, :name, :email, :pass, :wa, :role, :status)";
                $this->db->query($insertUser);
                $this->db->bind(':id', $customerId);
                $this->db->bind(':name', $u->name);
                $this->db->bind(':email', $u->email);
                $this->db->bind(':pass', password_hash('password123', PASSWORD_BCRYPT));
                $this->db->bind(':wa', $u->whatsapp ?? '081234567890');
                $this->db->bind(':role', $u->role ?? 'customer');
                $this->db->bind(':status', $u->status ?? 'active');
                $this->db->execute();
            }

            $sql = "INSERT INTO {$this->table} (customer_id, image, damage_detail, chronology, ai_result, estimated_min_price, estimated_max_price) 
                    VALUES (:customer_id, :image, :damage_detail, :chronology, :ai_result, :estimated_min_price, :estimated_max_price)";
            $this->db->query($sql);
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':image', $image);
            $this->db->bind(':damage_detail', $damageDetail);
            $this->db->bind(':chronology', $chronology);
            $this->db->bind(':ai_result', $aiResult);
            $this->db->bind(':estimated_min_price', $minPrice);
            $this->db->bind(':estimated_max_price', $maxPrice);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        }

        if (!isset($_SESSION['_mock_consultations'])) {
            $_SESSION['_mock_consultations'] = $this->fallbackConsultations;
        }

        $newId = count($_SESSION['_mock_consultations']) + 1;
        $newRecord = [
            'id' => $newId,
            'customer_id' => $customerId,
            'image' => $image,
            'damage_detail' => $damageDetail,
            'chronology' => $chronology,
            'ai_result' => $aiResult,
            'estimated_min_price' => $minPrice,
            'estimated_max_price' => $maxPrice,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $_SESSION['_mock_consultations'][] = $newRecord;
        return $newId;
    }

    /**
     * Get all consultations by specific customer
     *
     * @param int $customerId
     * @return array
     */
    public function getConsultationsByCustomerId($customerId) {
        $customerId = (int) $customerId;

        if ($this->isDbConnected()) {
            $sql = "SELECT * FROM {$this->table} WHERE customer_id = :customer_id ORDER BY id DESC";
            $this->db->query($sql);
            $this->db->bind(':customer_id', $customerId);
            $results = $this->db->resultSet();
            if (!empty($results)) {
                return array_map(function($item) {
                    $item->ai_data = json_decode($item->ai_result, true);
                    return $item;
                }, $results);
            }
        }

        $sessionConsultations = $_SESSION['_mock_consultations'] ?? $this->fallbackConsultations;
        $filtered = array_filter($sessionConsultations, function($c) use ($customerId) {
            return (int)$c['customer_id'] === $customerId;
        });

        return array_map(function($item) {
            $obj = (object) $item;
            $obj->ai_data = is_string($obj->ai_result) ? json_decode($obj->ai_result, true) : (array)$obj->ai_result;
            return $obj;
        }, array_values($filtered));
    }

    /**
     * Get single consultation by ID
     *
     * @param int $id
     * @return object|null
     */
    public function getConsultationById($id) {
        $id = (int)$id;

        if ($this->isDbConnected()) {
            $this->db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
            $this->db->bind(':id', $id);
            $result = $this->db->single();
            if ($result) {
                $result->ai_data = json_decode($result->ai_result, true);
                return $result;
            }
        }

        $sessionConsultations = $_SESSION['_mock_consultations'] ?? $this->fallbackConsultations;
        foreach ($sessionConsultations as $c) {
            if ((int)$c['id'] === $id) {
                $obj = (object) $c;
                $obj->ai_data = is_string($obj->ai_result) ? json_decode($obj->ai_result, true) : (array)$obj->ai_result;
                return $obj;
            }
        }

        return null;
    }
}
