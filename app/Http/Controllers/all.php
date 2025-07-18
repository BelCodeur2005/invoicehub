<?php

// 1. DashboardController
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Proforma;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_invoices' => Invoice::count(),
            'total_proformas' => Proforma::count(),
            'total_clients' => Client::count(),
            'total_products' => Product::count(),
            'monthly_revenue' => Invoice::whereMonth('created_at', now()->month)
                                     ->where('status', 'paid')
                                     ->sum('total'),
            'pending_invoices' => Invoice::where('status', 'sent')->count(),
            'overdue_invoices' => Invoice::where('due_date', '<', now())
                                        ->where('status', '!=', 'paid')
                                        ->count(),
        ];

        $recent_invoices = Invoice::with('client')
                                 ->latest()
                                 ->take(5)
                                 ->get();

        $recent_proformas = Proforma::with('client')
                                   ->latest()
                                   ->take(5)
                                   ->get();

        return view('dashboard', compact('stats', 'recent_invoices', 'recent_proformas'));
    }
}

// 2. ClientController
// app/Http/Controllers/ClientController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{

    public function index()
    {
        $clients = Client::withCount(['invoices', 'proformas'])
                        ->paginate(15);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . ($client->id ?? 'NULL'),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'niu' => 'nullable|string|max:255',
            'rccm' => 'nullable|string|max:255',
            'bp' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ]);
        Client::create($validated);

        return redirect()->route('clients.index')
                        ->with('success', 'Client créé avec succès');
    }

    public function show(Client $client)
    {
        $client->load(['invoices', 'proformas']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'niu' => 'nullable|string|max:255',
            'rccm' => 'nullable|string|max:255',
            'bp' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
                        ->with('success', 'Client mis à jour avec succès');
    }


    public function destroy(Client $client)
    {
        if ($client->invoices()->count() > 0 || $client->proformas()->count() > 0) {
            return redirect()->route('clients.index')
                            ->with('error', 'Impossible de supprimer ce client car il a des factures ou proformas associées');
        }

        $client->delete();

        return redirect()->route('clients.index')
                        ->with('success', 'Client supprimé avec succès');
    }
}

// 3. ProductController
// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:product,service',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['tax_rate'] = $validated['tax_rate'] ?? 19.25;
        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('products.index')
                        ->with('success', 'Produit créé avec succès');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:product,service',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['tax_rate'] = $validated['tax_rate'] ?? 19.25;
        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('products.index')
                        ->with('success', 'Produit mis à jour avec succès');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
                        ->with('success', 'Produit supprimé avec succès');
    }
}

// 4. ProformaController
// app/Http/Controllers/ProformaController.php

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

class ProformaController extends Controller
{
    public function index()
    {
        $proformas = Proforma::with(['client', 'user'])
                            ->latest()
                            ->paginate(15);

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

        return view('proformas.edit', compact('proforma', 'clients', 'products'));
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
}

// 5. InvoiceController
// app/Http/Controllers/InvoiceController.php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['client', 'user'])
                          ->latest()
                          ->paginate(15);

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
                $invoiceItem = new InvoiceItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
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
            return back()->with('error', 'Erreur lors de la création de la facture');
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'user', 'items.product', 'proforma']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('invoices.show', $invoice)
                            ->with('error', 'Seules les factures en brouillon peuvent être modifiées');
        }

        $clients = Client::all();
        $products = Product::where('is_active', true)->get();
        $invoice->load(['items.product']);

        return view('invoices.edit', compact('invoice', 'clients', 'products'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('invoices.show', $invoice)
                            ->with('error', 'Seules les factures en brouillon peuvent être modifiées');
        }

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
                $invoiceItem = new InvoiceItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
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
            return back()->with('error', 'Erreur lors de la mise à jour de la facture');
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

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);

        return redirect()->route('invoices.show', $invoice)
                        ->with('success', 'Facture marquée comme payée');
    }

    public function markAsSent(Invoice $invoice)
    {
        $invoice->update(['status' => 'sent']);

        return redirect()->route('invoices.show', $invoice)
                        ->with('success', 'Facture marquée comme envoyée');
    }
}

// 6. UserController
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->canManageUsers()) {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::withCount(['invoices', 'proformas'])
                    ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,user',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur créé avec succès');
    }

    public function show(User $user)
    {
        $user->load(['invoices', 'proformas']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,user',
            'is_active' => 'boolean',
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                            ->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        if ($user->invoices()->count() > 0 || $user->proformas()->count() > 0) {
            return redirect()->route('users.index')
                            ->with('error', 'Impossible de supprimer cet utilisateur car il a des factures ou proformas associées');
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur supprimé avec succès');
    }
}
