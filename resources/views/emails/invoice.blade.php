<x-mail::message>
# Bonjour {{ $invoice->client->name }},

Veuillez trouver ci-joint votre facture d'un montant de **{{ number_format($invoice->total, 0, ',', ' ') }} FCFA**.

**Date d'échéance**: {{ $invoice->due_date->format('d/m/Y') }}



Pour toute question concernant cette facture, n'hésitez pas à nous contacter.

Cordialement,
**Bridge Technologies Solutions**
contact@bridgetech-solutions.com
+237 679 28 81 66 / +237 692 14 38 11
</x-mail::message>
