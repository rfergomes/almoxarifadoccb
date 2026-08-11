<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index(): View
    {
        $inventories = Inventory::with(['user', 'items'])->latest()->paginate(15);
        return view('inventories.index', compact('inventories'));
    }

    public function create(): View
    {
        return view('inventories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'title.required' => 'Informe o título do inventário geral.',
        ]);

        try {
            $inventory = $this->inventoryService->startInventory(
                $request->title,
                $request->user()->id,
                $request->notes
            );

            return redirect()
                ->route('inventories.show', $inventory)
                ->with('success', "Inventário Geral '{$inventory->title}' iniciado com sucesso!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Inventory $inventory): View
    {
        $inventory->load(['user', 'items.material.category']);
        return view('inventories.show', compact('inventory'));
    }

    public function saveCounts(Request $request, Inventory $inventory): RedirectResponse
    {
        if ($inventory->isCompleted()) {
            return back()->with('error', 'Este inventário já foi concluído e não pode mais ser alterado.');
        }

        $this->inventoryService->saveCounts($inventory, $request->input('items', []));

        return back()->with('success', 'Contagens salvas com sucesso!');
    }

    public function complete(Request $request, Inventory $inventory): RedirectResponse
    {
        if ($inventory->isCompleted()) {
            return back()->with('error', 'Este inventário já foi concluído.');
        }

        // Salva as contagens antes de finalizar
        if ($request->has('items')) {
            $this->inventoryService->saveCounts($inventory, $request->input('items', []));
        }

        $this->inventoryService->completeInventory($inventory);

        return redirect()
            ->route('inventories.show', $inventory)
            ->with('success', "Inventário Geral '{$inventory->code}' concluído com sucesso! Os saldos de estoque foram atualizados.");
    }

    public function pdf(Inventory $inventory): Response
    {
        $inventory->load(['user', 'items.material.category']);
        $pdf = Pdf::loadView('inventories.pdf', compact('inventory'));
        return $pdf->download("inventario-geral-{$inventory->code}.pdf");
    }
}
