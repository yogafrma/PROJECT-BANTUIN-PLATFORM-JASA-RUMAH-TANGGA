<?php
/**
 * Not Found (404) Controller
 * Project: Teknisi Rumah Tangga
 */

class NotFoundController extends Controller {
    /**
     * Display 404 Page
     *
     * @return void
     */
    public function index() {
        http_response_code(404);
        
        $data = [
            'title' => '404 - Halaman Tidak Ditemukan | ' . APP_NAME,
            'currentPage' => '404'
        ];

        $this->view('errors/404', $data);
    }
}
