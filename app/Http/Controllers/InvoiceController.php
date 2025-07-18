<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proforma;
use App\Models\Proforma_item;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Invoice_item;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\PDF as DomPDF;
 use App\Mail\SendInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\InvoiceStatusLog;
use App\Traits\HasStatusTracking; // <-- Vérifiez cette ligne

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'user'])
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
        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('date', '<=', $request->end_date);
        }

        $invoices = $query->paginate(7);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::all();
        $products = Product::where('is_active', true)->get();

        return view('invoices.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after:date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $invoice = Invoice::create([
                'client_id' => $validated['client_id'],
                'user_id' => auth()->id(),
                'number' => Invoice::generateNumber(),
                'date' => $validated['date'],
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $invoiceItem = new Invoice_item([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'invoice_id' => $invoice->id, // Ajoutez explicitement l'ID de la facture
                ]);

                $invoiceItem->calculateTotals();
                $invoice->items()->save($invoiceItem);
            }

            $invoice->calculateTotals();

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                            ->with('success', 'Facture créée avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la facture : ' . $e->getMessage());
        }

    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'user', 'items.product', 'proforma']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        // if ($invoice->status !== 'draft') {
        //     return redirect()->route('invoices.show', $invoice)
        //                     ->with('error', 'Seules les factures en brouillon peuvent être modifiées');
        // }

        $clients = Client::all();
        $products = Product::where('is_active', true)->get();
        $invoice->load(['items.product']);

            // Formater les dates pour les champs HTML date (YYYY-MM-DD)
        $invoice->date = $invoice->date ? $invoice->date->format('Y-m-d') : '';
        $invoice->due_date = $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '';

        // Préparation des données des items pour le frontend
        $items = $invoice->items->map(function($item) {
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

        return view('invoices.edit', compact('invoice', 'clients', 'products', 'items'));
    }


    public function update(Request $request, Invoice $invoice)
    {
        // if ($invoice->status !== 'draft') {
        //     return redirect()->route('invoices.show', $invoice)
        //                     ->with('error', 'Seules les factures en brouillon peuvent être modifiées');
        // }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after:date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $invoice->update([
                'client_id' => $validated['client_id'],
                'date' => $validated['date'],
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Supprimer les anciens items
            $invoice->items()->delete();

            // Créer les nouveaux items
            foreach ($validated['items'] as $item) {
                $invoiceItem = new Invoice_item([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'invoice_id' => $invoice->id, // Ajout explicite de l'ID de la facture
                ]);

                $invoiceItem->calculateTotals();
                $invoice->items()->save($invoiceItem);
            }

            $invoice->calculateTotals();

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                            ->with('success', 'Facture mise à jour avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise a jour de la facture : ' . $e->getMessage());
        }
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('invoices.index')
                            ->with('error', 'Seules les factures en brouillon peuvent être supprimées');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
                        ->with('success', 'Facture supprimée avec succès');
    }

    public function markAsSent(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|in:' . implode(',', array_keys(InvoiceStatusLog::getReasons()['draft_to_sent'])),
            'comment' => 'nullable|string|max:500',
            'send_email' => 'boolean',
            'email_addresses' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $invoice->markAsSent(
                $request->reason,
                $request->comment,
                [
                    'send_email' => $request->send_email,
                    'email_addresses' => $request->email_addresses
                ]
            );

            $reasonLabel = InvoiceStatusLog::getReasonLabel('draft', 'sent', $request->reason);

            return redirect()->route('invoices.index')
                ->with('success', "Facture marquée comme envoyée. Raison: {$reasonLabel}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|in:' . implode(',', array_keys(InvoiceStatusLog::getReasons()['sent_to_paid'])),
            'comment' => 'nullable|string|max:500',
            'payment_method' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
            'payment_date' => 'nullable|date',
            'amount_paid' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $paymentData = [
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'payment_date' => $request->payment_date ?? now(),
                'amount_paid' => $request->amount_paid ?? $invoice->total
            ];

            $invoice->markAsPaid($request->reason, $request->comment, $paymentData);

            $reasonLabel = InvoiceStatusLog::getReasonLabel('sent', 'paid', $request->reason);

            return redirect()->route('invoices.index')
                ->with('success', "Facture marquée comme payée. Raison: {$reasonLabel}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    public function markAsCancelled(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|in:' . implode(',', array_keys(InvoiceStatusLog::getReasons()['any_to_cancelled'])),
            'comment' => 'required|string|max:500' // Obligatoire pour les annulations
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $invoice->markAsCancelled($request->reason, $request->comment);

            $reasonLabel = InvoiceStatusLog::getReasonLabel($invoice->status, 'cancelled', $request->reason);

            return redirect()->route('invoices.index')
                ->with('success', "Facture annulée. Raison: {$reasonLabel}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    public function showStatusHistory(Invoice $invoice)
    {
        $history = $invoice->getStatusHistory();

        return view('invoices.status-history', compact('invoice', 'history'));
    }
    public function generatePdf(Invoice $invoice, DomPDF $pdf)
    {
        $invoice->load(['client', 'items.product']);

        $document = $pdf->loadView('pdf.invoice', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        return $document->stream('facture.pdf');
    }


    public function send(Request $request, Invoice $invoice)
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
            $mail->send(new SendInvoiceMail($invoice, $emailData));

            // Mettre à jour le statut de la facture
            $invoice->update([
                'status' => 'sent',
            ]);

            return back()->with('success', 'La facture a été envoyée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de l'envoi: " . $e->getMessage());
        }
    }
}
