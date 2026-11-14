<?php
/**
 * Technician Controller
 * Project: Teknisi Rumah Tangga
 * Protected Area: Technician Role Only
 */

class TechnicianController extends Controller {
    private $postModel;
    private $techProfileModel;
    private $categoryModel;

    public function __construct() {
        Auth::requireRole(['technician']);
        $this->postModel = $this->model('PostModel');
        $this->techProfileModel = $this->model('TechnicianProfileModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    /**
     * Technician Dashboard Overview
     *
     * @return void
     */
    public function index() {
        $user = Auth::user();
        $profile = $this->techProfileModel->getProfileByUserId($user->id);
        
        $latestPosts = $this->postModel->getActivePostsForTechnicians(null, 8);
        $categoryPosts = !empty($profile->category_id) ? $this->postModel->getActivePostsForTechnicians($profile->category_id, 8) : [];
        $myResponses = $this->postModel->getResponsesByTechnicianId($user->id);

        // Separate active ongoing jobs (assigned & in progress) and completed jobs
        $activeJobs = [];
        $completedJobsCount = 0;
        foreach ($myResponses as $resp) {
            $rStatus = is_object($resp) ? $resp->status : $resp['status'];
            $pStatus = is_object($resp) ? $resp->post_status : $resp['post_status'];
            
            if ($rStatus === 'selected') {
                if ($pStatus === 'Selesai') {
                    $completedJobsCount++;
                } elseif ($pStatus !== 'Dibatalkan') {
                    $activeJobs[] = $resp;
                }
            }
        }

        $data = [
            'title' => 'Dashboard Mitra Teknisi - ' . APP_NAME,
            'currentPage' => 'technician_dashboard',
            'user' => $user,
            'profile' => $profile,
            'latestPosts' => $latestPosts,
            'categoryPosts' => $categoryPosts,
            'myResponses' => $myResponses,
            'activeJobs' => $activeJobs,
            'activeJobsCount' => count($activeJobs),
            'completedJobsCount' => max($completedJobsCount, (int)($profile->completed_jobs ?? 0))
        ];

        $this->view('technician/dashboard', $data);
    }
}
