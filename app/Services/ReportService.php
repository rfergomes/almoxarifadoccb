<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ItemStatus;
use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementItem;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{
    public function getInventoryReport(?int $categoryId = null): Collection
    {
        $query = Material::with('category')->where('status', true);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->orderBy('name')->get();
    }

    public function getLowStockReport(): Collection
    {
        return Material::with('category')
            ->where('status', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('name')
            ->get();
    }

    public function getOverdueLoansReport(): Collection
    {
        return MovementItem::with(['movement.beneficiary', 'movement.destination', 'material.category'])
            ->where('status', ItemStatus::PENDING_RETURN)
            ->whereNotNull('expected_return_date')
            ->where('expected_return_date', '<', now()->startOfDay())
            ->get();
    }

    public function getMovementsReport(?string $startDate = null, ?string $endDate = null, ?string $type = null): Collection
    {
        $query = Movement::with(['user', 'beneficiary', 'destination', 'entryDocument', 'items.material']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($type) {
            $query->where('type', $type);
        }

        return $query->latest()->get();
    }

    public function getExpirationReport(string $filterType = 'all', int $daysThreshold = 30): Collection
    {
        $query = Material::with('category')->where('status', true);

        match ($filterType) {
            'expired' => $query->expired(),
            'expiring_soon' => $query->expiringSoon($daysThreshold),
            'valid' => $query->whereNotNull('expiration_date')->where('expiration_date', '>', now()->addDays($daysThreshold)),
            'no_expiration' => $query->whereNull('expiration_date'),
            default => $query->whereNotNull('expiration_date'),
        };

        return $query->orderBy('expiration_date', 'asc')->get();
    }

    public function getPatrimonyReport(): Collection
    {
        return Material::with('category')
            ->withPatrimony()
            ->orderBy('patrimony_code', 'asc')
            ->get();
    }
}

