<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormResponse;

class ComexController extends Controller
{
    public function index(Request $request)
    {
        $companySearch = $request->input('company');

        $query = FormResponse::where('status', 'rejected')
            ->with(['user', 'monitoreoUser']);

        if ($companySearch) {
            $query->whereHas('fieldResponses', function ($q) use ($companySearch) {
                $q->where('value', 'like', "%{$companySearch}%")
                    ->whereHas('field', function ($f) {
                        $f->where('label', 'Transportadora');
                    });
            });
        }

        $rejectedInspections = $query->orderBy('updated_at', 'desc')->get();

        // Stats
        $totalRejected = $rejectedInspections->count();
        $totalInspections = FormResponse::count();
        $rejectionRate = $totalInspections > 0 ? round(($totalRejected / $totalInspections) * 100, 1) : 0;

        // Monthly Trend (Last 6 months)
        $monthlyTrend = FormResponse::where('status', 'rejected')
            ->selectRaw('DATE_FORMAT(updated_at, "%M %Y") as month, count(*) as count, month(updated_at) as m, year(updated_at) as y')
            ->groupBy('month', 'm', 'y')
            ->orderBy('y', 'desc')
            ->orderBy('m', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        // Reasons categorization (Simplified for now)
        $reasons = [
            'Documentación' => 0,
            'Daño Físico' => 0,
            'Sellos/Precintos' => 0,
            'Otros' => 0,
        ];

        foreach ($rejectedInspections as $ins) {
            $reason = strtolower($ins->getRejectionReason());
            if (str_contains($reason, 'docum') || str_contains($reason, 'papel')) {
                $reasons['Documentación']++;
            } elseif (str_contains($reason, 'daño') || str_contains($reason, 'roto') || str_contains($reason, 'golpe')) {
                $reasons['Daño Físico']++;
            } elseif (str_contains($reason, 'sello') || str_contains($reason, 'precint')) {
                $reasons['Sellos/Precintos']++;
            } else {
                $reasons['Otros']++;
            }
        }

        // Top Transportadoras for "Origin/Destination" chart
        $clientsData = [];
        foreach ($rejectedInspections as $ins) {
            $client = $ins->getFieldValue('Transportadora') ?? 'Desconocido';
            $clientsData[$client] = ($clientsData[$client] ?? 0) + 1;
        }
        arsort($clientsData);
        $topClients = array_slice($clientsData, 0, 5);

        return view('comex.index', compact(
            'rejectedInspections',
            'totalRejected',
            'rejectionRate',
            'monthlyTrend',
            'reasons',
            'topClients',
            'companySearch'
        ));
    }

    public function exportCsv(Request $request)
    {
        $companySearch = $request->input('company');

        $query = FormResponse::where('status', 'rejected')
            ->with(['user', 'monitoreoUser']);

        if ($companySearch) {
            $query->whereHas('fieldResponses', function ($q) use ($companySearch) {
                $q->where('value', 'like', "%{$companySearch}%")
                    ->whereHas('field', function ($f) {
                        $f->where('label', 'Transportadora');
                    });
            });
        }

        $rejectedInspections = $query->orderBy('updated_at', 'desc')->get();

        $filename = "reporte_rechazados_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($rejectedInspections) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['ID', 'Fecha', 'Contenedor', 'Transportadora', 'Inspector', 'Motivo Rechazo', 'Rechazado Por']);

            foreach ($rejectedInspections as $ins) {
                fputcsv($file, [
                    $ins->id,
                    $ins->updated_at->format('d/m/Y H:i'),
                    $ins->getContainerNumber(),
                    $ins->getFieldValue('Transportadora') ?? 'N/A',
                    $ins->user->name ?? 'N/A',
                    $ins->getRejectionReason(),
                    $ins->monitoreoUser->name ?? 'incumplimiento de la norma'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function searchCompanies(Request $request)
    {
        $query = $request->get('q');
        if (empty($query)) {
            return response()->json([]);
        }

        // Search unique customer names from rejected inspections
        $companies = \App\Models\FieldResponse::whereHas('field', function ($q) {
            $q->where('label', 'Transportadora');
        })
            ->whereHas('formResponse', function ($q) {
                $q->where('status', 'rejected');
            })
            ->where('value', 'LIKE', "%{$query}%")
            ->distinct()
            ->take(10)
            ->pluck('value');

        return response()->json($companies);
    }
}
