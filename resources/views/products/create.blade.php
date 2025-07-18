@extends('layouts.app')
@section('title', 'Nouveau Produit')
@section('page-title', 'Créer un Nouveau Produit')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau Produit</h1>
            <p class="mt-2 text-sm text-gray-600">Créez un nouveau produit ou service</p>
        </div>
        <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la liste
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informations du produit</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du produit <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Nom du produit ou service">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select id="type"
                                name="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                            <option value="">Sélectionnez le type</option>
                            <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Produit</option>
                            <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
                        </select>
                        @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Prix unitaire (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number"
                                   id="price"
                                   name="price"
                                   value="{{ old('price') }}"
                                   min="0"
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-500 @enderror"
                                   placeholder="0.00">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">FCFA</span>
                            </div>
                        </div>
                        @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax Rate -->
                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Taux de taxe (%)
                        </label>
                        <div class="relative">
                            <input type="number"
                                   id="tax_rate"
                                   name="tax_rate"
                                   value="{{ old('tax_rate', 19.25) }}"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tax_rate') border-red-500 @enderror"
                                   placeholder="19.25">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">%</span>
                            </div>
                        </div>
                        @error('tax_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <div class="flex items-center">
                            <input type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">
                                Actif
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Un produit actif peut être utilisé dans les factures</p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                              placeholder="Description détaillée du produit ou service...">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price Preview -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Aperçu des prix</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-sm text-gray-600">Prix HT</div>
                            <div class="text-lg font-bold text-gray-900" id="price-ht">0 FCFA</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-600">Taxe</div>
                            <div class="text-lg font-bold text-orange-600" id="tax-amount">0 FCFA</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-600">Prix TTC</div>
                            <div class="text-lg font-bold text-blue-600" id="price-ttc">0 FCFA</div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Créer le produit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('price');
    const taxRateInput = document.getElementById('tax_rate');
    const priceHT = document.getElementById('price-ht');
    const taxAmount = document.getElementById('tax-amount');
    const priceTTC = document.getElementById('price-ttc');

    function updatePricePreview() {
        const price = parseFloat(priceInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 0;
        const tax = price * taxRate / 100;
        const totalPrice = price + tax;

        priceHT.textContent = price.toLocaleString('fr-FR') + ' FCFA';
        taxAmount.textContent = tax.toLocaleString('fr-FR') + ' FCFA';
        priceTTC.textContent = totalPrice.toLocaleString('fr-FR') + ' FCFA';
    }

    priceInput.addEventListener('input', updatePricePreview);
    taxRateInput.addEventListener('input', updatePricePreview);

    // Initial update
    updatePricePreview();
});
</script>
@endsection
