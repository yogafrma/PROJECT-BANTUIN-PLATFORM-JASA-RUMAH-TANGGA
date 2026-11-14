<?php
/**
 * History Controller
 * Project: BANTUIN — Platform Layanan Jasa Rumah Tangga
 * Handles Transaction, Service, and Payment History for Customers, Technicians, and Admin
 */

class HistoryController extends Controller {
    private $postModel;

    public function __construct() {
        Auth::requireLogin();
        $this->postModel = $this->model('PostModel');
    }

    /**
     * Display Order & Payment Transaction History
     * URL: /history
     */
    public function index() {
        $user = Auth::user();
        $filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';
        if (!in_array($filter, ['all', 'completed', 'in_progress'], true)) {
            $filter = 'all';
        }

        $history = $this->postModel->getTransactionHistory($user->id, $user->role, $filter);

        // Calculate summary stats
        $allRecords = ($filter === 'all') ? $history : $this->postModel->getTransactionHistory($user->id, $user->role, 'all');
        $totalCompleted = 0;
        $totalAmount = 0;
        $totalInProgress = 0;

        foreach ($allRecords as $item) {
            if ($item->status === 'Selesai') {
                $totalCompleted++;
                $totalAmount += (int)($item->paid_amount ?: ($item->agreed_price ?: 0));
            } else {
                $totalInProgress++;
            }
        }

        $data = [
            'title' => 'Riwayat Transaksi & Layanan - ' . APP_NAME,
            'currentPage' => 'history',
            'user' => $user,
            'history' => $history,
            'currentFilter' => $filter,
            'stats' => [
                'total' => count($allRecords),
                'completed' => $totalCompleted,
                'in_progress' => $totalInProgress,
                'total_amount' => $totalAmount
            ]
        ];

        $this->view('history/index', $data);
    }
}

