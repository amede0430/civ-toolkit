<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande Assignée</title>
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
        <h1>Assignation à une nouvelle commande</h1>
        <p>Bonjour <span class="highlight">{{ $mailData->engineer->name }}</span>,</p>
        <p>Une nouvelle commande vous a été assignée. En voici quelques détails :</p>

        <div class="resource-info">
            <p><strong>Client : </strong> <span class="highlight">{{ $mailData->user->name }}</span></p>
            <p><strong>Plan demandé : </strong> <span class="highlight">{{ $mailData->name }}</span></p>
            <p><strong>Prix accepté : </strong> <span class="highlight">{{ number_format($mailData->price, 0, ",", " ") }} FCFA</span></p>
        </div>

        <p>Merci de traiter cette commande dans les meilleurs délais.</p>
        {{-- <a href="{{ url('/engineer/command/' . $mailData['command']->id) }}" class="button">Voir la commande</a> --}}

        <footer>
            <p>L'équipe de gestion de CIV-TOOLKIT.</p>
        </footer>
    </div>
</body>
</html>
