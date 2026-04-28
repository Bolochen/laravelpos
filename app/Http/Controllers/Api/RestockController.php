<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RestockService;
use Illuminate\Http\Request;

class RestockController extends Controller
{
    public function __construct(private RestockService $restockService) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string'],
        ]);

        $restock = $this->restockService->restock($data);

        return response()->json([
            'message' => 'Restock success',
            'data' => $restock,
        ], 201);
    }
}