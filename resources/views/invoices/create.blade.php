@extends('layouts.app')
@section('title', 'Nouvelle Facture')
@section('page-title', 'Créer une Nouvelle Facture')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouvelle Facture</h1>
            <p class="mt-2 text-sm text-gray-600">Créez une nouvelle facture pour vos clients</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la liste
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informations de la facture</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6" x-data="invoiceForm()">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Client -->
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select id="client_id"
                                name="client_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('client_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} - {{ $client->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de la facture <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="date"
                               name="date"
                               value="{{ old('date', date('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date') border-red-500 @enderror"
                               required>
                        @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date d'échéance <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="due_date"
                               name="due_date"
                               value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('due_date') border-red-500 @enderror"
                               required>
                        @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea id="notes"
                                  name="notes"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                  placeholder="Notes additionnelles...">{{ old('notes') }}</textarea>
                        @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Items Section -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Articles</h3>
                        <button type="button"
                                @click="addItem()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Ajouter un article
                        </button>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                                    <!-- Product -->
                                    <div>
                                        <label :for="'product_' + index" class="block text-sm font-medium text-gray-700 mb-2">
                                            Produit <span class="text-red-500">*</span>
                                        </label>
                                        <select :id="'product_' + index"
                                                :name="'items[' + index + '][product_id]'"
                                                x-model="item.product_id"
                                                @change="updateProductPrice(index)"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                required>
                                            <option value="">Sélectionnez un produit</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                    {{ $product->name }} - {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div>
                                        <label :for="'quantity_' + index" class="block text-sm font-medium text-gray-700 mb-2">
                                            Quantité <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number"
                                               :id="'quantity_' + index"
                                               :name="'items[' + index + '][quantity]'"
                                               x-model="item.quantity"
                                               @input="calculateItemTotal(index)"
                                               min="1"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               required>
                                    </div>

                                    <!-- Price -->
                                    <div>
                                        <label :for="'price_' + index" class="block text-sm font-medium text-gray-700 mb-2">
                                            Prix unitaire <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number"
                                               :id="'price_' + index"
                                               :name="'items[' + index + '][price]'"
                                               x-model="item.price"
                                               @input="calculateItemTotal(index)"
                                               step="0.01"
                                               min="0"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               required>
                                    </div>

                                    <!-- Tax Rate -->
                                    <div>
                                        <label :for="'tax_rate_' + index" class="block text-sm font-medium text-gray-700 mb-2">
                                            TVA (%) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number"
                                               :id="'tax_rate_' + index"
                                               :name="'items[' + index + '][tax_rate]'"
                                               x-model="item.tax_rate"
                                               @input="calculateItemTotal(index)"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               required>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-700">
                                            Total: <span x-text="formatPrice(item.total)" class="font-bold text-blue-600"></span>
                                        </span>
                                        <button type="button"
                                                @click="removeItem(index)"
                                                class="text-red-600 hover:text-red-800 p-1"
                                                title="Supprimer cet article">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Add Item Button (when no items) -->
                    <div x-show="items.length === 0" class="text-center py-8">
                        <div class="text-gray-500 mb-4">
                            <i class="fas fa-box-open text-4xl mb-2"></i>
                            <p>Aucun article ajouté</p>
                        </div>
                        <button type="button"
                                @click="addItem()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Ajouter le premier article
                        </button>
                    </div>
                </div>

                <!-- Summary -->
                <div x-show="items.length > 0" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total HT:</span>
                            <span x-text="formatPrice(subtotal)" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">TVA:</span>
                            <span x-text="formatPrice(totalTax)" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-300 pt-2">
                            <span>Total TTC:</span>
                            <span x-text="formatPrice(total)" class="text-blue-600"></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('invoices.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Créer la facture
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function invoiceForm() {
    return {
        items: [],
        subtotal: 0,
        totalTax: 0,
        total: 0,

        addItem() {
            this.items.push({
                product_id: '',
                quantity: 1,
                price: 0,
                tax_rate: 19.25,
                total: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },

        updateProductPrice(index) {
            const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
            const option = select.options[select.selectedIndex];
            if (option && option.dataset.price) {
                this.items[index].price = parseFloat(option.dataset.price);
                this.calculateItemTotal(index);
            }
        },

        calculateItemTotal(index) {
            const item = this.items[index];
            const subtotal = item.quantity * item.price;
            const tax = subtotal * (item.tax_rate / 100);
            item.total = subtotal + tax;
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => {
                return sum + (item.quantity * item.price);
            }, 0);

            this.totalTax = this.items.reduce((sum, item) => {
                return sum + (item.quantity * item.price * (item.tax_rate / 100));
            }, 0);

            this.total = this.subtotal + this.totalTax;
        },

        formatPrice(value) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XAF',
                minimumFractionDigits: 0
            }).format(value || 0);
        },

        init() {
            this.addItem();
        }
    }
}
</script>
@endsection
