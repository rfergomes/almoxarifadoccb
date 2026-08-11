<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ReturnItemRequest;
use App\Http\Requests\StoreMovementRequest;
use App\Models\Beneficiary;
use App\Models\Destination;
use App\Models\Material;
use App\Models\Movement;
use App\Models\MovementItem;
use App\Services\LoanService;
use App\Services\StockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MovementController extends Controller
{
    public function __construct(
        protected StockService $stockService,
        protected LoanService $loanService
    ) {}

    public function index(): View
    {
        $movements = Movement::with(['user', 'beneficiary', 'destination', 'items.material'])
            ->latest()
            ->paginate(15);

        return view('movements.index', compact('movements'));
    }

    public function create(): View
    {
        $beneficiaries = Beneficiary::where('status', true)->orderBy('name')->get();
        $destinations = Destination::where('status', true)->orderBy('name')->get();
        $materials = Material::with('category')->where('status', true)->where('current_stock', '>', 0)->orderBy('name')->get();

        return view('movements.create', compact('beneficiaries', 'destinations', 'materials'));
    }

    public function store(StoreMovementRequest $request): RedirectResponse
    {
        try {
            $movement = $this->stockService->createMovement(
                $request->validated(),
                $request->user()->id
            );

            return redirect()
                ->route('movements.show', $movement)
                ->with('success', "Movimentação {$movement->code} registrada com sucesso!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Movement $movement): View
    {
        $movement->load(['user', 'beneficiary', 'destination', 'entryDocument', 'items.material.category']);

        return view('movements.show', compact('movement'));
    }

    public function exportPdf(Movement $movement): Response
    {
        $movement->load(['user', 'beneficiary', 'destination', 'entryDocument', 'items.material.category']);
        $pdf = Pdf::loadView('movements.pdf', compact('movement'));
        return $pdf->download("comprovante-{$movement->code}.pdf");
    }

    public function returnItem(ReturnItemRequest $request, MovementItem $item): RedirectResponse
    {
        try {
            $this->loanService->processReturn(
                $item,
                (int) $request->validated('quantity'),
                $request->validated('notes')
            );

            return redirect()
                ->route('movements.show', $item->movement_id)
                ->with('success', 'Devolução do item registrada com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
