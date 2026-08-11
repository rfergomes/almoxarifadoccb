<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function index(): View
    {
        $materials = Material::with('category')->latest()->paginate(15);
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

        return redirect()->route('materials.index')->with(
            'success',
            "Estoque do material '{$material->name}' ajustado de {$oldStock} para {$newStock} unidades. Justificativa: {$data['justification']}"
        );
    }
}
