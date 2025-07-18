<x-mail::message>
# Bonjour {{ $proforma->client->name }},

Veuillez trouver ci-joint votre proforma d'un montant de **{{ number_format($proforma->total, 0, ',', ' ') }} FCFA**.

**Date d'échéance**: {{ $proforma->valid_until->format('d/m/Y') }}



Pour toute question concernant cette proforma, n'hésitez pas à nous contacter.

Cordialement,
**Bridge Technologies Solutions**
contact@bridgetech-solutions.com
+237 679 28 81 66 / +237 692 14 38 11
</x-mail::message>
