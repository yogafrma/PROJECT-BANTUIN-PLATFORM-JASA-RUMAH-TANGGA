<?php
/**
 * Customer Controller
 * Project: Teknisi Rumah Tangga
 * Protected Area: Customer Role Only
 */

require_once '../app/services/AIService.php';

class CustomerController extends Controller {
    private $categoryModel;
    private $postModel;
    private $techProfileModel;
    private $consultationModel;
    private $aiService;

    public function __construct() {
        Auth::requireRole(['customer']);
        $this->categoryModel = $this->model('CategoryModel');
        $this->postModel = $this->model('PostModel');
        $this->techProfileModel = $this->model('TechnicianProfileModel');
        $this->consultationModel = $this->model('ConsultationModel');
        $this->aiService = new AIService();
    }

    /**
     * Customer Dashboard Overview
     *
     * @return void
     */
    public function index() {
        $user = Auth::user();
        $categories = $this->categoryModel->getAllCategories();
        $myPosts = $this->postModel->getPostsByCustomerId($user->id);
        $recommendedTechs = $this->techProfileModel->getRecommendedTechnicians(4);

        $data = [
            'title' => 'Dashboard Pelanggan - ' . APP_NAME,
            'currentPage' => 'customer_dashboard',
            'user' => $user,
            'categories' => $categories,
            'myPosts' => $myPosts,
            'recommendedTechs' => $recommendedTechs
        ];

        $this->view('customer/dashboard', $data);
    }

    /**
     * AI Consultation Form & Result Handler
     * URL: /customer/consultation
     *
     * @return void
     */
    public function consultation() {
        $user = Auth::user();
        $errors = [];
        $result = null;
        $consultationId = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                $errors['general'] = 'Permintaan tidak valid (CSRF token mismatch). Silakan muat ulang halaman.';
            } else {
                $damageDetail = trim($_POST['damage_detail'] ?? '');
                $chronology = trim($_POST['chronology'] ?? '');
                $uploadedImageRelativePath = null;

            // 1. Validation
            if (empty($damageDetail)) {
                $errors['damage_detail'] = 'Detail keluhan / kerusakan wajib diisi.';
            } elseif (strlen($damageDetail) < 10) {
                $errors['damage_detail'] = 'Detail keluhan minimal 10 karakter agar AI dapat menganalisis dengan akurat.';
            }

            if (empty($chronology)) {
                $errors['chronology'] = 'Kronologi kejadian wajib diisi.';
            } elseif (strlen($chronology) < 10) {
                $errors['chronology'] = 'Kronologi kejadian minimal 10 karakter.';
            }

            // 2. Handle Image Upload if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['image'];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($fileExt, $allowedExtensions, true) || !in_array($mimeType, $allowedMimeTypes, true)) {
                    $errors['image'] = 'Format foto tidak valid. Pastikan file berupa JPG, PNG, atau WEBP.';
                } elseif ($file['size'] > 5 * 1024 * 1024) {
                    $errors['image'] = 'Ukuran foto maksimal 5 MB.';
                } elseif ($file['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../public/uploads/consultations/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = 'consultation_' . $user->id . '_' . time() . '.' . $fileExt;
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $uploadedImageRelativePath = 'uploads/consultations/' . $fileName;
                    } else {
                        $errors['image'] = 'Gagal mengunggah foto. Silakan coba lagi.';
                    }
                }
            }

            // 3. Process AI Diagnosis
            if (empty($errors)) {
                $aiDiagnosis = $this->aiService->diagnose($damageDetail, $chronology, $uploadedImageRelativePath);

                // Save to database
                $newConsultationId = $this->consultationModel->createConsultation([
                    'customer_id' => $user->id,
                    'image' => $uploadedImageRelativePath,
                    'damage_detail' => $damageDetail,
                    'chronology' => $chronology,
                    'ai_result' => $aiDiagnosis,
                    'estimated_min_price' => $aiDiagnosis['estimated_min_price'] ?? 0,
                    'estimated_max_price' => $aiDiagnosis['estimated_max_price'] ?? 0
                ]);

                if ($newConsultationId) {
                    $consultationId = $newConsultationId;
                    $result = $aiDiagnosis;
                    $result['image'] = $uploadedImageRelativePath;
                    $result['damage_detail'] = $damageDetail;
                    $result['chronology'] = $chronology;

                    set_flash('success', 'Analisis Konsultasi AI berhasil dibuat!');
                } else {
                    $errors['general'] = 'Gagal menyimpan riwayat konsultasi. Silakan coba kembali.';
                }
            }
            }
        }

        // Get past consultation history for this customer
        $history = $this->consultationModel->getConsultationsByCustomerId($user->id);
        $recommendedTechs = $this->techProfileModel->getRecommendedTechnicians(3);

        $data = [
            'title' => 'Konsultasi AI Diagnosis Kerusakan - ' . APP_NAME,
            'currentPage' => 'customer_consultation',
            'user' => $user,
            'errors' => $errors,
            'result' => $result,
            'consultationId' => $consultationId,
            'history' => $history,
            'recommendedTechs' => $recommendedTechs
        ];

        $this->view('customer/consultation', $data);
    }

    /**
     * View Consultation Detail by ID
     * URL: /customer/consultation/detail/:id
     *
     * @param int|string $id
     * @return void
     */
    public function consultationDetail($id = null) {
        $user = Auth::user();
        if (!$id) {
            redirect('customer/consultation');
        }

        $consultation = $this->consultationModel->getConsultationById((int)$id);
        if (!$consultation || (int)$consultation->customer_id !== (int)$user->id) {
            set_flash('danger', 'Data konsultasi tidak ditemukan.');
            redirect('customer/consultation');
        }

        $history = $this->consultationModel->getConsultationsByCustomerId($user->id);
        $recommendedTechs = $this->techProfileModel->getRecommendedTechnicians(3);

        $result = $consultation->ai_data;
        $result['image'] = $consultation->image;
        $result['damage_detail'] = $consultation->damage_detail;
        $result['chronology'] = $consultation->chronology;

        $data = [
            'title' => 'Detail Konsultasi AI #' . $consultation->id . ' - ' . APP_NAME,
            'currentPage' => 'customer_consultation',
            'user' => $user,
            'errors' => [],
            'result' => $result,
            'consultationId' => $consultation->id,
            'history' => $history,
            'recommendedTechs' => $recommendedTechs
        ];

        $this->view('customer/consultation', $data);
    }
}
