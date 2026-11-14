<?php
/**
 * Chat Model
 * Project: Teknisi Rumah Tangga
 * Handles Conversations & Messages between Customer and Technician
 */

class ChatModel extends Model {
    private $convTable = 'conversations';
    private $msgTable = 'messages';

    /**
     * Get or create a conversation between customer and technician for a post
     *
     * @param int $postId
     * @param int $customerId
     * @param int $technicianId
     * @return object|false
     */
    public function getOrCreateConversation($postId, $customerId, $technicianId) {
        $postId = (int)$postId;
        $customerId = (int)$customerId;
        $technicianId = (int)$technicianId;

        if ($this->isDbConnected()) {
            // Check existing
            $sql = "SELECT * FROM {$this->convTable} 
                    WHERE post_id = :post_id AND customer_id = :customer_id AND technician_id = :technician_id 
                    LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':technician_id', $technicianId);
            $existing = $this->db->single();

            if ($existing) {
                return $this->getConversationById($existing->id);
            }

            // Create new
            $insertSql = "INSERT INTO {$this->convTable} (post_id, customer_id, technician_id, created_at, updated_at) 
                          VALUES (:post_id, :customer_id, :technician_id, NOW(), NOW())";
            $this->db->query($insertSql);
            $this->db->bind(':post_id', $postId);
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':technician_id', $technicianId);
            
            if ($this->db->execute()) {
                $newId = (int)$this->db->lastInsertId();
                return $this->getConversationById($newId);
            }
        }

        return false;
    }

    /**
     * Get conversation details by conversation ID
     *
     * @param int $id
     * @return object|false
     */
    public function getConversationById($id) {
        $id = (int)$id;

        if ($this->isDbConnected()) {
            $sql = "SELECT c.*, 
                           p.title AS post_title, p.description AS post_description, p.status AS post_status,
                           p.agreed_price AS post_agreed_price, p.estimated_min_price, p.estimated_max_price,
                           cat.name AS category_name, cat.icon AS category_icon, cat.slug AS category_slug,
                           cust.name AS customer_name, cust.whatsapp AS customer_whatsapp, cust.email AS customer_email,
                           tech.name AS technician_name, tech.whatsapp AS technician_whatsapp, tech.email AS technician_email
                    FROM {$this->convTable} c
                    JOIN posts p ON c.post_id = p.id
                    JOIN categories cat ON p.category_id = cat.id
                    JOIN users cust ON c.customer_id = cust.id
                    JOIN users tech ON c.technician_id = tech.id
                    WHERE c.id = :id
                    LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            return $this->db->single();
        }

        return false;
    }

    /**
     * Get conversation by Post ID (and optional technician)
     *
     * @param int $postId
     * @param int|null $technicianId
     * @return object|false
     */
    public function getConversationByPostId($postId, $technicianId = null) {
        $postId = (int)$postId;

        if ($this->isDbConnected()) {
            $sql = "SELECT c.*, 
                           p.title AS post_title, p.description AS post_description, p.status AS post_status,
                           cust.name AS customer_name, cust.whatsapp AS customer_whatsapp,
                           tech.name AS technician_name, tech.whatsapp AS technician_whatsapp
                    FROM {$this->convTable} c
                    JOIN posts p ON c.post_id = p.id
                    JOIN users cust ON c.customer_id = cust.id
                    JOIN users tech ON c.technician_id = tech.id
                    WHERE c.post_id = :post_id";
            if ($technicianId !== null) {
                $sql .= " AND c.technician_id = :technician_id";
            }
            $sql .= " ORDER BY c.id DESC LIMIT 1";

            $this->db->query($sql);
            $this->db->bind(':post_id', $postId);
            if ($technicianId !== null) {
                $this->db->bind(':technician_id', (int)$technicianId);
            }
            return $this->db->single();
        }

        return false;
    }

    /**
     * Check if user is a valid participant of the conversation
     *
     * @param int $conversationId
     * @param int $userId
     * @return bool
     */
    public function isParticipant($conversationId, $userId) {
        $conversationId = (int)$conversationId;
        $userId = (int)$userId;

        if ($this->isDbConnected()) {
            $sql = "SELECT id FROM {$this->convTable} 
                    WHERE id = :id AND (customer_id = :cust_user_id OR technician_id = :tech_user_id) 
                    LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $conversationId);
            $this->db->bind(':cust_user_id', $userId);
            $this->db->bind(':tech_user_id', $userId);
            return (bool)$this->db->single();
        }

        return false;
    }

    /**
     * Get list of conversations for a user with latest message info and unread count
     *
     * @param int $userId
     * @return array
     */
    public function getConversationsByUserId($userId) {
        $userId = (int)$userId;

        if ($this->isDbConnected()) {
            $sql = "SELECT c.*,
                           p.title AS post_title, p.status AS post_status,
                           cat.name AS category_name, cat.icon AS category_icon,
                           cust.id AS cust_id, cust.name AS customer_name, cust.whatsapp AS customer_whatsapp,
                           tech.id AS tech_id, tech.name AS technician_name, tech.whatsapp AS technician_whatsapp,
                           (
                               SELECT m.message 
                               FROM {$this->msgTable} m 
                               WHERE m.conversation_id = c.id 
                               ORDER BY m.id DESC 
                               LIMIT 1
                           ) AS last_message,
                           (
                               SELECT m.created_at 
                               FROM {$this->msgTable} m 
                               WHERE m.conversation_id = c.id 
                               ORDER BY m.id DESC 
                               LIMIT 1
                           ) AS last_message_time,
                           (
                               SELECT m.sender_id 
                               FROM {$this->msgTable} m 
                               WHERE m.conversation_id = c.id 
                               ORDER BY m.id DESC 
                               LIMIT 1
                           ) AS last_sender_id,
                           (
                               SELECT COUNT(*) 
                               FROM {$this->msgTable} m 
                               WHERE m.conversation_id = c.id 
                                 AND m.sender_id != :uid_unread 
                                 AND m.is_read = 0
                           ) AS unread_count
                    FROM {$this->convTable} c
                    JOIN posts p ON c.post_id = p.id
                    JOIN categories cat ON p.category_id = cat.id
                    JOIN users cust ON c.customer_id = cust.id
                    JOIN users tech ON c.technician_id = tech.id
                    WHERE c.customer_id = :uid_cust OR c.technician_id = :uid_tech
                    ORDER BY COALESCE(last_message_time, c.updated_at, c.created_at) DESC";

            $this->db->query($sql);
            $this->db->bind(':uid_unread', $userId);
            $this->db->bind(':uid_cust', $userId);
            $this->db->bind(':uid_tech', $userId);
            return $this->db->resultSet() ?: [];
        }

        return [];
    }

    /**
     * Get all messages in a conversation
     *
     * @param int $conversationId
     * @return array
     */
    public function getMessagesByConversationId($conversationId) {
        $conversationId = (int)$conversationId;

        if ($this->isDbConnected()) {
            $sql = "SELECT m.*, u.name AS sender_name, u.role AS sender_role
                    FROM {$this->msgTable} m
                    JOIN users u ON m.sender_id = u.id
                    WHERE m.conversation_id = :conversation_id
                    ORDER BY m.id ASC";
            $this->db->query($sql);
            $this->db->bind(':conversation_id', $conversationId);
            return $this->db->resultSet() ?: [];
        }

        return [];
    }

    /**
     * Get new messages after a specific ID for polling
     *
     * @param int $conversationId
     * @param int $afterId
     * @return array
     */
    public function getNewMessages($conversationId, $afterId) {
        $conversationId = (int)$conversationId;
        $afterId = (int)$afterId;

        if ($this->isDbConnected()) {
            $sql = "SELECT m.*, u.name AS sender_name, u.role AS sender_role
                    FROM {$this->msgTable} m
                    JOIN users u ON m.sender_id = u.id
                    WHERE m.conversation_id = :conversation_id AND m.id > :after_id
                    ORDER BY m.id ASC";
            $this->db->query($sql);
            $this->db->bind(':conversation_id', $conversationId);
            $this->db->bind(':after_id', $afterId);
            return $this->db->resultSet() ?: [];
        }

        return [];
    }

    /**
     * Send a new message
     *
     * @param int $conversationId
     * @param int $senderId
     * @param string $message
     * @return object|false
     */
    public function sendMessage($conversationId, $senderId, $message) {
        $conversationId = (int)$conversationId;
        $senderId = (int)$senderId;
        $message = trim((string)$message);

        if (empty($message)) {
            return false;
        }

        if ($this->isDbConnected()) {
            $sql = "INSERT INTO {$this->msgTable} (conversation_id, sender_id, message, is_read, created_at)
                    VALUES (:conversation_id, :sender_id, :message, 0, NOW())";
            $this->db->query($sql);
            $this->db->bind(':conversation_id', $conversationId);
            $this->db->bind(':sender_id', $senderId);
            $this->db->bind(':message', $message);

            if ($this->db->execute()) {
                $newId = (int)$this->db->lastInsertId();

                // Update conversation updated_at
                $updateConv = "UPDATE {$this->convTable} SET updated_at = NOW() WHERE id = :id";
                $this->db->query($updateConv);
                $this->db->bind(':id', $conversationId);
                $this->db->execute();

                // Return full message object with sender info
                $fetchSql = "SELECT m.*, u.name AS sender_name, u.role AS sender_role 
                             FROM {$this->msgTable} m
                             JOIN users u ON m.sender_id = u.id
                             WHERE m.id = :id LIMIT 1";
                $this->db->query($fetchSql);
                $this->db->bind(':id', $newId);
                return $this->db->single();
            }
        }

        return false;
    }

    /**
     * Mark all unread messages from other participants as read
     *
     * @param int $conversationId
     * @param int $userId
     * @return bool
     */
    public function markMessagesAsRead($conversationId, $userId) {
        $conversationId = (int)$conversationId;
        $userId = (int)$userId;

        if ($this->isDbConnected()) {
            $sql = "UPDATE {$this->msgTable} 
                    SET is_read = 1 
                    WHERE conversation_id = :conversation_id 
                      AND sender_id != :user_id 
                      AND is_read = 0";
            $this->db->query($sql);
            $this->db->bind(':conversation_id', $conversationId);
            $this->db->bind(':user_id', $userId);
            return $this->db->execute();
        }

        return false;
    }

    /**
     * Get total unread messages count for a user
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId) {
        $userId = (int)$userId;

        if ($this->isDbConnected()) {
            $sql = "SELECT COUNT(*) AS total
                    FROM {$this->msgTable} m
                    JOIN {$this->convTable} c ON m.conversation_id = c.id
                    WHERE (c.customer_id = :uid_c OR c.technician_id = :uid_t)
                      AND m.sender_id != :uid_s
                      AND m.is_read = 0";
            $this->db->query($sql);
            $this->db->bind(':uid_c', $userId);
            $this->db->bind(':uid_t', $userId);
            $this->db->bind(':uid_s', $userId);
            $res = $this->db->single();
            return $res ? (int)$res->total : 0;
        }

        return 0;
    }

    /**
     * Create a price negotiation offer
     *
     * @param int $conversationId
     * @param int $postId
     * @param int $senderId
     * @param int $price
     * @param string $note
     * @return object|false
     */
    public function createNegotiation($conversationId, $postId, $senderId, $price, $note = '') {
        $conversationId = (int)$conversationId;
        $postId = (int)$postId;
        $senderId = (int)$senderId;
        $price = max(1000, (int)$price);
        $note = trim((string)$note);

        if ($this->isDbConnected()) {
            $sql = "INSERT INTO price_negotiations (conversation_id, post_id, sender_id, proposed_price, note, status, created_at, updated_at)
                    VALUES (:conversation_id, :post_id, :sender_id, :proposed_price, :note, 'pending', NOW(), NOW())";
            $this->db->query($sql);
            $this->db->bind(':conversation_id', $conversationId);
            $this->db->bind(':post_id', $postId);
            $this->db->bind(':sender_id', $senderId);
            $this->db->bind(':proposed_price', $price);
            $this->db->bind(':note', $note);

            if ($this->db->execute()) {
                $newNegoId = (int)$this->db->lastInsertId();

                // Also insert a negotiation message into the chat thread
                $msgText = "[NEGO:{$newNegoId}:{$price}:pending:" . base64_encode($note) . "]";
                $this->sendMessage($conversationId, $senderId, $msgText);

                return $this->getNegotiationById($newNegoId);
            }
        }
        return false;
    }

    /**
     * Get negotiation by ID
     *
     * @param int $id
     * @return object|false
     */
    public function getNegotiationById($id) {
        $id = (int)$id;
        if ($this->isDbConnected()) {
            $sql = "SELECT n.*, u.name as sender_name, u.role as sender_role
                    FROM price_negotiations n
                    JOIN users u ON n.sender_id = u.id
                    WHERE n.id = :id LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            return $this->db->single();
        }
        return false;
    }

    /**
     * Get latest negotiation for a conversation
     *
     * @param int $conversationId
     * @return object|false
     */
    public function getLatestNegotiation($conversationId) {
        $conversationId = (int)$conversationId;
        if ($this->isDbConnected()) {
            $sql = "SELECT n.*, u.name as sender_name, u.role as sender_role
                    FROM price_negotiations n
                    JOIN users u ON n.sender_id = u.id
                    WHERE n.conversation_id = :conversation_id
                    ORDER BY n.id DESC LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':conversation_id', $conversationId);
            return $this->db->single();
        }
        return false;
    }

    /**
     * Accept or reject a negotiation offer
     *
     * @param int $negoId
     * @param string $action ('accept' or 'reject')
     * @param int $userId
     * @return bool
     */
    public function respondNegotiation($negoId, $action, $userId) {
        $negoId = (int)$negoId;
        $userId = (int)$userId;
        $statusVal = ($action === 'accept') ? 'accepted' : 'rejected';

        if ($this->isDbConnected()) {
            $nego = $this->getNegotiationById($negoId);
            if (!$nego) {
                return false;
            }

            // Update negotiation status
            $sql = "UPDATE price_negotiations SET status = :status, updated_at = NOW() WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':status', $statusVal);
            $this->db->bind(':id', $negoId);
            $ok = $this->db->execute();

            if ($ok) {
                if ($statusVal === 'accepted') {
                    // Update posts table agreed_price
                    $this->db->query("UPDATE posts SET agreed_price = :price WHERE id = :post_id");
                    $this->db->bind(':price', $nego->proposed_price);
                    $this->db->bind(':post_id', $nego->post_id);
                    $this->db->execute();

                    // Send deal confirmation message
                    $dealMsg = "[NEGO_DEAL:{$negoId}:{$nego->proposed_price}]";
                    $this->sendMessage($nego->conversation_id, $userId, $dealMsg);
                } else {
                    $dealMsg = "[NEGO_REJECT:{$negoId}:{$nego->proposed_price}]";
                    $this->sendMessage($nego->conversation_id, $userId, $dealMsg);
                }
                return true;
            }
        }
        return false;
    }
}
