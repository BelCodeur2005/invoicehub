{{-- resources/views/emails/otp.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de récupération - Invoice Hub</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .otp-box {
            background-color: #f3f4f6;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            border: 2px solid #e5e7eb;
        }
        .otp-code {
            color: #f97316;
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 8px;
            margin: 0;
            font-family: 'Courier New', monospace;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .warning h3 {
            color: #92400e;
            margin-top: 0;
        }
        .warning ul {
            color: #92400e;
            margin: 10px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Invoice Hub</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Code de récupération de mot de passe</p>
        </div>

        <div class="content">
            <h2>Bonjour,</h2>

            <p>Vous avez demandé un code de récupération pour votre mot de passe sur Invoice Hub.</p>

            <div class="otp-box">
                <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 16px;">Votre code de vérification :</p>
                <h1 class="otp-code">{{ $otp }}</h1>
            </div>

            <div class="warning">
                <h3>⚠️ Important :</h3>
                <ul>
                    <li>Ce code est valide pendant <strong>15 minutes</strong> seulement</li>
                    <li>Ne partagez ce code avec personne</li>
                    <li>Si vous n'avez pas demandé cette réinitialisation, ignorez ce message</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}" class="button">Retour à Invoice Hub</a>
            </div>

            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>

            <p>Cordialement,<br>
            <strong>L'équipe {{ config('app.name') }}</strong></p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Invoice Hub. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
