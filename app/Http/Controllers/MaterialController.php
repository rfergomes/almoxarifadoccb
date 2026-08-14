<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Models\Category;
use App\Models\Material;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function __construct(
        protected AttachmentService $attachmentService
    ) {}

    public function index(Request $request): View
    {
        $query = Material::with('category');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code_sku', 'like', "%{$search}%")
                  ->orWhere('patrimony_code', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($expirationStatus = $request->input('expiration_status')) {
            match ($expirationStatus) {
                'expired' => $query->expired(),
                'expiring_soon' => $query->expiringSoon(30),
                'valid' => $query->whereNotNull('expiration_date')->where('expiration_date', '>', now()->addDays(30)),
                'no_expiration' => $query->whereNull('expiration_date'),
                default => null,
            };
        }

        if ($request->input('has_patrimony') === '1') {
            $query->withPatrimony();
        }

        $materials = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('materials.index', compact('materials', 'categories'));
    }

    public function store(StoreMaterialRequest $request): RedirectResponse
    {
        Material::create($request->validated());
        return redirect()->route('materials.index')->with('success', 'Material cadastrado com sucesso!');
    }

    public function update(Request $request, Material $material): RedirectResponse
    {
        $data = $request->validate([
            'code_sku' => ['required', 'string', 'max:50', Rule::unique('materials', 'code_sku')->ignore($material->id)],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_measure' => ['required', 'string', 'max:20'],
            'is_returnable' => ['required', 'boolean'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'ca_number' => ['nullable', 'string', 'max:50'],
            'ca_validity' => ['nullable', 'date'],
            'expiration_date' => ['nullable', 'date'],
            'patrimony_code' => ['nullable', 'string', 'max:50', Rule::unique('materials', 'patrimony_code')->ignore($material->id)],
            'status' => ['required', 'boolean'],
        ]);

        // Impede explicitamente a alteração direta de estoque no formulário de edição cadastral
        unset($data['current_stock']);

        $material->update($data);
        return redirect()->route('materials.index')->with('success', 'Cadastro do material atualizado com sucesso! (Estoque inalterado)');
    }

    public function adjustStock(Request $request, Material $material): RedirectResponse
    {
        $data = $request->validate([
            'new_stock' => ['required', 'integer', 'min:0'],
            'justification' => ['required', 'string', 'min:5', 'max:500'],
            'attachment_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'new_stock.required' => 'Informe a contagem atual do estoque.',
            'justification.required' => 'Informe a justificativa do ajuste de inventário.',
            'justification.min' => 'A justificativa deve conter no mínimo 5 caracteres.',
        ]);

        $oldStock = $material->current_stock;
        $newStock = (int) $data['new_stock'];

        $material->update([
            'current_stock' => $newStock,
        ]);

        if ($request->hasFile('attachment_file')) {
            $this->attachmentService->uploadAttachment(
                $request->file('attachment_file'),
                $material,
                $request->user()->id,
                'adjustments'
            );
        }

        return redirect()->route('materials.index')->with(
            'success',
            "Estoque de {$material->name} ajustado de {$oldStock} para {$newStock} unidades com sucesso!"
        );
    }
}
