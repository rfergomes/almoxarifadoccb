<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocumentType;
use App\Enums\ItemStatus;
use App\Enums\MovementStatus;
use App\Enums\MovementType;
use App\Models\EntryDocument;
use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EntryService
{
    public function __construct(
        protected AttachmentService $attachmentService
    ) {}

    /**
     * Registra um documento de entrada (NF/Doação) e incrementa o saldo de estoque dos materiais.
     */
    public function createEntry(array $data, int $userId): Movement
    {
        return DB::transaction(function () use ($data, $userId) {
            // 1. Grava o documento de entrada
            $entryDoc = EntryDocument::create([
                'document_number' => $data['document_number'],
                'document_type' => DocumentType::from($data['document_type']),
                'supplier_or_donor' => $data['supplier_or_donor'],
                'total_amount' => $data['total_amount'] ?? null,
                'issued_at' => $data['issued_at'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // 1.1 Se houver anexo de documento enviado, faz o upload
            if (isset($data['document_file']) && $data['document_file'] instanceof \Illuminate\Http\UploadedFile) {
                $this->attachmentService->uploadAttachment($data['document_file'], $entryDoc, $userId, 'entries');
            }

            // 2. Gera a movimentação de entrada
            $code = 'ENT-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $movement = Movement::create([
                'code' => $code,
                'user_id' => $userId,
                'entry_document_id' => $entryDoc->id,
                'type' => MovementType::ENTRY,
                'status' => MovementStatus::COMPLETED,
                'notes' => $data['notes'] ?? null,
            ]);

            // 3. Processa os itens e incrementa o estoque
            foreach ($data['items'] as $itemData) {
                /** @var Material $material */
                $material = Material::findOrFail($itemData['material_id']);
                $quantity = (int) $itemData['quantity'];

                // Incrementa o saldo disponível
                $material->increment('current_stock', $quantity);

                MovementItem::create([
                    'movement_id' => $movement->id,
                    'material_id' => $material->id,
                    'quantity' => $quantity,
                    'returned_quantity' => 0,
                    'status' => ItemStatus::DELIVERED,
                ]);
            }

            return $movement;
        });
    }
}
