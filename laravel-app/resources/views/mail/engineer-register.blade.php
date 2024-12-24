<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription d'un nouvel ingénieur</title>
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
        <h1>Confirmation d'inscription</h1>
        <p>Bonjour M. </strong> <span class="highlight">{{ $data['name'] }}</span>.</p>
        <p>Vous vous êtes inscrit avec succès sur la plateforme CIV-TOOLKIT. Voici vos identifiants de connexion :</p>

        <div class="resource-info">
            <p><strong>Email : </strong> <span class="highlight">{{ $data['email'] }}</span></p>
            <p><strong>Mot de passe : </strong> <span class="highlight">{{ $data['password'] }}</span></p>
            <p><strong>Rôle : </strong> @if ($data['role'] == 'customer')
                                            Client                
                                        @else @if ($data['role'] == 'engineer')
                                            Ingenieur
                                        @else
                                            Administrateur
                                        @endif @endif
            </p>

        </div>

        <p>Bien à vous.</p>

        {{-- <a href="#" class="button">Voir les détails</a> --}}

        <footer>
            <p>L'équipe de gestion de CIV-TOOLKIT</p>
        </footer>
    </div>
</body>
</html>
