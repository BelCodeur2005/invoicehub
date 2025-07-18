@extends('layouts.app')
@section('title', 'Produit - ' . $product->name)
@section('page-title', 'Détails du Produit')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="mt-2 text-sm text-gray-600">Informations détaillées du produit</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('products.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informations générales</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom du produit</label>
                            <p class="text-gray-900 font-medium">{{ $product->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $product->type === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                <i class="fas {{ $product->type === 'product' ? 'fa-box' : 'fa-cogs' }} mr-1"></i>
                                {{ $product->type === 'product' ? 'Produit' : 'Service' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prix unitaire</label>
                            <p class="text-gray-900 font-medium">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Taux de taxe</label>
                            <p class="text-gray-900 font-medium">{{ $product->tax_rate }}%</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <div class="w-2 h-2 rounded-full mr-2 {{ $product->is_active ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                {{ $product->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>

                    @if($product->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $product->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Price Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Détails de prix</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600">Prix HT</div>
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }}</div>
                            <div class="text-sm text-gray-500">FCFA</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600">Taxe ({{ $product->tax_rate }}%)</div>
                            <div class="text-2xl font-bold text-orange-600">{{ number_format($product->price * $product->tax_rate / 100, 0, ',', ' ') }}</div>
                            <div class="text-sm text-gray-500">FCFA</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-sm text-blue-600">Prix TTC</div>
                            <div class="text-2xl font-bold text-blue-900">{{ number_format($product->price * (1 + $product->tax_rate / 100), 0, ',', ' ') }}</div>
                            <div class="text-sm text-blue-600">FCFA</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Statistiques</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Créé le</span>
                            <span class="text-sm font-medium text-gray-900">{{ $product->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Modifié le</span>
                            <span class="text-sm font-medium text-gray-900">{{ $product->updated_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Utilisations</span>
                            <span class="text-sm font-medium text-gray-900">{{ $product->invoice_items_count + $product->proforma_items_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('products.edit', $product) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Modifier le produit
                        </a>

                        @if($product->is_active)
                        <form action="{{ route('products.update', $product) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="description" value="{{ $product->description }}">
                            <input type="hidden" name="price" value="{{ $product->price }}">
                            <input type="hidden" name="type" value="{{ $product->type }}">
                            <input type="hidden" name="tax_rate" value="{{ $product->tax_rate }}">
                            <input type="hidden" name="is_active" value="0">
                            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-pause mr-2"></i>
                                Désactiver
                            </button>
                        </form>
                        @else
                        <form action="{{ route('products.update', $product) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="description" value="{{ $product->description }}">
                            <input type="hidden" name="price" value="{{ $product->price }}">
                            <input type="hidden" name="type" value="{{ $product->type }}">
                            <input type="hidden" name="tax_rate" value="{{ $product->tax_rate }}">
                            <input type="hidden" name="is_active" value="1">
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-play mr-2"></i>
                                Activer
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
