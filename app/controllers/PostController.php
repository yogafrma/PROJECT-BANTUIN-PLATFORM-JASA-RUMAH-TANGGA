<?php
/**
 * Post Controller
 * Project: Teknisi Rumah Tangga
 * Handles Customer Complaint Posts, Technician Interactions & Selections
 */

class PostController extends Controller {
    private $postModel;
    private $categoryModel;
    private $consultationModel;
    private $chatModel;
    private $userModel;
    private $notifModel;

    public function __construct() {
        $this->postModel = $this->model('PostModel');
        $this->categoryModel = $this->model('CategoryModel');
        $this->consultationModel = $this->model('ConsultationModel');
        $this->chatModel = $this->model('ChatModel');
        $this->userModel = $this->model('UserModel');
        $this->notifModel = $this->model('NotificationModel');
    }

    /**
     * Public Board of Active Posts for Technicians (Status: 'Mencari Teknisi')
     * URL: /posts
     *
     * @return void
     */
    public function index() {
        Auth::requireLogin();
        $user = Auth::user();

        $selectedCategoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $categories = $this->categoryModel->getActiveCategories();
        $posts = $this->postModel->getActivePostsForTechnicians($selectedCategoryId, 20);

        $data = [
            'title' => 'Lowongan Pekerjaan Aktif - ' . APP_NAME,
            'currentPage' => 'posts_board',
            'user' => $user,
            'categories' => $categories,
            'posts' => $posts,
            'selectedCategoryId' => $selectedCategoryId
        ];

        $this->view('posts/index', $data);
    }

    /**
     * Customer's Own Posts List
     * URL: /posts/my
     *
     * @return void
     */
    public function my() {
        Auth::requireRole(['customer']);
        $user = Auth::user();

        $myPosts = $this->postModel->getPostsByCustomerId($user->id);

        $data = [
            'title' => 'Postingan Keluhan Saya - ' . APP_NAME,
            'currentPage' => 'my_posts',
            'user' => $user,
            'posts' => $myPosts
        ];

        $this->view('posts/my_posts', $data);
    }

    /**
     * Create Post Form (Pre-filled from AI Consultation or manual)
     * URL: /posts/create
     *
     * @return void
     */
    public function create() {
        Auth::requireRole(['customer']);
        $user = Auth::user();
        $categories = $this->categoryModel->getActiveCategories();
        $errors = [];

        // Pre-fill from AI Consultation if query param supplied
        $consultationId = isset($_GET['consultation_id']) ? (int)$_GET['consultation_id'] : null;
        $consultation = $consultationId ? $this->consultationModel->getConsultationById($consultationId) : null;

        $prefillTitle = '';
        $prefillDescription = '';
        $prefillCategoryId = 2; // Default AC
        $prefillImage = null;
        $prefillAiAnalysis = null;
        $prefillMinPrice = 0;
        $prefillMaxPrice = 0;

        if ($consultation && (int)$consultation->customer_id === (int)$user->id) {
            $aiData = $consultation->ai_data;
            $prefillTitle = $aiData['draft_post_title'] ?? ('Perbaikan ' . ($aiData['category_name'] ?? 'Rumah'));
            $prefillDescription = $aiData['draft_post_description'] ?? $consultation->damage_detail;
            $prefillImage = $consultation->image;
            $prefillAiAnalysis = $aiData['possible_damage'] ?? null;
            $prefillMinPrice = (int) ($aiData['estimated_min_price'] ?? 0);
            $prefillMaxPrice = (int) ($aiData['estimated_max_price'] ?? 0);

            // Match category slug to category ID
            if (!empty($aiData['category_slug'])) {
                $matchedCat = $this->categoryModel->getCategoryBySlug($aiData['category_slug']);
                if ($matchedCat) {
                    $prefillCategoryId = $matchedCat->id;
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                $errors['general'] = 'Permintaan tidak valid (CSRF token mismatch). Silakan muat ulang halaman.';
            } else {
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $categoryId = (int) ($_POST['category_id'] ?? 2);
                $aiAnalysis = trim($_POST['ai_analysis'] ?? '');
                $minPrice = (int) ($_POST['estimated_min_price'] ?? 0);
                $maxPrice = (int) ($_POST['estimated_max_price'] ?? 0);
                $imagePath = trim($_POST['existing_image'] ?? '');

            // Validation
            if (empty($title)) {
                $errors['title'] = 'Judul postingan wajib diisi.';
            } elseif (strlen($title) < 5) {
                $errors['title'] = 'Judul minimal 5 karakter.';
            }

            if (empty($description)) {
                $errors['description'] = 'Deskripsi keluhan wajib diisi.';
            } elseif (strlen($description) < 10) {
                $errors['description'] = 'Deskripsi keluhan minimal 10 karakter.';
            }

            // Handle optional new image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['image'];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($ext, $allowedExtensions, true) || !in_array($mimeType, $allowedMimeTypes, true)) {
                    $errors['image'] = 'Format foto tidak valid. Pastikan file berupa JPG, PNG, atau WEBP.';
                } elseif ($file['size'] > 5 * 1024 * 1024) {
                    $errors['image'] = 'Ukuran foto maksimal 5 MB.';
                } elseif ($file['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../public/uploads/consultations/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = 'post_' . $user->id . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                        $imagePath = 'uploads/consultations/' . $fileName;
                    }
                }
            }

            if (empty($errors)) {
                $newPostId = $this->postModel->createPost([
                    'customer_id' => $user->id,
                    'consultation_id' => $consultationId,
                    'category_id' => $categoryId,
                    'title' => $title,
                    'description' => $description,
                    'image' => $imagePath ?: null,
                    'ai_analysis' => $aiAnalysis ?: null,
                    'estimated_min_price' => $minPrice,
                    'estimated_max_price' => $maxPrice
                ]);

                if ($newPostId) {
                    // 1. Kirim Notifikasi In-App & Email ke Mitra Teknisi
                    try {
                        require_once __DIR__ . '/../services/EmailService.php';
                        $category = $this->categoryModel->getCategoryById($categoryId);
                        $categoryName = $category ? (is_object($category) ? $category->name : ($category['name'] ?? 'Layanan')) : 'Layanan';
                        $allTechs = $this->userModel->getAllUsers('technician');

                        if (!empty($allTechs)) {
                            foreach ($allTechs as $tech) {
                                $techId = is_object($tech) ? (int)$tech->id : (int)$tech['id'];
                                $techName = is_object($tech) ? ($tech->name ?? 'Mitra Teknisi') : ($tech['name'] ?? 'Mitra Teknisi');
                                $techEmail = is_object($tech) ? ($tech->email ?? '') : ($tech['email'] ?? '');

                                // In-App Notification
                                $this->notifModel->create(
                                    $techId,
                                    'new_post',
                                    'Postingan Permintaan Jasa Baru!',
                                    "Pelanggan '{$user->name}' membutuhkan teknisi untuk: \"{$title}\" ({$categoryName}). Segera ajukan tawaran bantuan!",
                                    'posts/detail/' . $newPostId
                                );

                                // Email Notification
                                if (!empty($techEmail)) {
                                    try {
                                        EmailService::sendNewPostNotificationToTechnician(
                                            $techEmail,
                                            $techName,
                                            $user->name,
                                            $title,
                                            $categoryName,
                                            base_url('posts/detail/' . $newPostId)
                                        );
                                    } catch (\Throwable $emErr) {}
                                }
                            }
                        }
                    } catch (\Throwable $notifErr) {
                        // Silent fail to ensure post creation finishes smoothly
                    }

                    set_flash('success', 'Postingan keluhan Anda berhasil dipublikasikan! Mitra teknisi akan segera melihat dan menawarkan bantuan.');
                    redirect('posts/detail/' . $newPostId);
                } else {
                    $errors['general'] = 'Gagal mempublikasikan postingan. Silakan coba lagi.';
                }
            }
            }
        }

        $data = [
            'title' => 'Publikasikan Postingan Keluhan - ' . APP_NAME,
            'currentPage' => 'posts_create',
            'user' => $user,
            'categories' => $categories,
            'consultation' => $consultation,
            'prefill' => [
                'title' => $prefillTitle,
                'description' => $prefillDescription,
                'category_id' => $prefillCategoryId,
                'image' => $prefillImage,
                'ai_analysis' => $prefillAiAnalysis,
                'min_price' => $prefillMinPrice,
                'max_price' => $prefillMaxPrice
            ],
            'errors' => $errors
        ];

        $this->view('posts/create', $data);
    }

    /**
     * Edit Post Form (Only owner Customer)
     * URL: /posts/edit/:id
     *
     * @param int|string $id
     * @return void
     */
    public function edit($id = null) {
        Auth::requireRole(['customer']);
        $user = Auth::user();

        if (!$id) {
            redirect('posts/my');
        }

        $post = $this->postModel->getPostById((int)$id);
        if (!$post || (int)$post->customer_id !== (int)$user->id) {
            set_flash('danger', 'Akses ditolak! Anda hanya dapat mengedit postingan milik Anda sendiri.');
            redirect('posts/my');
        }

        $categories = $this->categoryModel->getActiveCategories();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                $errors['general'] = 'Permintaan tidak valid (CSRF token mismatch). Silakan muat ulang halaman.';
            } else {
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $categoryId = (int) ($_POST['category_id'] ?? $post->category_id);
                $imagePath = $post->image;

            if (empty($title)) {
                $errors['title'] = 'Judul postingan wajib diisi.';
            }
            if (empty($description)) {
                $errors['description'] = 'Deskripsi keluhan wajib diisi.';
            }

            // Optional new image
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['image'];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($ext, $allowedExtensions, true) || !in_array($mimeType, $allowedMimeTypes, true)) {
                    $errors['image'] = 'Format foto tidak valid. Pastikan file berupa JPG, PNG, atau WEBP.';
                } elseif ($file['size'] > 5 * 1024 * 1024) {
                    $errors['image'] = 'Ukuran foto maksimal 5 MB.';
                } elseif ($file['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../public/uploads/consultations/';
                    $fileName = 'post_' . $user->id . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                        $imagePath = 'uploads/consultations/' . $fileName;
                    }
                }
            }

            if (empty($errors)) {
                $this->postModel->updatePost((int)$id, [
                    'category_id' => $categoryId,
                    'title' => $title,
                    'description' => $description,
                    'image' => $imagePath
                ]);

                set_flash('success', 'Postingan keluhan berhasil diperbarui.');
                redirect('posts/detail/' . $id);
            }
            }
        }

        $data = [
            'title' => 'Edit Postingan: ' . e($post->title) . ' - ' . APP_NAME,
            'currentPage' => 'posts_edit',
            'user' => $user,
            'post' => $post,
            'categories' => $categories,
            'errors' => $errors
        ];

        $this->view('posts/edit', $data);
    }

    /**
     * Post Detail View
     * URL: /posts/detail/:id
     *
     * @param int|string $id
     * @return void
     */
    public function detail($id = null) {
        Auth::requireLogin();
        $user = Auth::user();

        if (!$id) {
            redirect('posts');
        }

        $post = $this->postModel->getPostById((int)$id);
        if (!$post) {
            set_flash('danger', 'Postingan keluhan tidak ditemukan.');
            redirect('posts');
        }

        // Fetch technician responses for this post
        $responses = $this->postModel->getResponsesByPostId((int)$id);
        $hasResponded = false;
        $isSelectedTechnician = false;
        if ($user->role === 'technician') {
            $hasResponded = $this->postModel->hasTechnicianResponded((int)$id, $user->id);
            $isSelectedTechnician = $this->postModel->isTechnicianAssignedToPost((int)$id, $user->id);
        }

        $isOwner = ((int)$post->customer_id === (int)$user->id);

        // Fetch conversation if post already has a selected technician or conversation exists
        $conversation = $this->chatModel->getConversationByPostId((int)$id);

        // Fetch assigned technician & review data (if any)
        $assignedTechnician = $this->postModel->getAssignedTechnician((int)$id);
        $review = $this->postModel->getReviewByPostId((int)$id);
        $payment = $this->postModel->getPaymentByPostId((int)$id);

        $data = [
            'title' => e($post->title) . ' - ' . APP_NAME,
            'currentPage' => 'posts_detail',
            'user' => $user,
            'post' => $post,
            'responses' => $responses,
            'hasResponded' => $hasResponded,
            'isSelectedTechnician' => $isSelectedTechnician,
            'isOwner' => $isOwner,
            'conversation' => $conversation,
            'assignedTechnician' => $assignedTechnician,
            'review' => $review,
            'payment' => $payment
        ];

        $this->view('posts/detail', $data);
    }

    /**
     * Technician Action: "Saya Bisa Membantu" (Offer Assistance)
     * URL: /posts/offer/:id
     *
     * @param int|string $id
     * @return void
     */
    public function offer($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('posts');
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF).');
            redirect('posts/detail/' . $id);
        }
        
        Auth::requireRole(['technician']);
        $user = Auth::user();

        if (!$id) {
            redirect('posts');
        }

        $post = $this->postModel->getPostById((int)$id);
        if (!$post) {
            set_flash('danger', 'Postingan tidak ditemukan.');
            redirect('posts');
        }

        if ($post->status !== 'Mencari Teknisi') {
            set_flash('warning', 'Postingan ini sudah tidak menerima tawaran bantuan baru.');
            redirect('posts/detail/' . $id);
        }

        if ($this->postModel->hasTechnicianResponded((int)$id, $user->id)) {
            set_flash('info', 'Anda sudah pernah mengajukan tawaran bantuan untuk postingan ini.');
            redirect('posts/detail/' . $id);
        }

        $responseId = $this->postModel->createResponse((int)$id, $user->id);
        if ($responseId) {
            // 1. Kirim Notifikasi ke Customer pembuat postingan: "Ada Teknisi yang Siap Membantu!"
            try {
                $customer = $this->userModel->findById((int)$post->customer_id);
                if ($customer) {
                    // In-App Notification
                    $this->notifModel->create(
                        (int)$customer->id,
                        'new_response',
                        'Ada Teknisi yang Siap Membantu!',
                        "Mitra Teknisi {$user->name} siap membantu keluhan Anda: \"{$post->title}\". Klik di sini untuk melihat tawaran dan memilih teknisi.",
                        'posts/detail/' . $id
                    );

                    // Email Notification ke Customer
                    if (!empty($customer->email)) {
                        require_once __DIR__ . '/../services/EmailService.php';
                        try {
                            EmailService::sendOfferNotificationToCustomer(
                                $customer->email,
                                $customer->name,
                                $user->name,
                                $post->title,
                                base_url('posts/detail/' . $id)
                            );
                        } catch (\Throwable $emErr) {}
                    }
                }
            } catch (\Throwable $notifErr) {
                // Silently continue
            }

            set_flash('success', 'Tawaran bantuan Anda berhasil dikirim ke Pelanggan! Pelanggan akan melihat profil Anda dan dapat memilih Anda untuk pengerjaan.');
        } else {
            set_flash('danger', 'Gagal mengirimkan tawaran bantuan. Silakan coba lagi.');
        }

        redirect('posts/detail/' . $id);
    }

    /**
     * Customer Action: Select a Technician
     * URL: /posts/select/:postId/:responseId
     *
     * @param int|string $postId
     * @param int|string $responseId
     * @return void
     */
    public function select($postId = null, $responseId = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('posts/my');
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF).');
            redirect('posts/detail/' . $postId);
        }
        
        Auth::requireRole(['customer']);
        $user = Auth::user();

        if (!$postId || !$responseId) {
            redirect('posts/my');
        }

        $post = $this->postModel->getPostById((int)$postId);
        if (!$post || (int)$post->customer_id !== (int)$user->id) {
            set_flash('danger', 'Akses ditolak! Anda hanya dapat memilih teknisi untuk postingan milik Anda.');
            redirect('posts/my');
        }

        // Find selected technician ID from responses
        $responses = $this->postModel->getResponsesByPostId((int)$postId);
        $selectedTechId = null;
        foreach ($responses as $r) {
            if ((int)$r->id === (int)$responseId) {
                $selectedTechId = (int)$r->technician_id;
                break;
            }
        }

        if ($selectedTechId && $this->postModel->selectTechnicianResponse((int)$postId, (int)$responseId, $selectedTechId)) {
            // 1. Buat conversation jika belum ada / gunakan jika sudah ada
            $conversation = $this->chatModel->getOrCreateConversation((int)$postId, (int)$user->id, $selectedTechId);
            $convoId = is_object($conversation) ? ($conversation->id ?? 1) : ($conversation['id'] ?? 1);

            // 2. Kirim Notifikasi ke Teknisi & Customer (In-App & Email Real-Time)
            try {
                $techUser = $this->userModel->findById((int)$selectedTechId);
                require_once __DIR__ . '/../services/EmailService.php';

                // A. Notifikasi ke TEKNISI: "Customer Mengkonfirmasi Bantuan Anda!"
                if ($techUser) {
                    $this->notifModel->create(
                        (int)$selectedTechId,
                        'response_selected',
                        'Customer Mengkonfirmasi Bantuan Anda!',
                        "Pelanggan {$user->name} telah mengkonfirmasi bantuan Anda untuk keluhan: \"{$post->title}\". Silakan hubungi pelanggan melalui Chat Internal atau WhatsApp sekarang.",
                        'chat/room/' . $convoId
                    );

                    if (!empty($techUser->email)) {
                        try {
                            EmailService::sendConfirmationNotificationToTechnician(
                                $techUser->email,
                                $techUser->name,
                                $user->name,
                                $post->title,
                                base_url('chat/room/' . $convoId)
                            );
                        } catch (\Throwable $emErr) {
                            error_log('[Email Error] Confirmation to Tech: ' . $emErr->getMessage());
                        }
                    }
                }

                // B. Notifikasi ke CUSTOMER: "Konfirmasi Bantuan Berhasil!"
                $this->notifModel->create(
                    (int)$user->id,
                    'response_selected',
                    'Konfirmasi Teknisi Berhasil!',
                    "Anda telah mengonfirmasi Teknisi " . ($techUser ? $techUser->name : 'Mitra') . " untuk keluhan: \"{$post->title}\". Ruang Chat Internal dan WhatsApp telah siap digunakan.",
                    'chat/room/' . $convoId
                );

                if (!empty($user->email)) {
                    try {
                        EmailService::sendConfirmationSuccessToCustomer(
                            $user->email,
                            $user->name,
                            $techUser ? $techUser->name : 'Mitra Teknisi',
                            $post->title,
                            base_url('chat/room/' . $convoId),
                            $techUser ? ($techUser->whatsapp ?? '') : ''
                        );
                    } catch (\Throwable $emErr) {
                        error_log('[Email Error] Confirmation to Cust: ' . $emErr->getMessage());
                    }
                }
            } catch (\Throwable $notifErr) {
                error_log('[Notification Error] select technician: ' . $notifErr->getMessage());
            }

            set_flash('success', 'Mitra Teknisi berhasil dipilih! Percakapan Chat Internal dan WhatsApp telah siap digunakan.');
        } else {
            set_flash('danger', 'Gagal memilih teknisi.');
        }

        redirect('posts/detail/' . $postId);
    }

    /**
     * Update Status of a Post (Selesai / Dibatalkan / Ada Teknisi)
     * URL: /posts/status/:postId
     * Supported: Customer (owner) or Technician (assigned)
     *
     * @param int|string $postId
     * @return void
     */
    public function status($postId = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('home');
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF token mismatch).');
            redirect('posts/detail/' . $postId);
        }
        
        Auth::requireRole(['customer', 'technician']);
        $user = Auth::user();

        if (!$postId) {
            redirect($user->role === 'technician' ? 'technician' : 'posts/my');
        }

        $post = $this->postModel->getPostById((int)$postId);
        if (!$post) {
            set_flash('danger', 'Postingan keluhan tidak ditemukan.');
            redirect($user->role === 'technician' ? 'technician' : 'posts/my');
        }

        $newStatus = trim($_POST['status'] ?? '');

        if ($user->role === 'customer') {
            if ((int)$post->customer_id !== (int)$user->id) {
                set_flash('danger', 'Akses ditolak. Anda bukan pemilik postingan ini.');
                redirect('posts/my');
            }

            if (in_array($newStatus, ['Selesai', 'Dibatalkan', 'Mencari Teknisi', 'Ada Teknisi'], true)) {
                $this->postModel->updateStatus((int)$postId, $newStatus);
                set_flash('success', "Status postingan berhasil diubah menjadi '{$newStatus}'.");
            }
            redirect('posts/detail/' . $postId);

        } elseif ($user->role === 'technician') {
            $isAssigned = $this->postModel->isTechnicianAssignedToPost((int)$postId, $user->id);
            if (!$isAssigned) {
                set_flash('danger', 'Akses ditolak. Anda bukan teknisi yang ditugaskan untuk pekerjaan ini.');
                redirect('technician');
            }

            if ($newStatus === 'Selesai') {
                $this->postModel->updateStatus((int)$postId, 'Selesai');

                // Notify Customer: In-App & Email
                try {
                    $this->notifModel->create(
                        (int)$post->customer_id,
                        'job_completed',
                        'Pekerjaan Telah Selesai!',
                        "Mitra Teknisi {$user->name} telah menyelesaikan pengerjaan untuk keluhan: \"{$post->title}\". Silakan periksa hasil pengerjaan.",
                        'posts/detail/' . $postId
                    );

                    $customerUser = $this->userModel->findById((int)$post->customer_id);
                    if ($customerUser && !empty($customerUser->email)) {
                        require_once __DIR__ . '/../services/EmailService.php';
                        EmailService::sendJobCompletedNotification(
                            $customerUser->email,
                            $customerUser->name,
                            $user->name,
                            $post->title,
                            base_url('posts/detail/' . $postId)
                        );
                    }
                } catch (\Throwable $notifEx) {
                    error_log('[Notification Error] job completed: ' . $notifEx->getMessage());
                }

                set_flash('success', "Pekerjaan '{$post->title}' berhasil diselesaikan! Status telah diperbarui menjadi Selesai.");
            }

            redirect('technician');
        }
    }

    /**
     * Submit or Update Review for Completed Post
     * URL: /posts/review/:postId
     *
     * @param int|string $postId
     * @return void
     */
    public function review($postId = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('posts/my');
        }

        Auth::requireRole(['customer']);
        $user = Auth::user();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF token mismatch).');
            redirect('posts/detail/' . $postId);
        }

        if (!$postId) {
            redirect('posts/my');
        }

        $post = $this->postModel->getPostById((int)$postId);
        if (!$post || (int)$post->customer_id !== (int)$user->id) {
            set_flash('danger', 'Akses ditolak. Anda hanya dapat memberi ulasan pada postingan Anda sendiri.');
            redirect('posts/my');
        }

        if ($post->status !== 'Selesai') {
            set_flash('warning', 'Ulasan hanya dapat diberikan setelah status pengerjaan telah Selesai.');
            redirect('posts/detail/' . $postId);
        }

        $technicianId = (int)($_POST['technician_id'] ?? 0);
        if (!$technicianId) {
            $assignedTech = $this->postModel->getAssignedTechnician((int)$postId);
            if ($assignedTech) {
                $technicianId = (int)$assignedTech->user_id;
            }
        }

        if (!$technicianId) {
            set_flash('danger', 'Mitra teknisi pengerjaan tidak ditemukan.');
            redirect('posts/detail/' . $postId);
        }

        $rating = (int)($_POST['rating'] ?? 5);
        $rating = max(1, min(5, $rating));
        $comment = trim($_POST['comment'] ?? '');

        if (empty($comment)) {
            $comment = 'Pengerjaan sangat memuaskan, rapi, dan cepat. Sangat direkomendasikan!';
        }

        $saved = $this->postModel->saveReview((int)$postId, (int)$user->id, $technicianId, $rating, $comment);
        if ($saved) {
            // Send In-App Notification to Technician
            try {
                $this->notifModel->create(
                    $technicianId,
                    'new_review',
                    'Ulasan Baru dari Pelanggan!',
                    "Pelanggan '{$user->name}' memberikan penilaian {$rating}/5 bintang untuk pengerjaan keluhan: \"{$post->title}\".",
                    'posts/detail/' . $postId
                );
            } catch (\Throwable $e) {}

            set_flash('success', 'Terima kasih! Ulasan dan rating penilaian performa teknisi berhasil disimpan.');
        } else {
            set_flash('danger', 'Gagal menyimpan ulasan. Silakan coba lagi.');
        }

        redirect('posts/detail/' . $postId);
    }

    /**
     * Customer Confirm Payment & Complete Post (Gojek/Grab style transaction completion)
     * URL: /posts/payment/:postId
     * Method: POST
     *
     * @param int|string $postId
     * @return void
     */
    public function payment($postId = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('posts/my');
        }

        Auth::requireRole(['customer']);
        $user = Auth::user();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrfToken)) {
            set_flash('danger', 'Permintaan tidak valid (CSRF token mismatch).');
            redirect('posts/detail/' . $postId);
        }

        if (!$postId) {
            redirect('posts/my');
        }

        $post = $this->postModel->getPostById((int)$postId);
        if (!$post || (int)$post->customer_id !== (int)$user->id) {
            set_flash('danger', 'Akses ditolak. Anda bukan pemilik postingan ini.');
            redirect('posts/my');
        }

        // Determine assigned technician
        $assignedTech = $this->postModel->getAssignedTechnician((int)$postId);
        $technicianId = $assignedTech ? (int)$assignedTech->user_id : 0;

        if (!$technicianId) {
            set_flash('danger', 'Mitra teknisi pengerjaan belum ditentukan.');
            redirect('posts/detail/' . $postId);
        }

        $amount = (int)($_POST['amount'] ?? 0);
        if ($amount <= 0) {
            $amount = (int)($post->agreed_price ?: ($post->estimated_min_price ?: 100000));
        }

        $paymentMethod = trim($_POST['payment_method'] ?? 'cash');
        if (!in_array($paymentMethod, ['qris', 'cash', 'transfer'], true)) {
            $paymentMethod = 'cash';
        }

        $notes = trim($_POST['notes'] ?? '');

        $payment = $this->postModel->savePayment((int)$postId, (int)$user->id, $technicianId, $amount, $paymentMethod, $notes);
        if ($payment) {
            // Send In-App & Email notification to technician
            try {
                $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');
                $methodLabel = ($paymentMethod === 'qris') ? 'QRIS' : (($paymentMethod === 'cash') ? 'Tunai (Cash)' : 'Transfer Bank');

                $this->notifModel->create(
                    $technicianId,
                    'payment_received',
                    'Pembayaran Selesai Diterima!',
                    "Pelanggan '{$user->name}' telah menyelesaikan pembayaran sebesar {$formattedAmount} via {$methodLabel} untuk '{$post->title}'. Invoice: {$payment->invoice_no}.",
                    'posts/detail/' . $postId
                );
            } catch (\Throwable $e) {}

            set_flash('success', "Pembayaran sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil dikonfirmasi! Status pengerjaan kini Selesai.");
        } else {
            set_flash('danger', 'Gagal memproses konfirmasi pembayaran. Silakan coba lagi.');
        }

        redirect('posts/detail/' . $postId);
    }
}
