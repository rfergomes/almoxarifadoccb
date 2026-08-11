<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'institution_name' => Setting::get('institution_name', 'CONGREGAÇÃO CRISTÃ NO BRASIL'),
            'administration_name' => Setting::get('administration_name', 'Gestão de Almoxarifado - Administração Nova Odessa'),
            'receipt_header_title' => Setting::get('receipt_header_title', 'Comprovante de Movimentação de Estoque'),
            'reports_header_title' => Setting::get('reports_header_title', 'Relatório Gerencial de Almoxarifado'),
            'inventory_header_title' => Setting::get('inventory_header_title', 'Termo Oficial de Inventário Geral Periódico'),
            'support_email' => Setting::get('support_email', 'rfergomes@gmail.com'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'institution_name' => ['required', 'string', 'max:200'],
            'administration_name' => ['required', 'string', 'max:200'],
            'receipt_header_title' => ['required', 'string', 'max:200'],
            'reports_header_title' => ['required', 'string', 'max:200'],
            'inventory_header_title' => ['required', 'string', 'max:200'],
            'support_email' => ['required', 'email', 'max:150'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('settings.index')->with('success', 'Configurações e títulos do sistema atualizados com sucesso!');
    }
}
