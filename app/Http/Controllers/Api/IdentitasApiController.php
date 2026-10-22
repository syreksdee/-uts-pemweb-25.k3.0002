<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IdentitasService;
use Illuminate\Http\JsonResponse;

class IdentitasApiController extends Controller
{
    protected IdentitasService $identitasService;

    public function __construct(IdentitasService $identitasService)
    {
        $this->identitasService = $identitasService;
    }

    /**
     * GET /api/identitas
     * Mengembalikan identitas pengembang: NIM, nama lengkap, foto, dan nama aplikasi.
     */
    public function index(): JsonResponse
    {
        $data = $this->identitasService->getIdentitas();

        return response()->json($data, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
