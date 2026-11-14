<?php
/**
 * AI Provider Service (Universal Multi-Model AI Gateway)
 * Project: BANTUIN — Platform Jasa Rumah Tangga
 * 
 * Supports:
 * - Google Gemini (gemini-1.5-flash, gemini-2.0-flash, gemini-1.5-pro)
 * - OpenAI ChatGPT (gpt-4o-mini, gpt-4o, gpt-3.5-turbo)
 * - Anthropic Claude (claude-3-5-sonnet, claude-3-haiku)
 * - OpenRouter / DeepSeek (deepseek-chat, deepseek-r1, llama-3.3)
 * - Groq Cloud (llama-3.3-70b-versatile, mixtral)
 * - Custom / Local (Ollama, LM Studio, any OpenAI-compatible API)
 * - BANTUIN Smart Local Engine (offline zero-config)
 */

class AIProviderService {

    /**
     * Get all supported AI providers metadata
     *
     * @return array
     */
    public static function getProviders() {
        return [
            'gemini' => [
                'id' => 'gemini',
                'name' => 'Google Gemini',
                'badge' => 'Google AI Studio',
                'icon' => 'bi-stars',
                'color' => '#1a73e8',
                'description' => 'Model cepat, cerdas, dan efisien dari Google dengan dukungan kuota gratis melimpah.',
                'default_model' => 'gemini-flash-lite-latest',
                'available_models' => [
                    'gemini-flash-lite-latest' => 'Gemini Flash Lite (Teruji 200 OK & Super Cepat - Rekomendasi)',
                    'gemini-flash-latest'      => 'Gemini Flash Latest (Versi Terbaru Google)',
                    'gemini-pro-latest'        => 'Gemini Pro Latest (Model Penalaran Tinggi)'
                ],
                'doc_url' => 'https://aistudio.google.com/app/apikey',
                'key_placeholder' => 'AIzaSy... / AQ....',
                'needs_custom_url' => false,
                'is_offline' => false
            ],
            'nvidia' => [
                'id' => 'nvidia',
                'name' => 'NVIDIA NIM (AI Foundation)',
                'badge' => 'NVIDIA Build Cloud',
                'icon' => 'bi-gpu-card',
                'color' => '#76b900',
                'description' => 'Inference akselerasi GPU NVIDIA NIM. Akses model Mistral Large, MiniMax & Nemotron dengan 1.000 free credits.',
                'default_model' => 'mistralai/mistral-large-4-1t-a52b',
                'available_models' => [
                    'mistralai/mistral-large-4-1t-a52b'       => 'Mistral Large 4 (Sangat Cerdas, Cepat & Aktif)',
                    'minimaxai/minimax-m3.1-flash'             => 'MiniMax M3.1 Flash (Respons Super Cepat)',
                    'nvidia/nemotron-3.5-lightning-30b-a3b'    => 'NVIDIA Nemotron 30B Lightning (Model Resmi NVIDIA)',
                    'deepseek-ai/deepseek-v4.1-flash'          => 'DeepSeek V4.1 Flash (Sering Antre/Timeout di Serverless NVIDIA)'
                ],
                'doc_url' => 'https://build.nvidia.com/',
                'key_placeholder' => 'nvapi-...',
                'needs_custom_url' => false,
                'default_url' => 'https://integrate.api.nvidia.com/v1/chat/completions',
                'is_offline' => false
            ]
        ];
    }

    /**
     * Get active AI configuration from SettingModel or Config constants
     *
     * @return array
     */
    public static function getConfig() {
        $settingModel = self::getSettingModel();

        $savedProvider = $settingModel->get('ai_active_provider', null);
        $savedApiKey   = $settingModel->get('ai_api_key', null);
        $savedModel    = $settingModel->get('ai_model', null);
        $savedUrl      = $settingModel->get('ai_custom_url', null);
        $savedTemp     = $settingModel->get('ai_temperature', null);
        $savedFallback = $settingModel->get('ai_fallback_enabled', null);

        // Default to gemini
        $geminiConst = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        $defaultProvider = 'gemini';

        $provider = $savedProvider ?: $defaultProvider;
        $providers = self::getProviders();
        if (!isset($providers[$provider])) {
            $provider = 'gemini';
        }

        $apiKey = $savedApiKey !== null ? $savedApiKey : $geminiConst;
        $model = $savedModel ?: ($providers[$provider]['default_model'] ?? 'gemini-flash-lite-latest');
        $customUrl = $savedUrl ?: ($providers[$provider]['default_url'] ?? '');
        $temperature = $savedTemp !== null ? (float)$savedTemp : 0.6;
        $fallbackEnabled = $savedFallback !== null ? (bool)$savedFallback : true;

        return [
            'provider'         => $provider,
            'provider_info'    => $providers[$provider] ?? $providers['gemini'],
            'provider_name'    => $providers[$provider]['name'] ?? 'Google Gemini',
            'api_key'          => $apiKey,
            'api_key_masked'   => self::maskApiKey($apiKey),
            'model'            => $model,
            'custom_url'       => $customUrl,
            'temperature'      => $temperature,
            'fallback_enabled' => $fallbackEnabled
        ];
    }

    /**
     * Auto-detect provider from API Key format / prefix
     *
     * @param string $apiKey
     * @return string|null Provider id or null
     */
    public static function detectProviderFromKey($apiKey) {
        $key = trim((string)$apiKey);
        if (empty($key)) return null;

        if (str_starts_with($key, 'nvapi-')) {
            return 'nvidia';
        }
        if (str_starts_with($key, 'AIzaSy') || str_starts_with($key, 'AQ.')) {
            return 'gemini';
        }

        return null;
    }

    /**
     * Save AI configuration
     *
     * @param array $data
     * @return bool
     */
    public static function saveConfig(array $data) {
        if (!function_exists('sanitize')) {
            require_once dirname(__DIR__) . '/core/Helper.php';
        }
        $settingModel = self::getSettingModel();

        $rawProvider = $data['ai_provider'] ?? 'gemini';
        $provider = function_exists('sanitize') ? sanitize($rawProvider) : htmlspecialchars(trim((string)$rawProvider), ENT_QUOTES, 'UTF-8');
        $providers = self::getProviders();

        $apiKey = trim($data['ai_api_key'] ?? '');

        // Auto-detect & auto-correct provider if API key signature matches a specific provider
        $detected = self::detectProviderFromKey($apiKey);
        if ($detected && isset($providers[$detected])) {
            $provider = $detected;
        }

        if (!isset($providers[$provider])) {
            $provider = 'gemini';
        }

        $model = trim($data['ai_model'] ?? '');
        $isModelMismatched = empty($model) 
            || ($provider === 'nvidia' && str_contains($model, 'gemini'))
            || ($provider === 'gemini' && (str_contains($model, 'llama') || str_contains($model, 'nemotron') || str_contains($model, 'deepseek') || str_contains($model, 'mistral')));

        if ($isModelMismatched) {
            $model = $providers[$provider]['default_model'] ?? 'gemini-flash-lite-latest';
        }

        $customUrl = trim($data['ai_custom_url'] ?? '');
        $temperature = isset($data['ai_temperature']) ? (float)$data['ai_temperature'] : 0.6;
        $fallback = isset($data['ai_fallback_enabled']) ? (bool)$data['ai_fallback_enabled'] : true;

        return $settingModel->saveMultiple([
            'ai_active_provider'  => $provider,
            'ai_api_key'          => $apiKey,
            'ai_model'            => $model,
            'ai_custom_url'       => $customUrl,
            'ai_temperature'      => $temperature,
            'ai_fallback_enabled' => $fallback
        ]);
    }

    /**
     * Send chat completion request to active provider
     *
     * @param string $systemPrompt
     * @param array $messages Array of ['role' => 'user'|'assistant'|'model', 'content' => string]
     * @param array $options Optional override for model, temperature, etc.
     * @return array ['success' => bool, 'reply' => string, 'provider_name' => string, 'model' => string, 'error' => string|null]
     */
    public static function chat($systemPrompt, array $messages, array $options = []) {
        $config = self::getConfig();
        $provider = $options['provider'] ?? $config['provider'];
        $apiKey = $options['api_key'] ?? $config['api_key'];
        $model = $options['model'] ?? $config['model'];
        $customUrl = $options['custom_url'] ?? $config['custom_url'];
        $temperature = $options['temperature'] ?? $config['temperature'];

        if (empty($apiKey)) {
            return [
                'success' => false,
                'reply' => '',
                'provider_name' => 'BANTUIN Engine',
                'model' => 'none',
                'error' => 'API Key belum diisi. Silakan masukkan API Key Google Gemini atau NVIDIA NIM.'
            ];
        }

        switch ($provider) {
            case 'gemini':
                return self::callGeminiChat($systemPrompt, $messages, $apiKey, $model, $temperature);
            case 'nvidia':
                return self::callOpenAiChat($systemPrompt, $messages, $apiKey, $model, $temperature, 'https://integrate.api.nvidia.com/v1/chat/completions', 'NVIDIA NIM');
            default:
                return [
                    'success' => false,
                    'reply' => '',
                    'provider_name' => 'None',
                    'model' => 'none',
                    'error' => 'Provider tidak dikenal. Sistem hanya mendukung Google Gemini dan NVIDIA NIM.'
                ];
        }
    }

    /**
     * Diagnose damage details and chronology using active AI Provider (JSON Output)
     *
     * @param string $damageDetail
     * @param string $chronology
     * @param string|null $imagePath
     * @return array|null Structured diagnosis result or null on failure
     */
    public static function diagnoseConsultation($damageDetail, $chronology, $imagePath = null) {
        $config = self::getConfig();
        if ($config['provider'] === 'internal' || (empty($config['api_key']) && $config['provider'] !== 'custom')) {
            return null;
        }

        $prompt = "Anda adalah konsultan teknis perbaikan rumah tangga profesional di platform 'BANTUIN'.\n"
                . "Analisis data keluhan pelanggan berikut ini secara terstruktur:\n"
                . "- Detail Kerusakan: {$damageDetail}\n"
                . "- Kronologi Kejadian: {$chronology}\n\n"
                . "PANDUAN PENTING:\n"
                . "1. Gunakan Bahasa Indonesia yang ramah, sopan, dan jelas.\n"
                . "2. Hindari diagnosis kepastian mutlak. Gunakan kata seperti 'kemungkinan', 'perkiraan', atau 'berdasarkan informasi yang diberikan'.\n"
                . "3. Kategori teknisi yang tersedia di BANTUIN WAJIB salah satu dari 4 ini:\n"
                . "   - 'Teknisi Listrik' (slug: 'teknisi-listrik')\n"
                . "   - 'Teknisi AC' (slug: 'teknisi-ac')\n"
                . "   - 'Tukang Kunci' (slug: 'tukang-kunci')\n"
                . "   - 'Teknisi Elektronik' (slug: 'teknisi-elektronik')\n"
                . "4. Berikan estimasi harga (Rupiah) dalam batas wajar servis rumah tangga Indonesia.\n\n"
                . "OUTPUT WAJIB DALAM FORMAT JSON SAJA (tanpa pengantar/penutup markdown):\n"
                . "{\n"
                . '  "possible_damage": "penjelasan kemungkinan penyebab kerusakan", ' . "\n"
                . '  "category_name": "Teknisi AC / Teknisi Listrik / Tukang Kunci / Teknisi Elektronik", ' . "\n"
                . '  "category_slug": "teknisi-ac / teknisi-listrik / tukang-kunci / teknisi-elektronik", ' . "\n"
                . '  "estimated_min_price": 75000, ' . "\n"
                . '  "estimated_max_price": 250000, ' . "\n"
                . '  "explanation": "penjelasan singkat analisis gejala & saran keamanan", ' . "\n"
                . '  "draft_post_title": "draft judul postingan keluhan yang menarik dan ringkas", ' . "\n"
                . '  "draft_post_description": "draft deskripsi lengkap untuk dipublikasikan ke teknisi" ' . "\n"
                . "}";

        $systemPrompt = "Anda adalah AI Engine backend platform BANTUIN yang selalu menghasilkan output JSON valid tanpa bungkus markdown.";
        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $res = self::chat($systemPrompt, $messages, [
            'temperature' => 0.2
        ]);

        if (!$res['success'] || empty($res['reply'])) {
            return null;
        }

        $cleanText = trim($res['reply']);
        // Strip markdown code fences if present (e.g. ```json ... ```)
        if (str_starts_with($cleanText, '```json')) {
            $cleanText = substr($cleanText, 7);
        } elseif (str_starts_with($cleanText, '```')) {
            $cleanText = substr($cleanText, 3);
        }
        if (str_ends_with($cleanText, '```')) {
            $cleanText = substr($cleanText, 0, -3);
        }
        $cleanText = trim($cleanText);

        $parsed = @json_decode($cleanText, true);
        if (is_array($parsed) && isset($parsed['possible_damage'])) {
            return [
                'possible_damage'        => (string)($parsed['possible_damage'] ?? ''),
                'category_name'          => (string)($parsed['category_name'] ?? 'Teknisi Elektronik'),
                'category_slug'          => (string)($parsed['category_slug'] ?? 'teknisi-elektronik'),
                'estimated_min_price'    => (int)($parsed['estimated_min_price'] ?? 75000),
                'estimated_max_price'    => (int)($parsed['estimated_max_price'] ?? 250000),
                'explanation'            => (string)($parsed['explanation'] ?? ''),
                'draft_post_title'       => (string)($parsed['draft_post_title'] ?? 'Permintaan Servis Rumah Tangga'),
                'draft_post_description' => (string)($parsed['draft_post_description'] ?? $damageDetail),
                'ai_provider'            => $res['provider_name'] ?? 'AI'
            ];
        }

        return null;
    }

    /**
     * Test connection for a given provider and API key
     *
     * @param string $provider
     * @param string $apiKey
     * @param string|null $model
     * @param string|null $customUrl
     * @return array ['success' => bool, 'message' => string, 'reply' => string, 'latency_ms' => int]
     */
    public static function testConnection($provider, $apiKey, $model = null, $customUrl = null) {
        $providers = self::getProviders();
        if (!isset($providers[$provider])) {
            return ['success' => false, 'message' => 'Provider tidak valid.', 'reply' => '', 'latency_ms' => 0];
        }

        if ($provider === 'internal') {
            return [
                'success' => true,
                'message' => 'BANTUIN Internal Engine siap digunakan (Offline & Zero-config).',
                'reply' => 'Halo! Sistem internal BANTUIN aktif dan siap melayani permintaan.',
                'latency_ms' => 1
            ];
        }

        if (empty($apiKey) && $provider !== 'custom') {
            return ['success' => false, 'message' => 'API Key wajib diisi untuk menguji koneksi.', 'reply' => '', 'latency_ms' => 0];
        }

        $activeModel = !empty($model) ? $model : ($providers[$provider]['default_model'] ?? 'gemini-1.5-flash');
        $startTime = microtime(true);

        $systemPrompt = "Balas dalam satu kalimat singkat Bahasa Indonesia yang ramah.";
        $messages = [
            ['role' => 'user', 'content' => "Tes koneksi API platform BANTUIN. Apakah kamu online dan siap bertugas?"]
        ];

        $res = self::chat($systemPrompt, $messages, [
            'provider'   => $provider,
            'api_key'    => $apiKey,
            'model'      => $activeModel,
            'custom_url' => $customUrl,
            'temperature' => 0.5
        ]);

        $latency = round((microtime(true) - $startTime) * 1000);

        if ($res['success']) {
            return [
                'success' => true,
                'message' => "Koneksi berhasil ke {$res['provider_name']} ({$res['model']})! Waktu respons: {$latency}ms.",
                'reply' => $res['reply'],
                'latency_ms' => $latency
            ];
        }

        return [
            'success' => false,
            'message' => "Gagal terhubung ke {$providers[$provider]['name']}: " . ($res['error'] ?? 'Terjadi kesalahan tidak dikenal.'),
            'reply' => '',
            'latency_ms' => $latency
        ];
    }

    // =========================================================================
    // PROVIDER CALL IMPLEMENTATIONS
    // =========================================================================

    /**
     * Call Google Gemini API
     */
    private static function callGeminiChat($systemPrompt, array $messages, $apiKey, $model, $temperature) {
        $contents = [];
        $lastRole = null;
        foreach ($messages as $msg) {
            $content = trim($msg['content'] ?? '');
            if ($content === '') continue;
            $rawRole = strtolower($msg['role'] ?? '');
            $role = in_array($rawRole, ['user', 'customer']) ? 'user' : 'model';

            // First message in Gemini conversation must be 'user'
            if (empty($contents) && $role !== 'user') {
                continue;
            }

            // If consecutive same role, merge parts to strictly satisfy Gemini alternating turn requirements
            if ($role === $lastRole) {
                $lastIndex = count($contents) - 1;
                $contents[$lastIndex]['parts'][] = ['text' => $content];
            } else {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $content]]
                ];
                $lastRole = $role;
            }
        }

        // Ensure contents is not empty
        if (empty($contents)) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => 'Halo']]
            ];
        }

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => 800
            ]
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            $json = json_decode($response, true);
            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            if (!empty($text)) {
                return [
                    'success' => true,
                    'reply' => trim($text),
                    'provider_name' => 'Google Gemini',
                    'model' => $model,
                    'error' => null
                ];
            }
        }

        $errMsg = "HTTP {$httpCode}";
        if (!empty($response)) {
            $errJson = @json_decode($response, true);
            if (str_contains($response, 'ACCESS_TOKEN_TYPE_UNSUPPORTED')) {
                $errMsg .= " - Kunci yang dimasukkan adalah OAuth Access Token (awalan 'AQ.'), bukan API Key Google AI Studio. Silakan buat dan gunakan API Key resmi dari Google AI Studio yang berawalan 'AIzaSy...'.";
            } elseif (isset($errJson['error']['message'])) {
                $errMsg .= ': ' . $errJson['error']['message'];
            } else {
                $errMsg .= ': ' . substr($response, 0, 150);
            }
        } elseif ($curlError) {
            if (str_contains(strtolower($curlError), 'timed out') || str_contains(strtolower($curlError), 'timeout')) {
                $errMsg = "Koneksi ke Google Gemini timeout (kehabisan waktu). Jaringan internet sedang lambat atau terhambat. Silakan coba klik tombol Uji Sambungan sekali lagi.";
            } else {
                $errMsg .= " - {$curlError}";
            }
        }

        return [
            'success' => false,
            'reply' => '',
            'provider_name' => 'Google Gemini',
            'model' => $model,
            'error' => $errMsg
        ];
    }

    /**
     * Call OpenAI / OpenRouter / Groq / Custom OpenAI-Compatible Chat Completions API
     */
    private static function callOpenAiChat($systemPrompt, array $messages, $apiKey, $model, $temperature, $endpointUrl, $providerName) {
        $chatMessages = [];
        if (!empty($systemPrompt)) {
            $chatMessages[] = ['role' => 'system', 'content' => $systemPrompt];
        }

        foreach ($messages as $msg) {
            $role = in_array($msg['role'] ?? '', ['assistant', 'model']) ? 'assistant' : 'user';
            $chatMessages[] = [
                'role' => $role,
                'content' => $msg['content'] ?? ''
            ];
        }

        $payload = [
            'model' => $model,
            'messages' => $chatMessages,
            'temperature' => $temperature,
            'max_tokens' => 1200
        ];

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        if (!empty($apiKey)) {
            $headers[] = 'Authorization: Bearer ' . $apiKey;
        }
        if (str_contains($endpointUrl, 'openrouter.ai')) {
            $headers[] = 'HTTP-Referer: http://localhost/lat1c';
            $headers[] = 'X-Title: BANTUIN Household Services';
        }

        $ch = curl_init($endpointUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 35);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            $json = json_decode($response, true);
            $msgObj = $json['choices'][0]['message'] ?? [];
            $text = $msgObj['content'] ?? '';
            // If content is empty (e.g. reasoning model token cutoff), check reasoning_content
            if (empty(trim($text)) && !empty($msgObj['reasoning_content'])) {
                $text = trim($msgObj['reasoning_content']);
            }
            if (!empty($text)) {
                return [
                    'success' => true,
                    'reply' => trim($text),
                    'provider_name' => $providerName,
                    'model' => $model,
                    'error' => null
                ];
            }
        }

        $errMsg = "HTTP {$httpCode}";
        if (!empty($response)) {
            $errJson = @json_decode($response, true);
            if (isset($errJson['detail'])) {
                if (str_contains($errJson['detail'], 'Not found for account') || str_contains($errJson['detail'], 'Function')) {
                    $errMsg .= " - Model '{$model}' belum diaktifkan untuk akun NVIDIA Anda. Silakan klik salah satu preset model lain di bawah (misalnya: 'mistralai/mistral-large-4-1t-a52b' atau 'minimaxai/minimax-m3.1-flash'), atau salin nama model yang tertulis di tab NVIDIA Build Anda.";
                } else {
                    $errMsg .= ': ' . $errJson['detail'];
                }
            } elseif (isset($errJson['error']['message'])) {
                $errMsg .= ': ' . $errJson['error']['message'];
            } else {
                $errMsg .= ': ' . substr($response, 0, 150);
            }
        } elseif ($curlError) {
            if (str_contains(strtolower($curlError), 'timed out') || str_contains(strtolower($curlError), 'timeout')) {
                $errMsg = "Koneksi ke {$providerName} timeout (kehabisan waktu). Serverless NVIDIA untuk model '{$model}' sedang antre/padat. Silakan gunakan model 'mistralai/mistral-large-4-1t-a52b' atau 'minimaxai/minimax-m3.1-flash' yang siap pakai.";
            } else {
                $errMsg .= " - {$curlError}";
            }
        }

        return [
            'success' => false,
            'reply' => '',
            'provider_name' => $providerName,
            'model' => $model,
            'error' => $errMsg
        ];
    }

    /**
     * Helper to mask API keys for safe display (e.g. AIzaSy...9XYZ)
     */
    public static function maskApiKey($key) {
        $key = trim((string)$key);
        if (empty($key)) {
            return 'Belum diatur (Offline Engine)';
        }
        $len = strlen($key);
        if ($len <= 8) {
            return '••••••••';
        }
        return substr($key, 0, 4) . '••••' . substr($key, -4);
    }

    /**
     * Helper to obtain instance of SettingModel
     */
    private static function getSettingModel() {
        require_once dirname(__DIR__) . '/core/Model.php';
        require_once dirname(__DIR__) . '/models/SettingModel.php';
        return new SettingModel();
    }
}
