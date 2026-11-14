<?php
/**
 * Profile Controller
 * Project: Teknisi Rumah Tangga
 * Handles Role-Adaptive Profile View and Updates
 */

class ProfileController extends Controller {
    private $userModel;
    private $techProfileModel;
    private $categoryModel;

    public function __construct() {
        Auth::requireLogin();
        $this->userModel = $this->model('UserModel');
        $this->techProfileModel = $this->model('TechnicianProfileModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    /**
     * Display & Update Profile
     *
     * @return void
     */
    public function index() {
        $user = Auth::user();
        $techProfile = null;
        $categories = [];
        $errors = [];

        if ($user->role === 'technician') {
            $techProfile = $this->techProfileModel->getProfileByUserId($user->id);
            $categories = $this->categoryModel->getAllCategories();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                $errors['general'] = 'Permintaan tidak valid (CSRF token mismatch). Silakan muat ulang halaman.';
            } else {
                $name = trim($_POST['name'] ?? '');
                $whatsapp = trim($_POST['whatsapp'] ?? '');
            $cleanWa = preg_replace('/[^0-9]/', '', $whatsapp);

            // Validations
            if (empty($name)) {
                $errors['name'] = 'Nama lengkap wajib diisi.';
            }

            if (empty($whatsapp)) {
                $errors['whatsapp'] = 'Nomor WhatsApp WAJIB diisi.';
            } elseif (strlen($cleanWa) < 9 || strlen($cleanWa) > 15) {
                $errors['whatsapp'] = 'Nomor WhatsApp tidak valid.';
            }

            // Technician specific validation
            if ($user->role === 'technician') {
                $categoryId = (int) ($_POST['category_id'] ?? 2);
                $location = trim($_POST['location'] ?? '');
                $description = trim($_POST['description'] ?? '');

                if (empty($location)) {
                    $errors['location'] = 'Area/Lokasi jangkauan layanan wajib diisi.';
                }
                if (empty($description)) {
                    $errors['description'] = 'Deskripsi pengalaman & keahlian wajib diisi.';
                }
            }
            
            if (empty($errors)) {
                // Update basic user profile
                $updateSuccess = $this->userModel->updateProfile($user->id, [
                    'name' => $name,
                    'whatsapp' => $cleanWa
                ]);

                if ($updateSuccess) {
                    // Update technician profile if applicable
                    if ($user->role === 'technician') {
                        $this->techProfileModel->upsertProfile($user->id, [
                            'category_id' => $categoryId,
                            'location' => $location,
                            'description' => $description
                        ]);
                    }

                    // Refresh session user data
                    $updatedUser = $this->userModel->findById($user->id);
                    Auth::login($updatedUser);

                    set_flash('success', 'Profil Anda telah berhasil diperbarui.');
                    redirect('profile');
                } else {
                    $errors['general'] = 'Terjadi kesalahan sistem saat memperbarui profil.';
                }
            }
        }
    }

        $data = [
            'title' => 'Pengaturan Profil - ' . APP_NAME,
            'currentPage' => 'profile',
            'user' => Auth::user(),
            'techProfile' => $techProfile,
            'categories' => $categories,
            'errors' => $errors
        ];

        $this->view('profile/index', $data);
    }
}
