<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Proforma;
use App\Models\Client;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer le mois et l'année depuis la requête ou utiliser le mois actuel
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);
        $viewType = $request->get('view', 'monthly');

        // Créer une date Carbon pour le mois sélectionné
        $selectedDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        // Statistiques générales (inchangées)
        $stats = [
            'total_invoices' => Invoice::count(),
            'total_proformas' => Proforma::count(),
            'total_clients' => Client::count(),
            'total_products' => Product::count(),
            'pending_invoices' => Invoice::where('status', 'sent')->count(),
            'overdue_invoices' => Invoice::where('due_date', '<', now())
                                        ->where('status', '!=', 'paid')
                                        ->count(),
        ];

        // Statistiques pour le mois sélectionné
        $monthlyStats = [
            'monthly_revenue' => Invoice::whereMonth('created_at', $selectedMonth)
                                      ->whereYear('created_at', $selectedYear)
                                      ->where('status', 'paid')
                                      ->sum('total'),
            'monthly_invoices' => Invoice::whereMonth('created_at', $selectedMonth)
                                        ->whereYear('created_at', $selectedYear)
                                        ->count(),
            'monthly_proformas' => Proforma::whereMonth('created_at', $selectedMonth)
                                          ->whereYear('created_at', $selectedYear)
                                          ->count(),
            'monthly_clients' => Client::whereMonth('created_at', $selectedMonth)
                                      ->whereYear('created_at', $selectedYear)
                                      ->count(),
            'monthly_products' => Product::whereMonth('created_at', $selectedMonth)
                                        ->whereYear('created_at', $selectedYear)
                                        ->count(),
        ];

        // Statistiques annuelles pour l'année sélectionnée
        $yearlyStats = [
            'yearly_revenue' => Invoice::whereYear('created_at', $selectedYear)
                                      ->where('status', 'paid')
                                      ->sum('total'),
            'yearly_invoices' => Invoice::whereYear('created_at', $selectedYear)->count(),
            'yearly_proformas' => Proforma::whereYear('created_at', $selectedYear)->count(),
            'yearly_clients' => Client::whereYear('created_at', $selectedYear)->count(),
            'yearly_products' => Product::whereYear('created_at', $selectedYear)->count(),
        ];

        // Factures récentes selon le type de vue
        if ($viewType === 'general') {
            $recent_invoices = Invoice::with('client')
                                     ->latest()
                                     ->take(5)
                                     ->get();
        } else {
            $recent_invoices = Invoice::with('client')
                                     ->whereMonth('created_at', $selectedMonth)
                                     ->whereYear('created_at', $selectedYear)
                                     ->latest()
                                     ->take(5)
                                     ->get();
        }

        // Proformas récents selon le type de vue
        if ($viewType === 'general') {
            $recent_proformas = Proforma::with('client')
                                       ->latest()
                                       ->take(5)
                                       ->get();
        } else {
            $recent_proformas = Proforma::with('client')
                                       ->whereMonth('created_at', $selectedMonth)
                                       ->whereYear('created_at', $selectedYear)
                                       ->latest()
                                       ->take(5)
                                       ->get();
        }

        // Obtenir les années disponibles (de 2025 en remontant)
        $availableYears = $this->getAvailableYears();

        // Obtenir les mois disponibles
        $availableMonths = $this->getAvailableMonths();

        return view('dashboard', compact(
            'stats',
            'monthlyStats',
            'yearlyStats',
            'recent_invoices',
            'recent_proformas',
            'availableMonths',
            'availableYears',
            'selectedMonth',
            'selectedYear',
            'selectedDate',
            'viewType'
        ));
    }

    /**
     * Obtient les années disponibles (de 2025 en remontant)
     */
    private function getAvailableYears()
    {
        $years = [];
        $currentYear = now()->year;

        // Commencer par 2025 ou l'année actuelle si on est après 2025
        $startYear = max(2025, $currentYear);

        // Obtenir la première année avec des données pour limiter la plage
        $firstInvoiceYear = Invoice::oldest('created_at')->first()?->created_at->year;
        $firstProformaYear = Proforma::oldest('created_at')->first()?->created_at->year;

        // Déterminer l'année la plus ancienne avec des données
        $oldestDataYear = null;
        if ($firstInvoiceYear && $firstProformaYear) {
            $oldestDataYear = min($firstInvoiceYear, $firstProformaYear);
        } elseif ($firstInvoiceYear) {
            $oldestDataYear = $firstInvoiceYear;
        } elseif ($firstProformaYear) {
            $oldestDataYear = $firstProformaYear;
        }

        // Si on a des données, limiter jusqu'à l'année la plus ancienne, sinon jusqu'à 2020
        $endYear = $oldestDataYear ? max($oldestDataYear, 2020) : 2020;

        // Générer les années de la plus récente à la plus ancienne
        for ($year = $startYear; $year >= $endYear; $year--) {
            $years[] = $year;
        }

        return $years;
    }

    /**
     * Obtient la liste des mois (janvier à décembre)
     */
    private function getAvailableMonths()
    {
        return [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        ];
    }

    /**
     * Obtient le nom du mois en français
     */
    private function getMonthNameFrench($month)
    {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        return $months[$month];
    }

    /**
     * Obtient les statistiques de comparaison (mois précédent, année précédente)
     */
    private function getComparisonStats($selectedMonth, $selectedYear)
    {
        $previousMonth = $selectedMonth == 1 ? 12 : $selectedMonth - 1;
        $previousMonthYear = $selectedMonth == 1 ? $selectedYear - 1 : $selectedYear;
        $previousYear = $selectedYear - 1;

        return [
            'previous_month' => [
                'revenue' => Invoice::whereMonth('created_at', $previousMonth)
                                   ->whereYear('created_at', $previousMonthYear)
                                   ->where('status', 'paid')
                                   ->sum('total'),
                'invoices' => Invoice::whereMonth('created_at', $previousMonth)
                                    ->whereYear('created_at', $previousMonthYear)
                                    ->count(),
            ],
            'previous_year_same_month' => [
                'revenue' => Invoice::whereMonth('created_at', $selectedMonth)
                                   ->whereYear('created_at', $previousYear)
                                   ->where('status', 'paid')
                                   ->sum('total'),
                'invoices' => Invoice::whereMonth('created_at', $selectedMonth)
                                    ->whereYear('created_at', $previousYear)
                                    ->count(),
            ]
        ];
    }
}
