<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\AcceptPlanMailable;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs.
     */
    public function index()
    {
        $users = User::with('plans')->where('role', 'engineer')->get();
        return response()->json(['data' => $users], 200);
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'role' => 'engineer',
            'last_login' => now(),
        ]);

        return response()->json(['message' => 'Ingénieur créé avec succès', 'data' => $user], 201);
    }

    /**
     * Affiche les détails d'un utilisateur spécifique.
     */
    public function show($user_id)
    {
        $user = User::with('plans')->findOrFail($user_id);
        return response()->json(['data' => $user], 200);
    }

    /**
     * Met à jour un utilisateur existant.
     */
    public function update(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $rules = [
            'name' => 'sometimes|string|max:255'
        ];

        // Ajouter la validation d'email uniquement si l'email est différent
        if ($request->has('email') && $request->email !== $user->email) {
            $rules['email'] = 'sometimes|string|email|max:255|unique:users,email,' . $user_id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return response()->json([
            'message' => 'Ingénieur mis à jour avec succès',
            'data' => $user
        ], 200);
    }

    /**
     * Supprime un utilisateur existant.
     */
    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->delete();
        return response()->json(['message' => 'Ingénieur supprimé avec succès'], 200);
    }

    //accepter ou rejeter un plan créer par un ingénieur

    public function accept_plan(Request $request, $plan_id)
    {
        // Validation des données entrantes
        $validated = $request->validate([
            'comment' => 'required|string',
            'accept' => 'required|boolean',
        ]);

        
        // Récupérer le plan et vérifier son existence
        $plan = Plan::findOrFail($plan_id);

        // Mettre à jour le statut du plan (accepté ou non)
        $plan->accept = $validated['accept'];

        // Sauvegarder les changements (si le plan est accepté)
        if ($validated['accept']) {
            $plan->save();
        }

        // Récupérer l'utilisateur lié au plan
        $user = $plan->user;

        // Préparer les informations pour la notification par mail
        $mailData = [
            'user_name' => $user->name,
            'plan_title' => $plan->title,
            'decision' => $validated['accept'] ? 'Accepté' : 'Rejeté',
            'comment' => $validated['comment'],
        ];

        // Envoyer la notification par e-mail
        Mail::to($user->email)->send(new AcceptPlanMailable($mailData));

        // Supprimer le plan si la décision est un rejet
        if (!$validated['accept']) {
            $plan->delete();
        }

        // Retourner une réponse au client
        return response()->json([
            'message' => 'Décision prise avec succès.',
            'data' => $mailData,
        ], 200);
    }
}
