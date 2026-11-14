<?php
/**
 * Chat Controller
 * Project: Teknisi Rumah Tangga
 * Handles Internal Chat between Customer and Technician
 */

class ChatController extends Controller {
    private $chatModel;
    private $notifModel;

    public function __construct() {
        $this->chatModel = $this->model('ChatModel');
        $this->notifModel = $this->model('NotificationModel');
    }

    /**
     * Display list of conversations for current user
     * URL: /chat
     */
    public function index() {
        Auth::requireRole(['customer', 'technician']);
        $user = Auth::user();

        $conversations = $this->chatModel->getConversationsByUserId($user->id);

        $data = [
            'title' => 'Chat & Pesan Internal - ' . APP_NAME,
            'currentPage' => 'chat',
            'user' => $user,
            'conversations' => $conversations
        ];

        $this->view('chat/index', $data);
    }

    /**
     * Open or create chat room for a specific post
     * URL: /chat/open/:postId
     *
     * @param int|string $postId
     */
    public function open($postId = null) {
        Auth::requireRole(['customer', 'technician']);
        $user = Auth::user();

        if (!$postId) {
            redirect('chat');
        }

        $postId = (int)$postId;
        $postModel = $this->model('PostModel');
        $post = $postModel->getPostById($postId);

        if (!$post) {
            set_flash('danger', 'Postingan keluhan tidak ditemukan.');
            redirect('chat');
        }

        $customerId = (int)$post->customer_id;
        $technicianId = null;

        if ($user->role === 'technician') {
            $technicianId = (int)$user->id;
            // Verify technician is assigned or has offered
            $isAssigned = $postModel->isTechnicianAssignedToPost($postId, $technicianId);
            $hasResponded = $postModel->hasTechnicianResponded($postId, $technicianId);
            if (!$isAssigned && !$hasResponded) {
                set_flash('danger', 'Anda belum mengajukan tawaran bantuan pada postingan ini.');
                redirect('posts/detail/' . $postId);
            }
        } else {
            // Customer role
            if ((int)$user->id !== $customerId) {
                set_flash('danger', 'Akses ditolak.');
                redirect('posts/my');
            }
            $selectedResp = $postModel->getSelectedResponseByPostId($postId);
            if ($selectedResp) {
                $technicianId = (int)$selectedResp->technician_id;
            }
        }

        if (!$technicianId) {
            set_flash('warning', 'Percakapan belum dapat dibuka karena belum ada teknisi yang dipilih.');
            redirect('posts/detail/' . $postId);
        }

        $conversation = $this->chatModel->getOrCreateConversation($postId, $customerId, $technicianId);
        if ($conversation) {
            redirect('chat/room/' . $conversation->id);
        }

        set_flash('danger', 'Gagal memuat ruang percakapan.');
        redirect('chat');
    }

    /**
     * Display Chat Room
     * URL: /chat/room/:id
     *
     * @param int|string $id
     */
    public function room($id = null) {
        Auth::requireRole(['customer', 'technician']);
        $user = Auth::user();

        if (!$id) {
            redirect('chat');
        }

        $conversationId = (int)$id;
        $conversation = $this->chatModel->getConversationById($conversationId);

        if (!$conversation) {
            set_flash('danger', 'Percakapan tidak ditemukan.');
            redirect('chat');
        }

        // Security check: User must be a participant (customer or technician)
        if (!$this->chatModel->isParticipant($conversationId, $user->id)) {
            set_flash('danger', 'Akses ditolak! Anda tidak memiliki izin untuk membuka percakapan ini.');
            redirect('chat');
        }

        // Mark unread messages from partner as read
        $this->chatModel->markMessagesAsRead($conversationId, $user->id);

        // Fetch all messages in this conversation
        $messages = $this->chatModel->getMessagesByConversationId($conversationId);

        // Determine partner info
        if ((int)$user->id === (int)$conversation->customer_id) {
            $partnerName = $conversation->technician_name;
            $partnerWhatsapp = $conversation->technician_whatsapp;
            $partnerRole = 'technician';
            $partnerRoleLabel = 'Mitra Teknisi';
        } else {
            $partnerName = $conversation->customer_name;
            $partnerWhatsapp = $conversation->customer_whatsapp;
            $partnerRole = 'customer';
            $partnerRoleLabel = 'Pelanggan';
        }

        // Build WhatsApp message and URL
        $waMessage = "Halo " . $partnerName . ", saya " . $user->name . " terkait perbaikan '" . $conversation->post_title . "' di Teknisi Rumah Tangga.";
        $waUrl = wa_url($partnerWhatsapp, $waMessage);

        // Fetch latest price negotiation for this conversation
        $latestNegotiation = $this->chatModel->getLatestNegotiation($conversationId);

        $data = [
            'title' => 'Chat dengan ' . e($partnerName) . ' - ' . APP_NAME,
            'currentPage' => 'chat_room',
            'user' => $user,
            'conversation' => $conversation,
            'partnerName' => $partnerName,
            'partnerWhatsapp' => $partnerWhatsapp,
            'partnerRole' => $partnerRole,
            'partnerRoleLabel' => $partnerRoleLabel,
            'waUrl' => $waUrl,
            'messages' => $messages,
            'latestNegotiation' => $latestNegotiation
        ];

        $this->view('chat/room', $data);
    }

    /**
     * AJAX Endpoint: Send message
     * URL: /chat/send
     * Method: POST
     */
    public function send() {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Method not allowed.'], 405);
        }

        $user = Auth::user();

        // Support both form-urlencoded and JSON payload
        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);

        $csrfToken = $_POST['csrf_token'] ?? ($jsonData['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
        if (!verify_csrf_token($csrfToken)) {
            return $this->json(['success' => false, 'message' => 'Token keamanan (CSRF) tidak valid.'], 403);
        }

        $conversationId = (int)($_POST['conversation_id'] ?? ($jsonData['conversation_id'] ?? 0));
        $messageText = trim((string)($_POST['message'] ?? ($jsonData['message'] ?? '')));

        if (!$conversationId) {
            return $this->json(['success' => false, 'message' => 'ID percakapan tidak valid.'], 400);
        }

        if (empty($messageText)) {
            return $this->json(['success' => false, 'message' => 'Pesan tidak boleh kosong.'], 422);
        }

        if (mb_strlen($messageText) > 2000) {
            return $this->json(['success' => false, 'message' => 'Pesan maksimal 2000 karakter.'], 422);
        }

        // Security check: Must be participant
        if (!$this->chatModel->isParticipant($conversationId, $user->id)) {
            return $this->json(['success' => false, 'message' => 'Akses ditolak. Anda bukan peserta percakapan ini.'], 403);
        }

        // Save message (sender_id strictly from Auth::user()->id)
        $savedMessage = $this->chatModel->sendMessage($conversationId, $user->id, $messageText);

        if ($savedMessage) {
            return $this->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim.',
                'data' => [
                    'id' => (int)$savedMessage->id,
                    'conversation_id' => (int)$savedMessage->conversation_id,
                    'sender_id' => (int)$savedMessage->sender_id,
                    'sender_name' => e($savedMessage->sender_name),
                    'sender_role' => $savedMessage->sender_role,
                    'message' => e($savedMessage->message),
                    'is_read' => (int)$savedMessage->is_read,
                    'created_at' => $savedMessage->created_at,
                    'time_formatted' => date('H:i', strtotime($savedMessage->created_at))
                ]
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Gagal menyimpan pesan.'], 500);
    }

    /**
     * AJAX Endpoint: Fetch new messages for polling
     * URL: /chat/fetch/:conversationId?after_id=:afterId
     * Method: GET
     *
     * @param int|string $conversationId
     */
    public function fetch($conversationId = null) {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $conversationId = (int)$conversationId;

        if (!$conversationId || !$this->chatModel->isParticipant($conversationId, $user->id)) {
            return $this->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $afterId = (int)($_GET['after_id'] ?? 0);
        $newMessages = $this->chatModel->getNewMessages($conversationId, $afterId);

        // Mark incoming messages as read
        if (!empty($newMessages)) {
            $this->chatModel->markMessagesAsRead($conversationId, $user->id);
        }

        $formatted = array_map(function($msg) use ($user) {
            return [
                'id' => (int)$msg->id,
                'conversation_id' => (int)$msg->conversation_id,
                'sender_id' => (int)$msg->sender_id,
                'sender_name' => e($msg->sender_name),
                'sender_role' => $msg->sender_role,
                'message' => e($msg->message),
                'is_read' => (int)$msg->is_read,
                'is_me' => ((int)$msg->sender_id === (int)$user->id),
                'created_at' => $msg->created_at,
                'time_formatted' => date('H:i', strtotime($msg->created_at))
            ];
        }, $newMessages);

        return $this->json([
            'success' => true,
            'messages' => $formatted
        ]);
    }

    /**
     * AJAX Endpoint: Mark messages as read
     * URL: /chat/read/:conversationId
     * Method: POST
     *
     * @param int|string $conversationId
     */
    public function read($conversationId = null) {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $conversationId = (int)$conversationId;

        if (!$conversationId || !$this->chatModel->isParticipant($conversationId, $user->id)) {
            return $this->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $this->chatModel->markMessagesAsRead($conversationId, $user->id);

        return $this->json(['success' => true]);
    }

    /**
     * AJAX Endpoint: Propose a price negotiation
     * URL: /chat/negotiate
     * Method: POST
     */
    public function negotiate() {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Metode tidak diizinkan.'], 405);
        }

        $user = Auth::user();

        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);

        $csrfToken = $_POST['csrf_token'] ?? ($jsonData['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
        if (!verify_csrf_token($csrfToken)) {
            return $this->json(['success' => false, 'message' => 'Sesi tidak valid (CSRF token mismatch).'], 403);
        }

        $conversationId = (int)($_POST['conversation_id'] ?? ($jsonData['conversation_id'] ?? 0));
        $price = (int)($_POST['price'] ?? ($jsonData['price'] ?? 0));
        $note = trim((string)($_POST['note'] ?? ($jsonData['note'] ?? '')));

        if (!$conversationId || !$this->chatModel->isParticipant($conversationId, $user->id)) {
            return $this->json(['success' => false, 'message' => 'Akses percakapan ditolak.'], 403);
        }

        if ($price <= 0) {
            return $this->json(['success' => false, 'message' => 'Nominal harga penawaran harus lebih dari Rp 0.'], 422);
        }

        $conversation = $this->chatModel->getConversationById($conversationId);
        if (!$conversation) {
            return $this->json(['success' => false, 'message' => 'Percakapan tidak ditemukan.'], 404);
        }

        // Check if post is already completed
        if (in_array(strtolower($conversation->post_status ?? ''), ['completed', 'selesai'], true)) {
            return $this->json(['success' => false, 'message' => 'Status postingan sudah selesai. Kesepakatan harga sudah final dan tidak dapat dinegosiasikan lagi.'], 422);
        }

        $nego = $this->chatModel->createNegotiation($conversationId, (int)$conversation->post_id, (int)$user->id, $price, $note);
        if ($nego) {
            // Notify partner
            try {
                $recipientId = ((int)$user->id === (int)$conversation->customer_id) 
                    ? (int)$conversation->technician_id 
                    : (int)$conversation->customer_id;

                $formattedPrice = 'Rp ' . number_format($price, 0, ',', '.');
                $this->notifModel->create(
                    $recipientId,
                    'price_negotiation',
                    'Penawaran Harga Baru!',
                    "{$user->name} mengajukan penawaran harga kesepakatan {$formattedPrice} untuk '{$conversation->post_title}'.",
                    'chat/room/' . $conversationId
                );
            } catch (\Throwable $e) {}

            return $this->json([
                'success' => true,
                'message' => 'Penawaran harga berhasil diajukan ke ruang percakapan.',
                'data' => $nego
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Gagal mengajukan penawaran harga.'], 500);
    }

    /**
     * AJAX Endpoint: Accept or reject a price negotiation
     * URL: /chat/negotiate-action
     * Method: POST
     */
    public function negotiateAction() {
        if (!Auth::check()) {
            return $this->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Metode tidak diizinkan.'], 405);
        }

        $user = Auth::user();

        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);

        $csrfToken = $_POST['csrf_token'] ?? ($jsonData['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
        if (!verify_csrf_token($csrfToken)) {
            return $this->json(['success' => false, 'message' => 'Sesi tidak valid (CSRF token mismatch).'], 403);
        }

        $negoId = (int)($_POST['negotiation_id'] ?? ($jsonData['negotiation_id'] ?? 0));
        $action = trim((string)($_POST['action'] ?? ($jsonData['action'] ?? '')));

        if (!$negoId || !in_array($action, ['accept', 'reject'], true)) {
            return $this->json(['success' => false, 'message' => 'Parameter tidak valid.'], 422);
        }

        $nego = $this->chatModel->getNegotiationById($negoId);
        if (!$nego) {
            return $this->json(['success' => false, 'message' => 'Data penawaran tidak ditemukan.'], 404);
        }

        if (!$this->chatModel->isParticipant($nego->conversation_id, $user->id)) {
            return $this->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // Only the recipient (not the one who proposed) can accept or reject
        if ((int)$nego->sender_id === (int)$user->id) {
            return $this->json(['success' => false, 'message' => 'Anda tidak dapat menyetujui tawaran yang Anda ajukan sendiri.'], 422);
        }

        // Check if post is already completed
        $conversation = $this->chatModel->getConversationById((int)$nego->conversation_id);
        if ($conversation && in_array(strtolower($conversation->post_status ?? ''), ['completed', 'selesai'], true)) {
            return $this->json(['success' => false, 'message' => 'Status postingan sudah selesai. Kesepakatan harga sudah final.'], 422);
        }

        $ok = $this->chatModel->respondNegotiation($negoId, $action, (int)$user->id);
        if ($ok) {
            // Notify the original sender
            try {
                $statusText = ($action === 'accept') ? 'menyetujui' : 'menolak';
                $formattedPrice = 'Rp ' . number_format($nego->proposed_price, 0, ',', '.');
                $this->notifModel->create(
                    (int)$nego->sender_id,
                    'price_negotiation',
                    ($action === 'accept') ? 'Tawaran Harga Disepakati (Deal)!' : 'Tawaran Harga Ditolak',
                    "{$user->name} {$statusText} tawaran harga {$formattedPrice}.",
                    'chat/room/' . $nego->conversation_id
                );
            } catch (\Throwable $e) {}

            return $this->json([
                'success' => true,
                'action' => $action,
                'message' => ($action === 'accept') ? 'Kesepakatan harga berhasil disetujui (Deal)!' : 'Tawaran harga ditolak.'
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Gagal memproses kesepakatan harga.'], 500);
    }
}
