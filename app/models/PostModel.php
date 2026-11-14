<?php
/**
 * Post & Technician Response Model
 * Project: Teknisi Rumah Tangga
 * Handles Customer Complaint Posts & Technician Responses
 */

class PostModel extends Model {
    private $table = 'posts';
    private $responsesTable = 'technician_responses';

    /**
     * Fallback mock posts (cleared)
     */
    private $fallbackPosts = [];

    /**
     * Fallback mock technician responses (cleared)
     */
    private $fallbackResponses = [];

    /**
     * Get posts created by specific customer
     *
     * @param int $customerId
     * @return array
     */
    public function getPostsByCustomerId($customerId) {
        $customerId = (int) $customerId;

        if ($this->isDbConnected()) {
            $sql = "SELECT p.*, c.name as category_name, c.icon as category_icon, u.name as customer_name, u.whatsapp as customer_whatsapp,
                    (SELECT COUNT(*) FROM {$this->responsesTable} tr WHERE tr.post_id = p.id) as offers_count,
                    (SELECT COUNT(*) FROM {$this->responsesTable} tr WHERE tr.post_id = p.id AND tr.status = 'selected') as selected_count
                    FROM {$this->table} p
                    JOIN categories c ON p.category_id = c.id
                    JOIN users u ON p.customer_id = u.id
                    WHERE p.customer_id = :customer_id
                    ORDER BY p.id DESC";
            $this->db->query($sql);
            $this->db->bind(':customer_id', $customerId);
            return $this->db->resultSet() ?: [];
        }

        $sessionPosts = $_SESSION['_mock_posts'] ?? $this->fallbackPosts;
        $filtered = array_filter($sessionPosts, function($p) use ($customerId) {
            return (int)$p['customer_id'] === $customerId;
        });

        return array_map(function($item) {
            return (object) $item;
        }, array_values($filtered));
    }

    /**
     * Get single post by ID with full details
     *
     * @param int $id
     * @return object|null
     */
    public function getPostById($id) {
        $id = (int) $id;

        if ($this->isDbConnected()) {
            $sql = "SELECT p.*, c.name as category_name, c.icon as category_icon, u.name as customer_name, u.whatsapp as customer_whatsapp,
                    cs.chronology as consultation_chronology
                    FROM {$this->table} p
                    JOIN categories c ON p.category_id = c.id
                    JOIN users u ON p.customer_id = u.id
                    LEFT JOIN consultations cs ON p.consultation_id = cs.id
                    WHERE p.id = :id LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            return $this->db->single() ?: null;
        }

        $sessionPosts = $_SESSION['_mock_posts'] ?? $this->fallbackPosts;
        foreach ($sessionPosts as $p) {
            if ((int)$p['id'] === $id) {
                return (object) $p;
            }
        }

        return null;
    }

    /**
     * Get active posts for technicians (only status 'Mencari Teknisi')
     *
     * @param int|null $categoryId
     * @param int $limit
     * @return array
     */
    public function getActivePostsForTechnicians($categoryId = null, $limit = 20) {
        if ($this->isDbConnected()) {
            $sql = "SELECT p.*, c.name as category_name, c.icon as category_icon, u.name as customer_name, u.whatsapp as customer_whatsapp,
                    (SELECT COUNT(*) FROM {$this->responsesTable} tr WHERE tr.post_id = p.id) as offers_count
                    FROM {$this->table} p
                    JOIN categories c ON p.category_id = c.id
                    JOIN users u ON p.customer_id = u.id
                    WHERE p.status = 'Mencari Teknisi'";
            if ($categoryId) {
                $sql .= " AND p.category_id = :category_id";
            }
            $sql .= " ORDER BY p.id DESC LIMIT :limit";

            $this->db->query($sql);
            if ($categoryId) {
                $this->db->bind(':category_id', (int)$categoryId);
            }
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            return $this->db->resultSet() ?: [];
        }

        $sessionPosts = $_SESSION['_mock_posts'] ?? $this->fallbackPosts;
        $active = array_filter($sessionPosts, function($p) use ($categoryId) {
            $statusMatch = ($p['status'] === 'Mencari Teknisi' || $p['status'] === 'pending');
            $catMatch = $categoryId ? ((int)$p['category_id'] === (int)$categoryId) : true;
            return $statusMatch && $catMatch;
        });

        return array_map(function($item) {
            return (object) $item;
        }, array_slice(array_values($active), 0, $limit));
    }

    /**
     * Create a new complaint post
     *
     * @param array $data
     * @return int|bool Inserted ID
     */
    public function createPost($data) {
        $customerId = (int) $data['customer_id'];
        $consultationId = !empty($data['consultation_id']) ? (int)$data['consultation_id'] : null;
        $categoryId = (int) $data['category_id'];
        $title = trim($data['title']);
        $description = trim($data['description']);
        $image = !empty($data['image']) ? $data['image'] : null;
        $aiAnalysis = !empty($data['ai_analysis']) ? trim($data['ai_analysis']) : null;
        $minPrice = (int) ($data['estimated_min_price'] ?? 0);
        $maxPrice = (int) ($data['estimated_max_price'] ?? 0);
        $status = 'Mencari Teknisi';

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

            $sql = "INSERT INTO {$this->table} (customer_id, consultation_id, category_id, title, description, image, ai_analysis, estimated_min_price, estimated_max_price, status) 
                    VALUES (:customer_id, :consultation_id, :category_id, :title, :description, :image, :ai_analysis, :estimated_min_price, :estimated_max_price, :status)";
            $this->db->query($sql);
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':consultation_id', $consultationId);
            $this->db->bind(':category_id', $categoryId);
            $this->db->bind(':title', $title);
            $this->db->bind(':description', $description);
            $this->db->bind(':image', $image);
            $this->db->bind(':ai_analysis', $aiAnalysis);
            $this->db->bind(':estimated_min_price', $minPrice);
            $this->db->bind(':estimated_max_price', $maxPrice);
            $this->db->bind(':status', $status);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        }

        if (!isset($_SESSION['_mock_posts'])) {
            $_SESSION['_mock_posts'] = $this->fallbackPosts;
        }

        $newId = count($_SESSION['_mock_posts']) + 1;
        $newPost = [
            'id' => $newId,
            'customer_id' => $customerId,
            'customer_name' => $_SESSION['user']['name'] ?? 'Pelanggan',
            'customer_whatsapp' => $_SESSION['user']['whatsapp'] ?? '081234567890',
            'consultation_id' => $consultationId,
            'category_id' => $categoryId,
            'category_name' => 'Kategori Jasa',
            'category_icon' => 'bi-tools',
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'ai_analysis' => $aiAnalysis,
            'estimated_min_price' => $minPrice,
            'estimated_max_price' => $maxPrice,
            'status' => $status,
            'offers_count' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $_SESSION['_mock_posts'][] = $newPost;
        return $newId;
    }

    /**
     * Update an existing post
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updatePost($id, $data) {
        $id = (int)$id;
        $categoryId = (int) $data['category_id'];
        $title = trim($data['title']);
        $description = trim($data['description']);
        $image = isset($data['image']) ? $data['image'] : null;

        if ($this->isDbConnected()) {
            $sql = "UPDATE {$this->table} SET category_id = :category_id, title = :title, description = :description";
            if ($image !== null) {
                $sql .= ", image = :image";
            }
            $sql .= " WHERE id = :id";

            $this->db->query($sql);
            $this->db->bind(':category_id', $categoryId);
            $this->db->bind(':title', $title);
            $this->db->bind(':description', $description);
            if ($image !== null) {
                $this->db->bind(':image', $image);
            }
            $this->db->bind(':id', $id);

            return $this->db->execute();
        }

        if (!isset($_SESSION['_mock_posts'])) {
            $_SESSION['_mock_posts'] = $this->fallbackPosts;
        }

        foreach ($_SESSION['_mock_posts'] as &$p) {
            if ((int)$p['id'] === $id) {
                $p['category_id'] = $categoryId;
                $p['title'] = $title;
                $p['description'] = $description;
                if ($image !== null) {
                    $p['image'] = $image;
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Update status of a post
     *
     * @param int $id
     * @param string $status ('Mencari Teknisi', 'Ada Teknisi', 'Selesai', 'Dibatalkan')
     * @return bool
     */
    public function updateStatus($id, $status) {
        $id = (int)$id;

        if ($this->isDbConnected()) {
            $this->db->query("UPDATE {$this->table} SET status = :status WHERE id = :id");
            $this->db->bind(':status', $status);
            $this->db->bind(':id', $id);
            return $this->db->execute();
        }

        if (!isset($_SESSION['_mock_posts'])) {
            $_SESSION['_mock_posts'] = $this->fallbackPosts;
        }

        foreach ($_SESSION['_mock_posts'] as &$p) {
            if ((int)$p['id'] === $id) {
                $p['status'] = $status;
                return true;
            }
        }

        return false;
    }

    /**
     * Check if technician has already responded to a post (Anti-Duplicate)
     *
     * @param int $postId
     * @param int $technicianId
     * @return bool
     */
    public function hasTechnicianResponded($postId, $technicianId) {
        $postId = (int)$postId;
        $technicianId = (int)$technicianId;

        if ($this->isDbConnected()) {
            $this->db->query("SELECT id FROM {$this->responsesTable} WHERE post_id = :post_id AND technician_id = :technician_id LIMIT 1");
            $this->db->bind(':post_id', $postId);
            $this->db->bind(':technician_id', $technicianId);
            return (bool)$this->db->single();
        }

        $sessionResponses = $_SESSION['_mock_tech_responses'] ?? $this->fallbackResponses;
        foreach ($sessionResponses as $r) {
            if ((int)$r['post_id'] === $postId && (int)$r['technician_id'] === $technicianId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create technician response ("Saya Bisa Membantu")
     *
     * @param int $postId
     * @param int $technicianId
     * @return int|bool
     */
    public function createResponse($postId, $technicianId) {
        $postId = (int)$postId;
        $technicianId = (int)$technicianId;

        if ($this->hasTechnicianResponded($postId, $technicianId)) {
            return false; // Prevent duplicate response
        }

        if ($this->isDbConnected()) {
            $sql = "INSERT INTO {$this->responsesTable} (post_id, technician_id, status) VALUES (:post_id, :technician_id, 'offered')";
            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            $this->db->bind(':technician_id', $technicianId);
            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        }

        if (!isset($_SESSION['_mock_tech_responses'])) {
            $_SESSION['_mock_tech_responses'] = $this->fallbackResponses;
        }

        $newId = count($_SESSION['_mock_tech_responses']) + 1;
        $newResponse = [
            'id' => $newId,
            'post_id' => $postId,
            'technician_id' => $technicianId,
            'technician_name' => $_SESSION['user']['name'] ?? 'Mitra Teknisi',
            'technician_whatsapp' => $_SESSION['user']['whatsapp'] ?? '085678901234',
            'technician_rating' => 5.0,
            'technician_location' => 'Jabodetabek',
            'technician_description' => 'Siap melayani perbaikan.',
            'status' => 'offered',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $_SESSION['_mock_tech_responses'][] = $newResponse;
        return $newId;
    }

    /**
     * Get all technician responses for a specific post
     *
     * @param int $postId
     * @return array
     */
    public function getResponsesByPostId($postId) {
        $postId = (int)$postId;

        if ($this->isDbConnected()) {
            $sql = "SELECT tr.*, u.name as technician_name, u.whatsapp as technician_whatsapp,
                    tp.rating as technician_rating, tp.location as technician_location, tp.description as technician_description
                    FROM {$this->responsesTable} tr
                    JOIN users u ON tr.technician_id = u.id
                    LEFT JOIN technician_profiles tp ON tr.technician_id = tp.user_id
                    WHERE tr.post_id = :post_id
                    ORDER BY (tr.status = 'selected') DESC, tr.id ASC";
            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            $results = $this->db->resultSet();
            if (!empty($results)) {
                return $results;
            }
        }

        $sessionResponses = $_SESSION['_mock_tech_responses'] ?? $this->fallbackResponses;
        $filtered = array_filter($sessionResponses, function($r) use ($postId) {
            return (int)$r['post_id'] === $postId;
        });

        return array_map(function($item) {
            return (object) $item;
        }, array_values($filtered));
    }

    /**
     * Customer selects a technician
     * Sets selected response to 'selected', others to 'rejected', and post status to 'Ada Teknisi'
     *
     * @param int $postId
     * @param int $responseId
     * @param int $technicianId
     * @return bool
     */
    public function selectTechnicianResponse($postId, $responseId, $technicianId) {
        $postId = (int)$postId;
        $responseId = (int)$responseId;

        if ($this->isDbConnected()) {
            // 1. Mark selected response
            $this->db->query("UPDATE {$this->responsesTable} SET status = 'selected' WHERE id = :response_id");
            $this->db->bind(':response_id', $responseId);
            $this->db->execute();

            // 2. Mark other responses on this post as rejected
            $this->db->query("UPDATE {$this->responsesTable} SET status = 'rejected' WHERE post_id = :post_id AND id != :response_id");
            $this->db->bind(':post_id', $postId);
            $this->db->bind(':response_id', $responseId);
            $this->db->execute();

            // 3. Update post status to 'Ada Teknisi'
            return $this->updateStatus($postId, 'Ada Teknisi');
        }

        // Mock update
        if (!isset($_SESSION['_mock_tech_responses'])) {
            $_SESSION['_mock_tech_responses'] = $this->fallbackResponses;
        }

        foreach ($_SESSION['_mock_tech_responses'] as &$r) {
            if ((int)$r['post_id'] === $postId) {
                if ((int)$r['id'] === $responseId) {
                    $r['status'] = 'selected';
                } else {
                    $r['status'] = 'rejected';
                }
            }
        }

        return $this->updateStatus($postId, 'Ada Teknisi');
    }

    /**
     * Get selected response for a post (if any)
     *
     * @param int $postId
     * @return object|null
     */
    public function getSelectedResponseByPostId($postId) {
        $postId = (int)$postId;
        if ($this->isDbConnected()) {
            $sql = "SELECT tr.*, u.name as technician_name, u.email as technician_email, u.whatsapp as technician_whatsapp
                    FROM {$this->responsesTable} tr
                    JOIN users u ON tr.technician_id = u.id
                    WHERE tr.post_id = :post_id AND tr.status = 'selected'
                    LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            return $this->db->single();
        }
        return null;
    }

    /**
     * Check if a technician is assigned/selected for a post
     *
     * @param int $postId
     * @param int $technicianId
     * @return bool
     */
    public function isTechnicianAssignedToPost($postId, $technicianId) {
        $postId = (int)$postId;
        $technicianId = (int)$technicianId;
        if ($this->isDbConnected()) {
            $sql = "SELECT COUNT(*) as cnt FROM {$this->responsesTable} 
                    WHERE post_id = :post_id AND technician_id = :technician_id AND status = 'selected'";
            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            $this->db->bind(':technician_id', $technicianId);
            $res = $this->db->single();
            return $res && (int)$res->cnt > 0;
        }
        return false;
    }

    /**
     * Get responses made by a technician (for technician dashboard)
     *
     * @param int $technicianId
     * @return array
     */
    public function getResponsesByTechnicianId($technicianId) {
        $technicianId = (int)$technicianId;

        if ($this->isDbConnected()) {
            $sql = "SELECT tr.*, 
                           p.title as post_title, 
                           p.description as post_description, 
                           p.image as post_image, 
                           p.status as post_status, 
                           p.estimated_min_price, 
                           p.estimated_max_price, 
                           p.created_at as post_created_at, 
                           c.name as category_name, 
                           c.icon as category_icon, 
                           u.name as customer_name, 
                           u.whatsapp as customer_whatsapp, 
                           u.email as customer_email,
                           (SELECT conv.id FROM conversations conv WHERE conv.post_id = p.id AND conv.technician_id = tr.technician_id LIMIT 1) as conversation_id
                    FROM {$this->responsesTable} tr
                    JOIN posts p ON tr.post_id = p.id
                    JOIN categories c ON p.category_id = c.id
                    JOIN users u ON p.customer_id = u.id
                    WHERE tr.technician_id = :technician_id
                    ORDER BY tr.id DESC";
            $this->db->query($sql);
            $this->db->bind(':technician_id', $technicianId);
            $results = $this->db->resultSet();
            if (!empty($results)) {
                return $results;
            }
        }

        $sessionResponses = $_SESSION['_mock_tech_responses'] ?? $this->fallbackResponses;
        $filtered = array_filter($sessionResponses, function($r) use ($technicianId) {
            return (int)$r['technician_id'] === $technicianId;
        });

        return array_map(function($item) {
            return (object) $item;
        }, array_values($filtered));
    }

    /**
     * Total posts count
     *
     * @return int
     */
    public function getTotalPostsCount() {
        if ($this->isDbConnected()) {
            $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
            $res = $this->db->single();
            return $res ? (int)$res->total : 0;
        }

        return count($this->fallbackPosts) + count($_SESSION['_mock_posts'] ?? []);
    }

    /**
     * Get all posts for Admin with optional status & category filter
     *
     * @param string|null $status
     * @param int|null $categoryId
     * @return array
     */
    public function getAllPostsForAdmin($status = null, $categoryId = null) {
        if ($this->isDbConnected()) {
            $sql = "SELECT p.*, c.name as category_name, c.icon as category_icon, 
                    u.name as customer_name, u.email as customer_email, u.whatsapp as customer_whatsapp,
                    (SELECT COUNT(*) FROM {$this->responsesTable} tr WHERE tr.post_id = p.id) as offers_count,
                    (SELECT COUNT(*) FROM {$this->responsesTable} tr WHERE tr.post_id = p.id AND tr.status = 'selected') as selected_count
                    FROM {$this->table} p
                    JOIN categories c ON p.category_id = c.id
                    JOIN users u ON p.customer_id = u.id
                    WHERE 1=1";
            if (!empty($status) && $status !== 'all') {
                $sql .= " AND p.status = :status";
            }
            if (!empty($categoryId) && $categoryId !== 'all') {
                $sql .= " AND p.category_id = :category_id";
            }
            $sql .= " ORDER BY p.id DESC";

            $this->db->query($sql);
            if (!empty($status) && $status !== 'all') {
                $this->db->bind(':status', $status);
            }
            if (!empty($categoryId) && $categoryId !== 'all') {
                $this->db->bind(':category_id', (int)$categoryId);
            }

            return $this->db->resultSet() ?: [];
        }

        $sessionPosts = $_SESSION['_mock_posts'] ?? [];
        $all = array_merge($this->fallbackPosts, $sessionPosts);

        if (!empty($status) && $status !== 'all') {
            $all = array_filter($all, function($p) use ($status) {
                return ($p['status'] === $status);
            });
        }
        if (!empty($categoryId) && $categoryId !== 'all') {
            $all = array_filter($all, function($p) use ($categoryId) {
                return ((int)$p['category_id'] === (int)$categoryId);
            });
        }

        return array_map(function($item) {
            return (object) $item;
        }, array_values($all));
    }

    /**
     * Admin update status of post
     *
     * @param int $postId
     * @param string $status
     * @return bool
     */
    public function adminUpdateStatus($postId, $status) {
        $allowed = ['Mencari Teknisi', 'Ada Teknisi', 'Selesai', 'Dibatalkan'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $postId = (int) $postId;

        if ($this->isDbConnected()) {
            $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':status', $status);
            $this->db->bind(':id', $postId);
            return $this->db->execute();
        }

        // Mock update
        if (isset($_SESSION['_mock_posts'])) {
            foreach ($_SESSION['_mock_posts'] as &$p) {
                if ($p['id'] == $postId) {
                    $p['status'] = $status;
                }
            }
        }
        foreach ($this->fallbackPosts as &$p) {
            if ($p['id'] == $postId) {
                $p['status'] = $status;
            }
        }

        return true;
    }

    /**
     * Soft delete post (set status to 'Dibatalkan')
     *
     * @param int $postId
     * @return bool
     */
    public function adminSoftDeletePost($postId) {
        return $this->adminUpdateStatus($postId, 'Dibatalkan');
    }

    /**
     * Get the assigned/selected technician for a post
     *
     * @param int $postId
     * @return object|null
     */
    public function getAssignedTechnician($postId) {
        $postId = (int)$postId;
        if ($this->isDbConnected()) {
            // 1. Try selected technician in responses
            $sql = "SELECT u.id as user_id, u.name, u.email, u.whatsapp,
                           tp.location, tp.rating, c.name as category_name, c.icon as category_icon,
                           tr.id as response_id, tr.status as response_status
                    FROM {$this->responsesTable} tr
                    JOIN users u ON tr.technician_id = u.id
                    LEFT JOIN technician_profiles tp ON u.id = tp.user_id
                    LEFT JOIN categories c ON tp.category_id = c.id
                    WHERE tr.post_id = :post_id AND tr.status = 'selected'
                    LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            $res = $this->db->single();
            if ($res) return $res;

            // 2. Check conversation for this post
            $sql2 = "SELECT u.id as user_id, u.name, u.email, u.whatsapp,
                            tp.location, tp.rating, c.name as category_name, c.icon as category_icon
                     FROM conversations conv
                     JOIN users u ON conv.technician_id = u.id
                     LEFT JOIN technician_profiles tp ON u.id = tp.user_id
                     LEFT JOIN categories c ON tp.category_id = c.id
                     WHERE conv.post_id = :post_id
                     LIMIT 1";
            $this->db->query($sql2);
            $this->db->bind(':post_id', $postId);
            $res2 = $this->db->single();
            if ($res2) return $res2;

            // 3. Fallback to any response on this post
            $sql3 = "SELECT u.id as user_id, u.name, u.email, u.whatsapp,
                            tp.location, tp.rating, c.name as category_name, c.icon as category_icon,
                            tr.id as response_id, tr.status as response_status
                     FROM {$this->responsesTable} tr
                     JOIN users u ON tr.technician_id = u.id
                     LEFT JOIN technician_profiles tp ON u.id = tp.user_id
                     LEFT JOIN categories c ON tp.category_id = c.id
                     WHERE tr.post_id = :post_id
                     ORDER BY tr.id ASC
                     LIMIT 1";
            $this->db->query($sql3);
            $this->db->bind(':post_id', $postId);
            return $this->db->single();
        }

        return null;
    }

    /**
     * Get review for a post
     *
     * @param int $postId
     * @return object|null
     */
    public function getReviewByPostId($postId) {
        $postId = (int)$postId;
        if ($this->isDbConnected()) {
            $sql = "SELECT r.*, 
                           c.name as customer_name,
                           t.name as technician_name,
                           tp.location as technician_location,
                           tp.rating as technician_current_rating
                    FROM reviews r
                    JOIN users c ON r.customer_id = c.id
                    JOIN users t ON r.technician_id = t.id
                    LEFT JOIN technician_profiles tp ON t.id = tp.user_id
                    WHERE r.post_id = :post_id
                    LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            return $this->db->single();
        }

        return null;
    }

    /**
     * Save or update review and recalculate technician average rating
     *
     * @param int $postId
     * @param int $customerId
     * @param int $technicianId
     * @param int $rating
     * @param string $comment
     * @return bool
     */
    public function saveReview($postId, $customerId, $technicianId, $rating, $comment) {
        $postId = (int)$postId;
        $customerId = (int)$customerId;
        $technicianId = (int)$technicianId;
        $rating = max(1, min(5, (int)$rating));
        $comment = trim($comment);

        if ($this->isDbConnected()) {
            $sql = "INSERT INTO reviews (post_id, customer_id, technician_id, rating, comment)
                    VALUES (:post_id, :customer_id, :technician_id, :rating, :comment)
                    ON DUPLICATE KEY UPDATE
                    rating = VALUES(rating),
                    comment = VALUES(comment),
                    updated_at = CURRENT_TIMESTAMP";
            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':technician_id', $technicianId);
            $this->db->bind(':rating', $rating);
            $this->db->bind(':comment', $comment);
            $ok = $this->db->execute();

            if ($ok) {
                // Recalculate average rating of technician
                $this->db->query("SELECT AVG(rating) as avg_rating FROM reviews WHERE technician_id = :tech_id");
                $this->db->bind(':tech_id', $technicianId);
                $avgRow = $this->db->single();
                if ($avgRow && $avgRow->avg_rating !== null) {
                    $newRating = round((float)$avgRow->avg_rating, 2);
                    $this->db->query("UPDATE technician_profiles SET rating = :rating WHERE user_id = :tech_id");
                    $this->db->bind(':rating', $newRating);
                    $this->db->bind(':tech_id', $technicianId);
                    $this->db->execute();
                }
            }
            return $ok;
        }

        return false;
    }

    /**
     * Save payment confirmation and mark post as Selesai
     *
     * @param int $postId
     * @param int $customerId
     * @param int $technicianId
     * @param int $amount
     * @param string $paymentMethod ('qris', 'cash', 'transfer')
     * @param string $notes
     * @return object|false
     */
    public function savePayment($postId, $customerId, $technicianId, $amount, $paymentMethod = 'cash', $notes = '') {
        $postId = (int)$postId;
        $customerId = (int)$customerId;
        $technicianId = (int)$technicianId;
        $amount = max(0, (int)$amount);
        $paymentMethod = in_array($paymentMethod, ['qris', 'cash', 'transfer'], true) ? $paymentMethod : 'cash';
        $notes = trim((string)$notes);

        if ($this->isDbConnected()) {
            // Generate clean unique invoice number
            $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $sql = "INSERT INTO payments (invoice_no, post_id, customer_id, technician_id, amount, payment_method, status, notes, paid_at, created_at)
                    VALUES (:invoice_no, :post_id, :customer_id, :technician_id, :amount, :payment_method, 'completed', :notes, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                    amount = VALUES(amount),
                    payment_method = VALUES(payment_method),
                    notes = VALUES(notes),
                    status = 'completed',
                    paid_at = NOW()";
            $this->db->query($sql);
            $this->db->bind(':invoice_no', $invoiceNo);
            $this->db->bind(':post_id', $postId);
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':technician_id', $technicianId);
            $this->db->bind(':amount', $amount);
            $this->db->bind(':payment_method', $paymentMethod);
            $this->db->bind(':notes', $notes);
            $ok = $this->db->execute();

            if ($ok) {
                // Update post status to Selesai and store agreed_price
                $this->db->query("UPDATE {$this->table} SET status = 'Selesai', agreed_price = :amount WHERE id = :id");
                $this->db->bind(':amount', $amount);
                $this->db->bind(':id', $postId);
                $this->db->execute();

                return $this->getPaymentByPostId($postId);
            }
        }
        return false;
    }

    /**
     * Get payment details by Post ID
     *
     * @param int $postId
     * @return object|null
     */
    public function getPaymentByPostId($postId) {
        $postId = (int)$postId;
        if ($this->isDbConnected()) {
            $sql = "SELECT py.*, 
                           c.name AS customer_name, c.email AS customer_email, c.whatsapp AS customer_whatsapp,
                           t.name AS technician_name, t.email AS technician_email, t.whatsapp AS technician_whatsapp,
                           tp.location AS technician_location, cat.name AS category_name
                    FROM payments py
                    JOIN users c ON py.customer_id = c.id
                    JOIN users t ON py.technician_id = t.id
                    JOIN posts p ON py.post_id = p.id
                    JOIN categories cat ON p.category_id = cat.id
                    LEFT JOIN technician_profiles tp ON t.id = tp.user_id
                    WHERE py.post_id = :post_id
                    LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            return $this->db->single();
        }
        return null;
    }

    /**
     * Get transaction & order history for a user
     *
     * @param int|null $userId
     * @param string $role ('customer', 'technician', 'admin')
     * @param string $filter ('all', 'completed', 'in_progress')
     * @return array
     */
    public function getTransactionHistory($userId = null, $role = 'customer', $filter = 'all') {
        if (!$this->isDbConnected()) {
            return [];
        }

        $sql = "SELECT p.*, 
                       c.name AS category_name, c.icon AS category_icon,
                       cust.name AS customer_name, cust.whatsapp AS customer_whatsapp,
                       tech.name AS technician_name, tech.whatsapp AS technician_whatsapp,
                       pay.id AS payment_id, pay.invoice_no, pay.amount AS paid_amount, pay.payment_method, pay.status AS payment_status, pay.paid_at, pay.notes AS payment_notes,
                       r.id AS review_id, r.rating AS review_rating, r.comment AS review_comment,
                       conv.id AS conversation_id
                FROM posts p
                JOIN categories c ON p.category_id = c.id
                JOIN users cust ON p.customer_id = cust.id
                LEFT JOIN technician_responses tr ON tr.post_id = p.id AND tr.status = 'selected'
                LEFT JOIN users tech ON tr.technician_id = tech.id
                LEFT JOIN payments pay ON pay.post_id = p.id
                LEFT JOIN reviews r ON r.post_id = p.id
                LEFT JOIN conversations conv ON conv.post_id = p.id";

        $where = [];
        $params = [];

        if ($role === 'customer' && $userId) {
            $where[] = "p.customer_id = :uid";
            $params[':uid'] = (int)$userId;
        } elseif ($role === 'technician' && $userId) {
            $where[] = "tr.technician_id = :uid";
            $params[':uid'] = (int)$userId;
        }

        if ($filter === 'completed') {
            $where[] = "p.status = 'Selesai'";
        } elseif ($filter === 'in_progress') {
            $where[] = "p.status IN ('Ada Teknisi', 'Mencari Teknisi')";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY COALESCE(pay.paid_at, p.updated_at, p.created_at) DESC";

        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $this->db->bind($k, $v);
        }

        return $this->db->resultSet() ?: [];
    }
}


