<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponse à votre demande de plan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            margin: 10px 0;
        }
        .highlight {
            color: #007bff;
            font-weight: bold;
        }
        .resource-info {
            margin: 20px 0;
            padding: 15px;
            background: #fdfdfd;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .resource-info p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .button:hover {
            background-color: #0056b3;
        }
        footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Réponse à votre demande de plan</h1>
        <p>Bonjour M./Mme <span class="highlight">{{ $mailData->command()->user()->name }}</span>,</p>
        <p>Une réponse a été apportée à votre demande de plan. Voici les détails :</p>

        <div class="resource-info">
            <p><strong>Plan demandé : </strong> <span class="highlight">{{ $mailData->command()->name }}</span></p>
            <p><strong>Proposition de prix : </strong> <span class="highlight">{{ number_format($mailData->command()->price, 0, ",", " " ) }} FCFA</span></p>
            <p><strong>Message de l'administrateur : </strong> {{ $mailData->comment }}</p>
        </div>

        <p>Veuillez choisir une option :</p>
        <span style="text-align: center;">
            <a href="{{ url('/command/'. $mailData['token'] .'/validate/accept') }}" class="button">Accepter</a>
            <a href="{{ url('/command/'. $mailData['token'] .'/validate/reject') }}" class="button" style="background-color: #dc3545;">Rejeter</a>
        </span>

        <footer>
            <p>L'équipe de gestion de CIV-TOOLKIT.</p>
        </footer>
    </div>
</body>
</html>
