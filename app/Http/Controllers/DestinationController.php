<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreDestinationRequest;
use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(): View
    {
        $destinations = Destination::latest()->paginate(15);
        return view('destinations.index', compact('destinations'));
    }

    public function store(StoreDestinationRequest $request): RedirectResponse
    {
        Destination::create($request->validated());
        return redirect()->route('destinations.index')->with('success', 'Destino cadastrado com sucesso!');
    }

    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('destinations', 'code')->ignore($destination->id)],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ]);

        $destination->update($data);
        return redirect()->route('destinations.index')->with('success', 'Cadastro do destino atualizado com sucesso!');
    }
}
