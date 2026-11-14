<?php
/**
 * Setting Model
 * Project: BANTUIN — Platform Jasa Rumah Tangga
 * Handles dynamic system settings persistence in MySQL & JSON file fallback
 */

class SettingModel extends Model {
    private static $cache = [];
    private $storageFile;

    public function __construct() {
        parent::__construct();
        $this->storageFile = dirname(__DIR__) . '/storage/settings.json';
        $this->ensureTable();
    }

    /**
     * Ensure system_settings table exists in MySQL
     */
    private function ensureTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `system_settings` (
                `setting_key` VARCHAR(100) NOT NULL PRIMARY KEY,
                `setting_value` MEDIUMTEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->db->query($sql);
            $this->db->execute();
        } catch (\Throwable $e) {
            // Silently continue; JSON file fallback will handle if DB is unavailable
        }
    }

    /**
     * Get a setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        try {
            $this->db->query("SELECT `setting_value` FROM `system_settings` WHERE `setting_key` = :key LIMIT 1");
            $this->db->bind(':key', $key);
            $row = $this->db->single();

            if ($row && isset($row->setting_value)) {
                $value = $this->deserialize($row->setting_value);
                self::$cache[$key] = $value;
                return $value;
            }
        } catch (\Throwable $e) {
            // Fallback to JSON storage
        }

        // Try reading from JSON file fallback
        $fileSettings = $this->readFileSettings();
        if (array_key_exists($key, $fileSettings)) {
            self::$cache[$key] = $fileSettings[$key];
            return $fileSettings[$key];
        }

        return $default;
    }

    /**
     * Set / update a setting by key
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value) {
        self::$cache[$key] = $value;
        $serialized = $this->serialize($value);

        $dbSaved = false;
        try {
            $this->db->query("INSERT INTO `system_settings` (`setting_key`, `setting_value`) 
                              VALUES (:key, :val) 
                              ON DUPLICATE KEY UPDATE `setting_value` = :val2, `updated_at` = NOW()");
            $this->db->bind(':key', $key);
            $this->db->bind(':val', $serialized);
            $this->db->bind(':val2', $serialized);
            $dbSaved = $this->db->execute();
        } catch (\Throwable $e) {
            $dbSaved = false;
        }

        // Always sync to JSON file fallback
        $this->updateFileSetting($key, $value);

        return $dbSaved || true;
    }

    /**
     * Save multiple settings at once
     *
     * @param array $settings ['key' => 'value']
     * @return bool
     */
    public function saveMultiple(array $settings) {
        $allSuccess = true;
        foreach ($settings as $key => $value) {
            $saved = $this->set($key, $value);
            if (!$saved) {
                $allSuccess = false;
            }
        }
        return $allSuccess;
    }

    /**
     * Get all settings as key-value array
     *
     * @return array
     */
    public function getAll() {
        $all = [];

        // 1. Load from file first as base
        $all = $this->readFileSettings();

        // 2. Override with DB values
        try {
            $this->db->query("SELECT `setting_key`, `setting_value` FROM `system_settings`");
            $rows = $this->db->resultSet();
            if ($rows) {
                foreach ($rows as $r) {
                    $all[$r->setting_key] = $this->deserialize($r->setting_value);
                }
            }
        } catch (\Throwable $e) {
            // DB failed, file fallback already loaded
        }

        self::$cache = $all;
        return $all;
    }

    /**
     * Helper to read JSON storage
     */
    private function readFileSettings() {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        $raw = @file_get_contents($this->storageFile);
        if (!$raw) {
            return [];
        }
        $decoded = @json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Helper to write a single key to JSON storage
     */
    private function updateFileSetting($key, $value) {
        $data = $this->readFileSettings();
        $data[$key] = $value;

        $dir = dirname($this->storageFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        @file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Serialize complex values to JSON string
     */
    private function serialize($value) {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return (string)$value;
    }

    /**
     * Deserialize JSON if possible
     */
    private function deserialize($value) {
        if ($value === null) {
            return null;
        }
        $decoded = json_decode($value, true);
        return (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) ? $decoded : $value;
    }
}
