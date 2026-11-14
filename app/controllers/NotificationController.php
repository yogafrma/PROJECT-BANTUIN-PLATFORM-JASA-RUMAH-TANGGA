<?php
/**
 * Notification Controller
 * Project: Teknisi Rumah Tangga (BANTUIN)
 * Handles AJAX endpoints for the in-app notification system
 */

class NotificationController extends Controller {

    private $notifModel;

    public function __construct() {
        $this->notifModel = $this->model('NotificationModel');
    }

    /**
     * AJAX: Get notification list + unread count for current user
     * URL: /notification/list
     * Method: GET
     */
    public function list() {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user  = Auth::user();
        $notifs = $this->notifModel->getByUserId($user->id, 15);
        $unread = $this->notifModel->getUnreadCount($user->id);

        $formatted = array_map(function($n) {
            return [
                'id'         => (int)$n->id,
                'type'       => $n->type,
                'title'      => e($n->title),
                'message'    => e($n->message),
                'link'       => $n->link,
                'is_read'    => (bool)(int)$n->is_read,
                'created_at' => $n->created_at,
                'time_ago'   => $this->timeAgo($n->created_at),
            ];
        }, $notifs);

        return $this->json([
            'success' => true,
            'unread'  => $unread,
            'items'   => $formatted,
        ]);
    }

    /**
     * AJAX: Get unread count only (for navbar polling)
     * URL: /notification/count
     * Method: GET
     */
    public function count() {
        if (!Auth::check()) {
            return $this->json(['unread' => 0]);
        }

        $unread = $this->notifModel->getUnreadCount(Auth::id());
        return $this->json(['success' => true, 'unread' => $unread]);
    }

    /**
     * AJAX: Mark one notification as read
     * URL: /notification/read/:id
     * Method: POST
     */
    public function read($id = null) {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notifId = (int)$id;
        if (!$notifId) {
            return $this->json(['success' => false, 'message' => 'ID tidak valid.'], 400);
        }

        $ok = $this->notifModel->markAsRead($notifId, Auth::id());
        return $this->json(['success' => $ok]);
    }

    /**
     * AJAX: Mark all notifications as read
     * URL: /notification/readall
     * Method: POST
     */
    public function readall() {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $ok = $this->notifModel->markAllAsRead(Auth::id());
        return $this->json(['success' => $ok]);
    }

    /**
     * AJAX: Delete one notification
     * URL: /notification/delete/:id
     * Method: POST
     */
    public function delete($id = null) {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notifId = (int)$id;
        if (!$notifId) {
            return $this->json(['success' => false, 'message' => 'ID tidak valid.'], 400);
        }

        $ok = $this->notifModel->delete($notifId, Auth::id());
        return $this->json(['success' => $ok]);
    }

    /**
     * Helper: Convert timestamp to human-readable "time ago"
     *
     * @param string $datetime
     * @return string
     */
    private function timeAgo($datetime) {
        $now  = new DateTime();
        $then = new DateTime($datetime);
        $diff = $now->getTimestamp() - $then->getTimestamp();

        if ($diff < 60)        return 'Baru saja';
        if ($diff < 3600)      return (int)($diff / 60) . ' menit lalu';
        if ($diff < 86400)     return (int)($diff / 3600) . ' jam lalu';
        if ($diff < 604800)    return (int)($diff / 86400) . ' hari lalu';
        return $then->format('d M Y');
    }
}
