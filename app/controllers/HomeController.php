<?php
/**
 * Home Controller
 * Project: Teknisi Rumah Tangga
 */

class HomeController extends Controller {
    private $categoryModel;
    private $technicianModel;

    public function __construct() {
        $this->categoryModel = $this->model('CategoryModel');
        $this->technicianModel = $this->model('TechnicianProfileModel');
    }

    /**
     * Display Landing Page
     *
     * @return void
     */
    public function index() {
        $categories = $this->categoryModel->getAllCategories();
        $isDbConnected = $this->categoryModel->isDbConnected();

        $featuredTechnicians = [];
        try {
            if ($this->technicianModel) {
                $featuredTechnicians = $this->technicianModel->getRecommendedTechnicians(4);
            }
        } catch (Exception $e) {
            $featuredTechnicians = [];
        }

        $data = [
            'title' => APP_NAME . ' - ' . APP_TAGLINE,
            'currentPage' => 'home',
            'categories' => $categories,
            'featuredTechnicians' => $featuredTechnicians,
            'isDbConnected' => $isDbConnected,
            'totalCategories' => count($categories),
            'stats' => [
                'mitra' => '150+',
                'selesai' => '2,500+',
                'rating' => '4.9/5',
                'garansi' => '30 Hari'
            ]
        ];

        $this->view('home/index', $data);
    }
}
