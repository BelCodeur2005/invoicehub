<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->number }}</title>
        {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .pad {
            word-wrap: break-word;
            white-space: nowrap;
        }
        .header img {
            width: 100%;
            height: auto;
        }

        .tone {
            background-color: #0070c0;
            color: white;
        }

        .gris {
            text-align: center;
            background-color: rgba(211, 211, 211, 0.3);
        }

        .pad {
            word-wrap: break-word;
            white-space: nowrap;
        }
        .tab2 {
            width: 50%;
        }

        .footer img {
            width: 100%;
            height: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .tab1 {
            width: 50%;
        }
        .items-section,.client-info,.invoice-info,.header{
            margin-bottom: 16px;
        }

        td,
        th {
            border: 1px solid rgba(211, 211, 211, 1);
            padding-left: 10px;
        }
        img {
        width: 100%;
        height: auto;
        border: none;
        }
        .footer{
        width: 100%;
        position: absolute;
        bottom: 0;
        left: 0;
        z-index: -1;
        }
        .center{
            text-align: center;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .invoice-container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/images/invoice-header.png'))) }}" alt="Header">
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-title">Facture : {{ $invoice->number }}</div>
            <div class="invoice-date">Date : {{ $invoice->date->format('Y-m-d') }}</div>
        </div>

        <!-- Client Info -->
        <div class="client-info">
            <table class="table-primary tab2">
                <tr>
                    <th class="tone">Client</th>
                    <td>{{ $invoice->client->name }}</td>
                </tr>
                <tr>
                    <th class="tone">Adresse</th>
                    <td>{{ $invoice->client->address ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th class="tone">BP</th>
                    <td>{{ $invoice->client->postal_code ?? 'N/A' }} {{ $invoice->client->city ?? '' }}</td>
                </tr>
                <tr>
                    <th class="tone">Téléphone</th>
                    <td>{{ $invoice->client->phone ?? 'N/A' }}</td>
                </tr>
                @if($invoice->client->niu)
                <tr>
                    <th class="tone">NIU</th>
                    <td>{{ $invoice->client->niu }}</td>
                </tr>
                @endif
                @if($invoice->client->rccm)
                <tr>
                    <th class="tone">RCCM</th>
                    <td>{{ $invoice->client->rccm }}</td>
                </tr>
                @endif
            </table>
        </div>
        <!-- Items Table -->
        <div class="items-section">
            <table class="table table-bordered items-table mb-0">
                <thead>
                    <tr>
                        <th class="tone">Désignation</th>
                        <th class="tone text-center">PU</th>
                        @if($invoice->items->some(fn($item) => ($item->product->type ?? 'product') === 'product' || $item->quantity > 1))
                        <th class="tone text-center">Quantité</th>
                        @endif
                        <th class="tone text-center">PT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="pad center">{{ number_format($item->price, 0, ',', ' ') }}</td>
                        @if($invoice->items->some(fn($i) => ($i->product->type ?? 'product') === 'product' || $i->quantity > 1))
                            <td class="center">
                                @if(($item->product->type ?? 'product') === 'service' && $item->quantity == 1)
                                    -
                                @else
                                    {{ $item->quantity }}
                                @endif
                            </td>
                        @endif
                        <td class="pad center">{{ number_format($item->price * $item->quantity, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                     <tr>
                        <th colspan="3" class="gris">TOTAL HT</th>
                        <th class="pad gris">{{ number_format($invoice->subtotal, 0, ',', ' ') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="gris">TVA {{ $invoice->items->first()->tax_rate ?? 19.25 }}%</th>
                        <th class="pad gris">{{ number_format($invoice->tax_amount, 0, ',', ' ') }}</th>
                    </tr>
                    <tr class="total-final" class="center">
                        <th colspan="3" class="gris">TOTAL TTC</th>
                        <th class="pad gris">{{ number_format($invoice->total, 0, ',', ' ') }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Bank Info -->
        <div class="bank-info mb-4">
            <table class="table table-bordered mb-0">
                <tr>
                    <th class="tone">Compte BGFIBank CMR</th>
                    <td>40035011304001361501101</td>
                </tr>
                <tr>
                    <th class="tone">Personne à contacter</th>
                    <td>merline.mefokenne@bridgetech-solutions.com</td>
                </tr>
            </table>
        </div>


        <!-- Footer -->
        <div class="footer">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/images/invoice-footer.png'))) }}" alt="Header">
      </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
