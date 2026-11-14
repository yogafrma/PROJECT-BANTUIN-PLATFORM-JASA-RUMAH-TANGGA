<?php
/**
 * Single Entry Point
 * Project: Teknisi Rumah Tangga
 * 
 * Bootstrap MVC Architecture
 */

// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 1. Load Configurations & Constants
require_once '../app/config/config.php';

// 2. Load Helper Functions
require_once '../app/core/Helper.php';

// 3. Load Core Classes
require_once '../app/core/Database.php';
require_once '../app/core/Auth.php';
require_once '../app/core/Model.php';
require_once '../app/core/Controller.php';
require_once '../app/core/App.php';

// 4. Initialize Auth Session
Auth::init();

// 5. Initialize Core Application Router
$app = new App();
