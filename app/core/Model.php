<?php
/**
 * Base Model Class
 * Project: Teknisi Rumah Tangga
 */

abstract class Model {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Check if database is currently connected
     *
     * @return bool
     */
    public function isDbConnected() {
        return $this->db->isConnected();
    }
}
