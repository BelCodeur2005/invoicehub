<?php

namespace App\Http\Controllers;

use App\Models\Proforma;
use App\Models\Proforma_item;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Invoice_item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\PDF as DomPDF;
 use App\Mail\SendProformaMail;
use Illuminate\Support\Facades\Mail;
class ProformaController extends Controller
{
    public function index(Request $request)
    {
        $query = Proforma::with(['client', 'user'])
                        ->latest();

        // Filtre par recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%$search%")
                ->orWhereHas('client', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            });
        }

        // Filtre par statut
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filtre par date
        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('date', '<=', $request->date_to);
        }

        $proformas = $query->paginate(7);

        return view('proformas.index', compact('proformas'));
    }

    public function create()
    {
        $clients = Client::all();
        $products = Product::where('is_active', true)->get();

        return view('proformas.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'valid_until' => 'required|date|after:date',
            'notes' => 'nullable|string',
            'conditionPaiement' => 'nullable|string',
            'delaiDeploiment' => 'nullable|string',
            'garantieMateriel' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $proforma = Proforma::create([
                'client_id' => $validated['client_id'],
                'user_id' => auth()->id(),
                'number' => Proforma::generateNumber(),
                'date' => $validated['date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'] ?? null,
                'conditionPaiement' => $validated['conditionPaiement'] ?? null,
                'delaiDeploiment' => $validated['delaiDeploiment'] ?? null,
                'garantieMateriel' => $validated['garantieMateriel'] ?? null,
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $item) {
                $proformaItem = new Proforma_item([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'proforma_id' => $proforma->id, // Ajout explicite de l'ID de la facture
                ]);

                $proformaItem->calculateTotals();
                $proforma->items()->save($proformaItem);
            }

            $proforma->calculateTotals();

            DB::commit();

            return redirect()->route('proformas.show', $proforma)
                            ->with('success', 'Proforma créé avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la création du proforma');
        }
    }
    public function edit(Proforma $proforma)
    {
        $clients = Client::all();
        $products = Product::where('is_active', true)->get();
        $proforma->load(['items.product']);

        // Préparation des données des items pour le frontend
        $items = $proforma->items->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'tax_rate' => $item->tax_rate,
                'total' => $item->total,
                // Vous pouvez ajouter d'autres champs si nécessaire
                'product_name' => $item->product->name ?? '', // Si vous avez chargé la relation
                'product_price' => $item->product->price ?? 0 // Si vous avez chargé la relation
            ];
        });

        return view('proformas.edit', compact('proforma', 'clients', 'products','items'));
    }

    public function update(Request $request, Proforma $proforma)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'valid_until' => 'required|date|after:date',
            'notes' => 'nullable|string',
            'conditionPaiement' => 'nullable|string',
            'delaiDeploiment' => 'nullable|string',
            'garantieMateriel' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $proforma->update([
                'client_id' => $validated['client_id'],
                'date' => $validated['date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'] ?? null,
                'conditionPaiement' => $validated['conditionPaiement'] ?? null,
                'delaiDeploiment' => $validated['delaiDeploiment'] ?? null,
                'garantieMateriel' => $validated['garantieMateriel'] ?? null,
            ]);

            $proforma->items()->delete();

            foreach ($validated['items'] as $item) {
                $proformaItem = new Proforma_item([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'proforma_id' => $proforma->id, // Ajout explicite de l'ID de la facture
                ]);

                $proformaItem->calculateTotals();
                $proforma->items()->save($proformaItem);
            }

            $proforma->calculateTotals();

            DB::commit();

            return redirect()->route('proformas.show', $proforma)
                            ->with('success', 'Proforma mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la mise à jour du proforma');
        }
    }

    public function show(Proforma $proforma)
    {
        $proforma->load(['client', 'user', 'items.product']);
        return view('proformas.show', compact('proforma'));
    }
    public function destroy(Proforma $proforma)
    {
        if ($proforma->status !== 'draft') {
            return redirect()->route('proformas.index')
                            ->with('error', 'Seuls les proformas en brouillon peuvent être supprimés');
        }

        $proforma->delete();

        return redirect()->route('proformas.index')
                        ->with('success', 'Proforma supprimé avec succès');
    }
    public function generatePdf(Proforma $proforma, DomPDF $pdf)
    {
        $proforma->load(['client', 'items.product']);

        $document = $pdf->loadView('pdf.proforma', compact('proforma'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        return $document->stream('proforma.pdf');
    }
    public function convertToInvoice(Proforma $proforma)
    {
        if ($proforma->status !== 'accepted') {
            return redirect()->route('proformas.show', $proforma)
                            ->with('error', 'Seuls les proformas acceptés peuvent être convertis en facture');
        }

        if ($proforma->invoice) {
            return redirect()->route('invoices.show', $proforma->invoice)
                            ->with('info', 'Ce proforma a déjà été converti en facture');
        }

        DB::beginTransaction();

        try {
            $invoice = Invoice::create([
                'client_id' => $proforma->client_id,
                'user_id' => auth()->id(),
                'proforma_id' => $proforma->id,
                'number' => Invoice::generateNumber(),
                'date' => now(),
                'due_date' => now()->addDays(30),
                'subtotal' => $proforma->subtotal,
                'tax_amount' => $proforma->tax_amount,
                'total' => $proforma->total,
                'notes' => $proforma->notes,
            ]);

            foreach ($proforma->items as $proformaItem) {
                Invoice_item::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $proformaItem->product_id,
                    'quantity' => $proformaItem->quantity,
                    'price' => $proformaItem->price,
                    'tax_rate' => $proformaItem->tax_rate,
                    'subtotal' => $proformaItem->subtotal,
                    'tax_amount' => $proformaItem->tax_amount,
                    'total' => $proformaItem->total,
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                            ->with('success', 'Proforma converti en facture avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la conversion du proforma');
        }
    }
    public function send(Request $request, Proforma $proforma)
    {
        $request->validate([
            'to' => 'required|email',
            'cc' => 'nullable|string',
            'bcc' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            // Préparer les données d'email
            $emailData = [
                'to' => $request->to,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            // Traiter CC si présent
            if ($request->cc) {
                $emailData['cc'] = array_map('trim', explode(',', $request->cc));
            }

            // Traiter BCC si présent
            if ($request->bcc) {
                $emailData['bcc'] = array_map('trim', explode(',', $request->bcc));
            }

            // Envoyer l'email
            $mail = Mail::to($emailData['to']);

            // Ajouter CC si présent
            if (isset($emailData['cc'])) {
                $mail->cc($emailData['cc']);
            }

            // Ajouter BCC si présent
            if (isset($emailData['bcc'])) {
                $mail->bcc($emailData['bcc']);
            }

            // Envoyer avec les données personnalisées
            $mail->send(new SendProformaMail($proforma, $emailData));

            // Mettre à jour le statut de la facture
            $proforma->update([
                'status' => 'sent',
            ]);

            return back()->with('success', 'La Proforma a été envoyée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de l'envoi: " . $e->getMessage());
        }
    }
}
