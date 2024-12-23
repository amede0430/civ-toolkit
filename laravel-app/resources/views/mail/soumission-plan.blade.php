<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification de soumission de ressource</title>
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
        <h1>Nouvelle soumission de plan</h1>
        <p>Bonjour cher Administrateur,</p>
        <p>Un ingénieur a soumis un nouveau plan sur la plateforme. Voici les détails :</p>

        <div class="resource-info">
            <p><strong>Utilisateur : </strong> <span class="highlight">{{ $data['username'] }}</span></p>
            <p><strong>Catégorie : </strong> <span class="highlight">{{ $data['category'] }}</span></p>
            <p><strong>Titre : </strong> <span class="highlight">{{ $data['title'] }}</span></p>
            <p><strong>Description : </strong> {{ $data['description'] }}</p>
            <p><strong>Prix : </strong> {{ $data['price'] }}</p>
            <p><strong>Gratuit : </strong> {{ $data['free'] }}</p>
            {{-- <p><strong>Chemin de la couverture : </strong> <a href="{{ cover_path }}" target="_blank">Voir la couverture</a></p>
            <p><strong>Chemin du PDF : </strong> <a href="{{ pdf_path }}" target="_blank">Voir le PDF</a></p>
            <p><strong>Chemin du ZIP : </strong> <a href="{{ zip_path }}" target="_blank">Télécharger le ZIP</a></p> --}}
        </div>

        <p>Merci de bien vouloir examiner cette soumission et prendre les mesures nécessaires.</p>

        {{-- <a href="#" class="button">Voir les détails</a> --}}

        <footer>
            <p>L'équipe de gestion des plans de CIV-TOOLKIT</p>
        </footer>
    </div>
</body>
</html>
