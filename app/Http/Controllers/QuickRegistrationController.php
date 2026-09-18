<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\StoreDestinationRequest;
use App\Http\Requests\StoreMaterialRequest;
use App\Models\Beneficiary;
use App\Models\Destination;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class QuickRegistrationController extends Controller
{
    public function beneficiary(StoreBeneficiaryRequest $request): JsonResponse
    {
        $beneficiary = Beneficiary::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Beneficiário cadastrado com sucesso!',
            'data' => [
                'id' => $beneficiary->id,
                'name' => $beneficiary->name . ($beneficiary->role_in_ccb ? " ({$beneficiary->role_in_ccb})" : ''),
            ],
        ], 201);
    }

    public function destination(StoreDestinationRequest $request): JsonResponse
    {
        $destination = Destination::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Destino cadastrado com sucesso!',
            'data' => [
                'id' => $destination->id,
                'name' => "{$destination->code} - {$destination->name}",
            ],
        ], 201);
    }

    public function material(StoreMaterialRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('notes', $data) && !Schema::hasColumn('materials', 'notes')) {
            unset($data['notes']);
        }

        try {
            $material = Material::create($data);
            $material->load('category');

            return response()->json([
                'success' => true,
                'message' => 'Material cadastrado com sucesso!',
                'data' => [
                    'id' => $material->id,
                    'code_sku' => $material->code_sku,
                    'name' => $material->name,
                    'current_stock' => $material->current_stock,
                    'unit_measure' => $material->unit_measure,
                    'notes' => $material->notes ?? null,
                    'label' => "{$material->code_sku} - {$material->name} (Atual: {$material->current_stock} {$material->unit_measure})",
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Erro no cadastro rápido de material: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível cadastrar o material. Detalhe: ' . $e->getMessage(),
            ], 500);
        }
    }
}
