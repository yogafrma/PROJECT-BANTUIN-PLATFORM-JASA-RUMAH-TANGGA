<?php
/**
 * Notification Model
 * Project: Teknisi Rumah Tangga (BANTUIN)
 * Handles in-app notifications for all roles (customer, technician, admin)
 */

class NotificationModel extends Model {
    private $table = 'notifications';

    public function __construct() {
        parent::__construct();
        $this->ensureTable();
    }

    /**
     * Ensure notifications table exists in MySQL
     */
    private function ensureTable() {
        if ($this->isDbConnected()) {
            try {
                $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NOT NULL,
                    `type` VARCHAR(50) NOT NULL DEFAULT 'system',
                    `title` VARCHAR(200) NOT NULL,
                    `message` TEXT NOT NULL,
                    `link` VARCHAR(255) NOT NULL DEFAULT '',
                    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                    INDEX `idx_user_unread` (`user_id`, `is_read`),
                    INDEX `idx_created` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                $this->db->query($sql);
                $this->db->execute();
            } catch (\Throwable $e) {
                // Table already exists or silent fallback
            }
        }
    }

    /**
     * Create a new notification for a user
     *
     * @param int    $userId  Target user ID
     * @param string $type    Notification type: 'new_response', 'response_selected', 'chat_message', 'post_status', 'system'
     * @param string $title   Short notification title
     * @param string $message Full notification message
     * @param string $link    Relative URL to redirect on click (e.g. 'posts/detail/5')
     * @return bool
     */
    public function create($userId, $type, $title, $message, $link = '') {
        $userId = (int)$userId;
        if (!$userId || empty(trim($title))) {
            return false;
        }

        if ($this->isDbConnected()) {
            $sql = "INSERT INTO {$this->table} (user_id, type, title, message, link, is_read, created_at)
                    VALUES (:user_id, :type, :title, :message, :link, 0, NOW())";
            $this->db->query($sql);
            $this->db->bind(':user_id',  $userId);
            $this->db->bind(':type',     substr((string)$type,    0, 50));
            $this->db->bind(':title',    substr((string)$title,   0, 200));
            $this->db->bind(':message',  (string)$message);
            $this->db->bind(':link',     substr((string)$link,    0, 255));
            return $this->db->execute();
        }

        return false;
    }

    /**
     * Get paginated notifications for a user, newest first
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getByUserId($userId, $limit = 15) {
        $userId = (int)$userId;
        $limit  = (int)$limit;

        if ($this->isDbConnected()) {
            $sql = "SELECT * FROM {$this->table}
                    WHERE user_id = :user_id
                    ORDER BY created_at DESC
                    LIMIT {$limit}";
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId);
            return $this->db->resultSet() ?: [];
        }

        return [];
    }

    /**
     * Count total unread notifications for a user
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId) {
        $userId = (int)$userId;

        if ($this->isDbConnected()) {
            $sql = "SELECT COUNT(*) AS total FROM {$this->table}
                    WHERE user_id = :user_id AND is_read = 0";
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId);
            $res = $this->db->single();
            return $res ? (int)$res->total : 0;
        }

        return 0;
    }

    /**
     * Mark a single notification as read (only if it belongs to the user)
     *
     * @param int $notifId
     * @param int $userId
     * @return bool
     */
    public function markAsRead($notifId, $userId) {
        $notifId = (int)$notifId;
        $userId  = (int)$userId;

        if ($this->isDbConnected()) {
            $sql = "UPDATE {$this->table}
                    SET is_read = 1
                    WHERE id = :id AND user_id = :user_id AND is_read = 0";
            $this->db->query($sql);
            $this->db->bind(':id',      $notifId);
            $this->db->bind(':user_id', $userId);
            return $this->db->execute();
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead($userId) {
        $userId = (int)$userId;

        if ($this->isDbConnected()) {
            $sql = "UPDATE {$this->table}
                    SET is_read = 1
                    WHERE user_id = :user_id AND is_read = 0";
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId);
            return $this->db->execute();
        }

        return false;
    }

    /**
     * Delete a single notification (only if it belongs to the user)
     *
     * @param int $notifId
     * @param int $userId
     * @return bool
     */
    public function delete($notifId, $userId) {
        $notifId = (int)$notifId;
        $userId  = (int)$userId;

        if ($this->isDbConnected()) {
            $sql = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
            $this->db->query($sql);
            $this->db->bind(':id',      $notifId);
            $this->db->bind(':user_id', $userId);
            return $this->db->execute();
        }

        return false;
    }

    /**
     * Delete all read notifications older than N days for a user (cleanup)
     *
     * @param int $userId
     * @param int $days
     * @return bool
     */
    public function cleanup($userId, $days = 30) {
        $userId = (int)$userId;
        $days   = max(1, (int)$days);

        if ($this->isDbConnected()) {
            $sql = "DELETE FROM {$this->table}
                    WHERE user_id = :user_id AND is_read = 1
                      AND created_at < NOW() - INTERVAL {$days} DAY";
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId);
            return $this->db->execute();
        }

        return false;
    }
}
