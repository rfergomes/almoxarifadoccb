<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntryRequest;
use App\Models\Beneficiary;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Material;
use App\Models\Movement;
use App\Services\EntryService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EntryController extends Controller
{
    public function __construct(
        protected EntryService $entryService
    ) {}

    public function index(): View
    {
        $entries = Movement::with(['user', 'entryDocument.attachments', 'items.material'])
            ->where('type', \App\Enums\MovementType::ENTRY)
            ->latest()
            ->paginate(15);

        return view('entries.index', compact('entries'));
    }

    public function create(): View
    {
        $materials = Material::with('category')->where('status', true)->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $beneficiaries = Beneficiary::where('status', true)->orderBy('name')->get();
        $destinations = Destination::where('status', true)->orderBy('name')->get();

        return view('entries.create', compact('materials', 'categories', 'beneficiaries', 'destinations'));
    }

    public function store(StoreEntryRequest $request): RedirectResponse
    {
        try {
            $movement = $this->entryService->createEntry(
                $request->validated(),
                $request->user()->id
            );

            return redirect()
                ->route('movements.show', $movement)
                ->with('success', "Entrada {$movement->code} registrada com sucesso!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
