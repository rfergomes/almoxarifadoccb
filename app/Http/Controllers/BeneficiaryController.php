<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBeneficiaryRequest;
use App\Models\Beneficiary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BeneficiaryController extends Controller
{
    public function index(): View
    {
        $beneficiaries = Beneficiary::latest()->paginate(15);
        return view('beneficiaries.index', compact('beneficiaries'));
    }

    public function store(StoreBeneficiaryRequest $request): RedirectResponse
    {
        Beneficiary::create($request->validated());
        return redirect()->route('beneficiaries.index')->with('success', 'Beneficiário cadastrado com sucesso!');
    }

    public function update(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'document_cpf' => ['nullable', 'string', 'max:20', Rule::unique('beneficiaries', 'document_cpf')->ignore($beneficiary->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_in_ccb' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'boolean'],
        ]);

        $beneficiary->update($data);
        return redirect()->route('beneficiaries.index')->with('success', 'Cadastro do beneficiário atualizado com sucesso!');
    }
}
