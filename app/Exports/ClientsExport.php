<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Client::withCount(['invoices', 'proformas']);

        // Appliquez les mêmes filtres que dans le contrôleur
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('niu', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%");
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom du Client',
            'NIU',
            'Email',
            'Téléphone',
            'Adresse',
            'Ville',
            'Pays',
            'Nombre de Factures',
            'Nombre de Proformas',
            'Date de Création',
            'Dernière Mise à Jour'
        ];
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->name,
            $client->niu ?? 'N/A',
            $client->email,
            $client->phone ?? 'N/A',
            $client->address ?? 'N/A',
            $client->city ?? 'N/A',
            $client->country ?? 'N/A',
            $client->invoices_count,
            $client->proformas_count,
            $client->created_at->format('d/m/Y H:i'),
            $client->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style pour la première ligne (en-têtes)
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFD9E1F2']
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
        ];
    }
}
