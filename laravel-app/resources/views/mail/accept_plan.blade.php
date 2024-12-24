<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décision concernant votre plan</title>
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
        blockquote {
            font-style: italic;
            color: #6c757d;
            margin: 15px 0;
            padding-left: 15px;
            border-left: 4px solid #007bff;
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
        <h1>Décision concernant votre plan</h1>
        <p>Bonjour <span class="highlight">{{ $mailData['user_name'] }}</span>,</p>
        <p>Votre plan intitulé <strong>"{{ $mailData['plan_title'] }}"</strong> a été 
            <span class="highlight">{{ $mailData['decision'] }}</span>.</p>

        <p>Voici le commentaire de l'administrateur :</p>
        <blockquote>
            {{ $mailData['comment'] }}
        </blockquote>

        @if($mailData['decision'] === 'Accepté')
            <p>Nous sommes ravis de vous informer que votre plan a été validé. Merci pour votre contribution !</p>
        @else
            <p>Malheureusement, votre plan n'a pas été retenu. Nous vous invitons à prendre en compte les remarques ci-dessus pour une éventuelle soumission future.</p>
        @endif

        {{-- <a href="" class="button">Voir les détails</a> --}}

        <footer>
            <p>L'équipe de gestion de CIV-TOOLKIT.</p>
        </footer>
    </div>
</body>
</html>
