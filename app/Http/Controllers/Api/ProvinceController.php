<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProvinceController extends Controller
{
    private const CAS_API_BASE = 'https://production.cas.so/address-kit';

    /**
     * Get all provinces
     * GET /api/provinces?effectiveDate=latest
     */
    public function getProvinces(Request $request)
    {
        $effectiveDate = $request->query('effectiveDate', 'latest');
        
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Laravel/Stylex'
                ]
            ]);

            $url = self::CAS_API_BASE . "/{$effectiveDate}/provinces";
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return response()->json([
                    'error' => 'Không thể kết nối đến API danh mục'
                ], 500);
            }

            $data = json_decode($response, true);

            // Cas API returns { "requestId": "...", "provinces": [...] }
            if (isset($data['provinces'])) {
                return response()->json(['data' => $data['provinces']]);
            } else if (isset($data['data'])) {
                return response()->json($data);
            } else if (is_array($data) && isset($data[0])) {
                return response()->json(['data' => $data]);
            } else {
                return response()->json([
                    'error' => 'Format dữ liệu không hợp lệ',
                    'url' => $url,
                    'received_keys' => array_keys($data ?? [])
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get communes by province
     * GET /api/communes?provinceID=01&effectiveDate=latest
     */
    public function getCommunes(Request $request)
    {
        $provinceID = $request->query('provinceID');
        $effectiveDate = $request->query('effectiveDate', 'latest');

        if (!$provinceID) {
            return response()->json([
                'error' => 'Tham số provinceID là bắt buộc'
            ], 400);
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Laravel/Stylex'
                ]
            ]);

            $url = self::CAS_API_BASE . "/{$effectiveDate}/provinces/{$provinceID}/communes";
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return response()->json([
                    'error' => 'Không thể kết nối đến API danh mục'
                ], 500);
            }

            $data = json_decode($response, true);

            // Cas API returns { "requestId": "...", "communes": [...] }
            if (isset($data['communes'])) {
                return response()->json(['data' => $data['communes']]);
            } else if (isset($data['data'])) {
                return response()->json($data);
            } else if (is_array($data) && isset($data[0])) {
                return response()->json(['data' => $data]);
            } else {
                return response()->json([
                    'error' => 'Format dữ liệu không hợp lệ',
                    'url' => $url,
                    'received_keys' => array_keys($data ?? [])
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert old 3-level address to new 2-level format
     * POST /api/address-convert
     * 
     * Request body:
     * {
     *   "provinceCode": "01",
     *   "districtCode": "001",
     *   "communeCode": "00001",
     *   "effectiveDate": "latest"
     * }
     */
    public function convertAddress(Request $request)
    {
        $validated = $request->validate([
            'provinceCode' => 'required|string',
            'districtCode' => 'nullable|string',
            'communeCode' => 'nullable|string',
            'effectiveDate' => 'nullable|string'
        ]);

        $effectiveDate = $validated['effectiveDate'] ?? 'latest';
        
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Laravel/Stylex',
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' => json_encode([
                        'provinceCode' => $validated['provinceCode'],
                        'districtCode' => $validated['districtCode'] ?? null,
                        'communeCode' => $validated['communeCode'] ?? null
                    ])
                ]
            ]);

            $url = self::CAS_API_BASE . "/{$effectiveDate}/convert";
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return response()->json([
                    'error' => 'Không thể kết nối đến API danh mục'
                ], 500);
            }

            $data = json_decode($response, true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}

