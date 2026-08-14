<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ItemStatus;
use App\Enums\MovementStatus;
use App\Enums\MovementType;
use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementItem;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalStockItems = Material::where('status', true)->count();

        // Alertas de estoque mínimo
        $lowStockMaterials = Material::with('category')
            ->where('status', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->get();

        // Empréstimos em atraso
        $overdueItems = MovementItem::with(['movement.beneficiary', 'movement.destination', 'material'])
            ->where('status', ItemStatus::PENDING_RETURN)
            ->whereNotNull('expected_return_date')
            ->where('expected_return_date', '<', now()->startOfDay())
            ->get();

        // Atualiza status das movimentações correspondentes para OVERDUE
        foreach ($overdueItems as $item) {
            if ($item->movement->status !== MovementStatus::OVERDUE) {
                $item->movement->update(['status' => MovementStatus::OVERDUE]);
            }
        }

        // EPIs com CA a vencer nos próximos 30 dias ou vencidos
        $expiringEpis = Material::with('category')
            ->whereHas('category', fn ($q) => $q->where('name', 'EPI'))
            ->whereNotNull('ca_validity')
            ->where('ca_validity', '<=', now()->addDays(30))
            ->get();

        // Movimentações recentes
        $recentMovements = Movement::with(['beneficiary', 'destination', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Produtos vencidos e a vencer
        $expiredMaterialsCount = Material::expired()->count();
        $expiringSoonMaterialsCount = Material::expiringSoon(30)->count();
        $patrimonyMaterialsCount = Material::withPatrimony()->count();
        $expiredMaterials = Material::expired()->with('category')->get();
        $expiringSoonMaterials = Material::expiringSoon(30)->with('category')->get();

        return view('dashboard.index', compact(
            'totalStockItems',
            'lowStockMaterials',
            'overdueItems',
            'expiringEpis',
            'recentMovements',
            'expiredMaterialsCount',
            'expiringSoonMaterialsCount',
            'patrimonyMaterialsCount',
            'expiredMaterials',
            'expiringSoonMaterials'
        ));
    }
}
