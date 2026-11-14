<?php
/**
 * AI Consultation Service
 * Project: Teknisi Rumah Tangga
 * 
 * Provides automated structured analysis of household appliance damages
 * Strictly backend execution (API keys are NEVER exposed to client-side)
 */

class AIService {

    /**
     * Diagnose damage details and chronology using AI or intelligent fallback heuristics
     *
     * @param string $damageDetail Description of the damage symptoms
     * @param string $chronology Chronological event leading up to the issue
     * @param string|null $imagePath Optional path to uploaded photo of damage
     * @return array Structured diagnosis result
     */
    public function diagnose($damageDetail, $chronology, $imagePath = null) {
        $combinedText = strtolower($damageDetail . ' ' . $chronology);

        // 1. Try active AI Provider from configuration (Gemini, ChatGPT, Claude, OpenRouter, Groq, Custom)
        require_once __DIR__ . '/AIProviderService.php';
        try {
            $aiResult = AIProviderService::diagnoseConsultation($damageDetail, $chronology, $imagePath);
            if ($aiResult !== null) {
                return $aiResult;
            }
        } catch (\Throwable $e) {
            // Silently proceed to fallback
        }

        // 2. Fallback to Gemini if legacy config exists
        $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : (getenv('GEMINI_API_KEY') ?: null);
        if (!empty($apiKey) && function_exists('curl_init')) {
            $apiResult = $this->callGeminiApi($damageDetail, $chronology, $apiKey, $imagePath);
            if ($apiResult !== null) {
                return $apiResult;
            }
        }

        // 3. Fallback Heuristic Diagnosis Engine (Always reliable & offline)
        return $this->fallbackDiagnosis($damageDetail, $chronology, $combinedText);
    }

    /**
     * Intelligent contextual heuristic diagnosis engine (Offline / Mock Fallback)
     *
     * @param string $damageDetail
     * @param string $chronology
     * @param string $combinedText
     * @return array
     */
    private function fallbackDiagnosis($damageDetail, $chronology, $combinedText) {
        // 1. Keyword Categorization
        $isAC = preg_match('/(ac|dingin|freon|bocor|menetes|suhu|indoor|outdoor|kondensor|evaporator|dinginnya|remote|hembusan)/i', $combinedText);
        $isListrik = preg_match('/(listrik|korslet|konslet|mcb|jeglek|stop kontak|saklar|lampu|panel|tegangan|kabel|arus|daya)/i', $combinedText);
        $isKunci = preg_match('/(kunci|pintu|gagang|silinder|macet|terkunci|gembok|smart lock|duplikat|patah|slot)/i', $combinedText);
        $isElektronik = preg_match('/(tv|televisi|kulkas|mesin cuci|microwave|pompa|pompa air|dinamo|kipas|dispenser|setrika|mati total|layar)/i', $combinedText);

        if ($isAC) {
            $categoryName = 'Teknisi AC';
            $categorySlug = 'teknisi-ac';
            $possibleDamage = 'Kemungkinan terjadi penyumbatan pada saluran pembuangan air (drainase), penurunan tekanan refrigrant (freon), atau penumpukan debu tebal pada kisi-kisi evaporator unit indoor.';
            $minPrice = 75000;
            $maxPrice = 250000;
            $explanation = 'Berdasarkan informasi yang diberikan, gejala tersebut biasanya disebabkan oleh unit pendingin yang memerlukan servis cuci berkala, perbaikan jalur pembuangan kondensasi, atau pengisian ulang freon. Disarankan tidak menyalakan AC secara terus menerus sebelum diperiksa teknisi guna mencegah kerusakan lebih lanjut pada kompresor.';
            $draftTitle = 'Perbaikan & Servis AC (' . mb_substr($damageDetail, 0, 45) . '...)';
            $draftDesc = "Membutuhkan mitra teknisi AC untuk penanganan kendala: {$damageDetail}. Kronologi: {$chronology}.";
        } elseif ($isListrik) {
            $categoryName = 'Teknisi Listrik';
            $categorySlug = 'teknisi-listrik';
            $possibleDamage = 'Kemungkinan terjadi arus pendek (korsleting), kelebihan beban pada jalur MCB tertentu, atau kerusakan pada terminal stop kontak/sambungan kabel di dalam dinding.';
            $minPrice = 80000;
            $maxPrice = 300000;
            $explanation = 'Berdasarkan informasi yang diberikan, gejala tripping atau matinya aliran listrik mengindikasikan adanya lonjakan arus atau kabel yang terkelupas. Perkiraan penanganan membutuhkan pengecekan resistansi isolasi dan perbaikan kabel panel oleh teknisi listrik bersertifikat untuk memastikan keamanan rumah Anda.';
            $draftTitle = 'Pemeriksaan Arus Listrik & MCB (' . mb_substr($damageDetail, 0, 45) . '...)';
            $draftDesc = "Membutuhkan teknisi listrik berpengalaman untuk menangani: {$damageDetail}. Kejadian: {$chronology}.";
        } elseif ($isKunci) {
            $categoryName = 'Tukang Kunci';
            $categorySlug = 'tukang-kunci';
            $possibleDamage = 'Kemungkinan pin silinder kunci aus, mekanisme kait pintu macet akibat karat/deformasi, atau patahan anak kunci di dalam lubang silinder.';
            $minPrice = 50000;
            $maxPrice = 200000;
            $explanation = 'Berdasarkan informasi yang diberikan, kendala pada daun pintu atau kunci rumah tangga ini diperkirakan membutuhkan penanganan pembukaan darurat dengan pick lock khusus dan penggantian master silinder agar pintu dapat terkunci kembali dengan aman tanpa merusak kusen.';
            $draftTitle = 'Bantuan Tukang Kunci Pintu Macet / Terkunci';
            $draftDesc = "Membutuhkan tukang kunci darurat untuk penanganan: {$damageDetail}. Lokasi siap didatangi segera.";
        } elseif ($isElektronik) {
            $categoryName = 'Teknisi Elektronik';
            $categorySlug = 'teknisi-elektronik';
            $possibleDamage = 'Kemungkinan kerusakan pada modul power supply (catu daya), kapasitor motor dinamo yang melemah, atau sensor termostat/timer peralatan.';
            $minPrice = 90000;
            $maxPrice = 350000;
            $explanation = 'Berdasarkan informasi yang diberikan, peralatan elektronik mengalami anomali fungsi yang diperkirakan berasal dari komponen kelistrikan internal atau keausan bearing mekanik. Diperlukan pengukuran multimeter dan pemeriksaan visual komponen di lokasi pengerjaan.';
            $draftTitle = 'Reparasi Peralatan Elektronik Rumah Tangga';
            $draftDesc = "Membutuhkan teknisi servis elektronik untuk perbaikan: {$damageDetail}. Gejala awal: {$chronology}.";
        } else {
            // General / Home Utility
            $categoryName = 'Teknisi Elektronik';
            $categorySlug = 'teknisi-elektronik';
            $possibleDamage = 'Kemungkinan terdapat keausan komponen mekanik atau kendala konektivitas listrik pada perangkat rumah tangga.';
            $minPrice = 75000;
            $maxPrice = 250000;
            $explanation = 'Berdasarkan informasi yang diberikan, gejala yang Anda deskripsikan memerlukan inspeksi langsung oleh teknisi berpengalaman untuk memastikan titik permasalahan utama dan estimasi penggantian suku cadang jika dibutuhkan.';
            $draftTitle = 'Permintaan Pemeriksaan & Servis Rumah Tangga';
            $draftDesc = "Membutuhkan teknisi untuk memeriksa kendala perbaikan: {$damageDetail}.";
        }

        return [
            'possible_damage' => $possibleDamage,
            'category_name' => $categoryName,
            'category_slug' => $categorySlug,
            'estimated_min_price' => $minPrice,
            'estimated_max_price' => $maxPrice,
            'explanation' => $explanation,
            'draft_post_title' => $draftTitle,
            'draft_post_description' => $draftDesc
        ];
    }

    /**
     * Call Google Gemini API (if key is supplied)
     *
     * @param string $damageDetail
     * @param string $chronology
     * @param string $apiKey
     * @param string|null $imagePath
     * @return array|null
     */
    private function callGeminiApi($damageDetail, $chronology, $apiKey, $imagePath = null) {
        $prompt = "Anda adalah asisten konsultan teknis perbaikan rumah tangga profesional di platform 'Teknisi Rumah Tangga'.\n"
                . "Analisis data keluhan pelanggan berikut ini:\n"
                . "- Detail Kerusakan: {$damageDetail}\n"
                . "- Kronologi Kejadian: {$chronology}\n\n"
                . "PENTING: Gunakan Bahasa Indonesia. Jangan menyatakan diagnosis sebagai kepastian mutlak, melainkan gunakan kata seperti 'kemungkinan', 'perkiraan', atau 'berdasarkan informasi yang diberikan'.\n"
                . "Kategori teknisi yang tersedia: 'Teknisi Listrik', 'Teknisi AC', 'Tukang Kunci', 'Teknisi Elektronik'.\n\n"
                . "Berikan output HANYA dalam format JSON valid tanpa format markdown lain dengan struktur key:\n"
                . "{\n"
                . '  "possible_damage": "penjelasan kemungkinan kerusakan", ' . "\n"
                . '  "category_name": "Teknisi AC / Teknisi Listrik / Tukang Kunci / Teknisi Elektronik", ' . "\n"
                . '  "category_slug": "teknisi-ac / teknisi-listrik / tukang-kunci / teknisi-elektronik", ' . "\n"
                . '  "estimated_min_price": 75000, ' . "\n"
                . '  "estimated_max_price": 250000, ' . "\n"
                . '  "explanation": "penjelasan singkat analisis", ' . "\n"
                . '  "draft_post_title": "draft judul postingan", ' . "\n"
                . '  "draft_post_description": "draft deskripsi postingan" ' . "\n"
                . "}";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . urlencode($apiKey);

        $postData = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0.2
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            $data = json_decode($response, true);
            $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $parsedJson = json_decode($rawText, true);
            if (!empty($parsedJson) && isset($parsedJson['possible_damage'])) {
                return $parsedJson;
            }
        }

        return null;
    }
}
