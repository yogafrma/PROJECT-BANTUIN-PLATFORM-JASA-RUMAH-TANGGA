<?php
/**
 * Category Controller
 * Project: Teknisi Rumah Tangga
 * Handles CRUD Category Management for Admin
 */

class CategoryController extends Controller {
    private $categoryModel;

    public function __construct() {
        // Enforce RBAC: Only Admin can access Category Management
        Auth::requireRole(['admin']);
        $this->categoryModel = $this->model('CategoryModel');
    }

    /**
     * Display List of Categories
     *
     * @return void
     */
    public function index() {
        $categories = $this->categoryModel->getAllCategories(false);

        $data = [
            'title' => 'Manajemen Kategori Jasa - ' . APP_NAME,
            'currentPage' => 'admin_categories',
            'user' => Auth::user(),
            'categories' => $categories
        ];

        $this->view('admin/categories/index', $data);
    }

    /**
     * Create New Category (GET Form & POST Process)
     *
     * @return void
     */
    public function create() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = trim($_POST['status'] ?? 'active');
            $icon = trim($_POST['icon'] ?? 'bi-tools');
            $badge = trim($_POST['badge'] ?? 'Layanan Baru');

            // 1. Backend Validations
            if (empty($name)) {
                $errors['name'] = 'Nama kategori wajib diisi.';
            } elseif (strlen($name) < 3) {
                $errors['name'] = 'Nama kategori minimal 3 karakter.';
            } elseif (strlen($name) > 150) {
                $errors['name'] = 'Nama kategori maksimal 150 karakter.';
            }

            if (empty($description)) {
                $errors['description'] = 'Deskripsi kategori wajib diisi.';
            } elseif (strlen($description) < 10) {
                $errors['description'] = 'Deskripsi kategori minimal 10 karakter.';
            }

            if (!in_array($status, ['active', 'inactive'], true)) {
                $errors['status'] = 'Status kategori tidak valid.';
            }

            // 2. Process Insert
            if (empty($errors)) {
                $insertedId = $this->categoryModel->createCategory([
                    'name' => $name,
                    'description' => $description,
                    'status' => $status,
                    'icon' => $icon,
                    'badge' => $badge
                ]);

                if ($insertedId) {
                    set_flash('success', "Kategori '{$name}' berhasil ditambahkan.");
                    redirect('categories');
                } else {
                    $errors['general'] = 'Gagal menyimpan kategori ke database. Silakan coba lagi.';
                }
            }
        }

        $data = [
            'title' => 'Tambah Kategori Jasa - ' . APP_NAME,
            'currentPage' => 'admin_categories',
            'user' => Auth::user(),
            'errors' => $errors
        ];

        $this->view('admin/categories/create', $data);
    }

    /**
     * Edit Category (GET Form & POST Process)
     *
     * @param int|string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$id) {
            set_flash('danger', 'ID kategori tidak valid.');
            redirect('categories');
        }

        $category = $this->categoryModel->getCategoryById((int)$id);
        if (!$category) {
            set_flash('danger', 'Kategori tidak ditemukan.');
            redirect('categories');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = trim($_POST['status'] ?? 'active');
            $icon = trim($_POST['icon'] ?? 'bi-tools');
            $badge = trim($_POST['badge'] ?? 'Populer');

            // 1. Backend Validations
            if (empty($name)) {
                $errors['name'] = 'Nama kategori wajib diisi.';
            } elseif (strlen($name) < 3) {
                $errors['name'] = 'Nama kategori minimal 3 karakter.';
            } elseif (strlen($name) > 150) {
                $errors['name'] = 'Nama kategori maksimal 150 karakter.';
            }

            if (empty($description)) {
                $errors['description'] = 'Deskripsi kategori wajib diisi.';
            } elseif (strlen($description) < 10) {
                $errors['description'] = 'Deskripsi kategori minimal 10 karakter.';
            }

            if (!in_array($status, ['active', 'inactive'], true)) {
                $errors['status'] = 'Status kategori tidak valid.';
            }

            // 2. Process Update
            if (empty($errors)) {
                $updated = $this->categoryModel->updateCategory((int)$id, [
                    'name' => $name,
                    'description' => $description,
                    'status' => $status,
                    'icon' => $icon,
                    'badge' => $badge
                ]);

                if ($updated) {
                    set_flash('success', "Kategori '{$name}' berhasil diperbarui.");
                    redirect('categories');
                } else {
                    $errors['general'] = 'Gagal memperbarui kategori. Silakan coba lagi.';
                }
            }
        }

        $data = [
            'title' => 'Edit Kategori: ' . e($category->name) . ' - ' . APP_NAME,
            'currentPage' => 'admin_categories',
            'user' => Auth::user(),
            'category' => $category,
            'errors' => $errors
        ];

        $this->view('admin/categories/edit', $data);
    }

    /**
     * Toggle Category Status (Active <-> Inactive)
     *
     * @param int|string $id
     * @return void
     */
    public function toggle($id = null) {
        if (!$id) {
            set_flash('danger', 'ID kategori tidak valid.');
            redirect('categories');
        }

        $category = $this->categoryModel->getCategoryById((int)$id);
        if (!$category) {
            set_flash('danger', 'Kategori tidak ditemukan.');
            redirect('categories');
        }

        $newStatus = ($category->status === 'active') ? 'Nonaktif' : 'Aktif';
        if ($this->categoryModel->toggleStatus((int)$id)) {
            set_flash('success', "Status kategori '" . e($category->name) . "' berhasil diubah menjadi {$newStatus}.");
        } else {
            set_flash('danger', 'Gagal mengubah status kategori.');
        }

        redirect('categories');
    }

    /**
     * Delete Category
     *
     * @param int|string $id
     * @return void
     */
    public function delete($id = null) {
        if (!$id) {
            set_flash('danger', 'ID kategori tidak valid.');
            redirect('categories');
        }

        $category = $this->categoryModel->getCategoryById((int)$id);
        if (!$category) {
            set_flash('danger', 'Kategori tidak ditemukan.');
            redirect('categories');
        }

        if ($this->categoryModel->deleteCategory((int)$id)) {
            set_flash('success', "Kategori '" . e($category->name) . "' berhasil dihapus.");
        } else {
            set_flash('danger', 'Gagal menghapus kategori.');
        }

        redirect('categories');
    }
}
