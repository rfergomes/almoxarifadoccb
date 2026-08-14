<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        $reportType = $request->get('type', 'inventory');

        $data = match ($reportType) {
            'inventory' => $this->reportService->getInventoryReport($request->filled('category_id') ? (int)$request->category_id : null),
            'low_stock' => $this->reportService->getLowStockReport(),
            'overdue' => $this->reportService->getOverdueLoansReport(),
            'movements' => $this->reportService->getMovementsReport(
                $request->get('start_date'),
                $request->get('end_date'),
                $request->get('movement_type')
            ),
            'expiration' => $this->reportService->getExpirationReport($request->get('expiration_filter', 'all')),
            'patrimony' => $this->reportService->getPatrimonyReport(),
            default => $this->reportService->getInventoryReport(),
        };

        return view('reports.index', compact('categories', 'reportType', 'data'));
    }

    public function exportPdf(Request $request): Response
    {
        $reportType = $request->get('type', 'inventory');

        if ($reportType === 'inventory') {
            $materials = $this->reportService->getInventoryReport($request->filled('category_id') ? (int)$request->category_id : null);
            $pdf = Pdf::loadView('reports.pdf.inventory', compact('materials'));
            return $pdf->download('relatorio-posicao-estoque-ccb.pdf');
        }

        if ($reportType === 'low_stock') {
            $materials = $this->reportService->getLowStockReport();
            $pdf = Pdf::loadView('reports.pdf.inventory', compact('materials'));
            return $pdf->download('relatorio-estoque-baixo-ccb.pdf');
        }

        if ($reportType === 'overdue') {
            $items = $this->reportService->getOverdueLoansReport();
            $pdf = Pdf::loadView('reports.pdf.overdue_loans', compact('items'));
            return $pdf->download('relatorio-emprestimos-atrasados-ccb.pdf');
        }

        if ($reportType === 'expiration') {
            $materials = $this->reportService->getExpirationReport($request->get('expiration_filter', 'all'));
            $pdf = Pdf::loadView('reports.pdf.expiration', compact('materials'));
            return $pdf->download('relatorio-validade-produtos-ccb.pdf');
        }

        if ($reportType === 'patrimony') {
            $materials = $this->reportService->getPatrimonyReport();
            $pdf = Pdf::loadView('reports.pdf.patrimony', compact('materials'));
            return $pdf->download('relatorio-bens-patrimoniados-ccb.pdf');
        }

        $movements = $this->reportService->getMovementsReport(
            $request->get('start_date'),
            $request->get('end_date'),
            $request->get('movement_type')
        );
        $pdf = Pdf::loadView('reports.pdf.movements', compact('movements'));
        return $pdf->download('relatorio-movimentacoes-ccb.pdf');
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $reportType = $request->get('type', 'inventory');
        $filename = "relatorio-{$reportType}-ccb-" . date('Ymd-His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream(function () use ($reportType, $request) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($reportType === 'inventory' || $reportType === 'low_stock') {
                fputcsv($file, ['SKU', 'Nome Material', 'Categoria', 'Unidade', 'Estoque Atual', 'Estoque Mínimo', 'CA EPI']);
                $materials = $reportType === 'low_stock' 
                    ? $this->reportService->getLowStockReport() 
                    : $this->reportService->getInventoryReport($request->filled('category_id') ? (int)$request->category_id : null);

                foreach ($materials as $mat) {
                    fputcsv($file, [
                        $mat->code_sku,
                        $mat->name,
                        $mat->category?->name ?? 'Geral',
                        $mat->unit_measure,
                        $mat->current_stock,
                        $mat->minimum_stock,
                        $mat->ca_number ?? '-'
                    ]);
                }
            } elseif ($reportType === 'expiration') {
                fputcsv($file, ['SKU', 'Nome Material', 'Categoria', 'Estoque Atual', 'Data de Validade', 'Status de Validade']);
                $materials = $this->reportService->getExpirationReport($request->get('expiration_filter', 'all'));

                foreach ($materials as $mat) {
                    fputcsv($file, [
                        $mat->code_sku,
                        $mat->name,
                        $mat->category?->name ?? 'Geral',
                        $mat->current_stock . ' ' . $mat->unit_measure,
                        $mat->expiration_date?->format('d/m/Y') ?? 'Indefinida',
                        $mat->expirationStatus()->label()
                    ]);
                }
            } elseif ($reportType === 'patrimony') {
                fputcsv($file, ['Código Patrimônio', 'SKU', 'Nome do Equipamento/Ferramenta', 'Categoria', 'Estoque Atual']);
                $materials = $this->reportService->getPatrimonyReport();

                foreach ($materials as $mat) {
                    fputcsv($file, [
                        $mat->patrimony_code,
                        $mat->code_sku,
                        $mat->name,
                        $mat->category?->name ?? 'Geral',
                        $mat->current_stock . ' ' . $mat->unit_measure
                    ]);
                }
            } elseif ($reportType === 'overdue') {
                fputcsv($file, ['Código Mov.', 'Material', 'Beneficiário', 'Destino', 'Qtd. Pendente', 'Previsão Retorno']);
                $items = $this->reportService->getOverdueLoansReport();

                foreach ($items as $item) {
                    fputcsv($file, [
                        $item->movement?->code,
                        $item->material?->name,
                        $item->movement?->beneficiary?->name,
                        $item->movement?->destination?->name,
                        $item->pendingQuantity(),
                        $item->expected_return_date?->format('d/m/Y')
                    ]);
                }
            } else {
                fputcsv($file, ['Código', 'Data', 'Tipo', 'Beneficiário/Documento', 'Destino/Fornecedor', 'Qtd Itens', 'Status']);
                $movements = $this->reportService->getMovementsReport(
                    $request->get('start_date'),
                    $request->get('end_date'),
                    $request->get('movement_type')
                );

                foreach ($movements as $mov) {
                    fputcsv($file, [
                        $mov->code,
                        $mov->created_at->format('d/m/Y H:i'),
                        $mov->type->label(),
                        $mov->type === \App\Enums\MovementType::ENTRY ? $mov->entryDocument?->document_number : $mov->beneficiary?->name,
                        $mov->type === \App\Enums\MovementType::ENTRY ? $mov->entryDocument?->supplier_or_donor : $mov->destination?->name,
                        $mov->items->count(),
                        $mov->status->label()
                    ]);
                }
            }

            fclose($file);
        }, 200, $headers);
    }

    public function userManualPdf(): Response
    {
        $pdf = Pdf::loadView('reports.pdf.user_manual');
        return $pdf->download('manual-usuario-almoxarifado-ccb.pdf');
    }
}

