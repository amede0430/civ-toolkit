<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\AcceptPlanMailable;
use App\Mail\EngineerRegisterMailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Plan;
use App\Models\User;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Gestion des utilisateurs"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     operationId="getUsers",
     *     tags={"Users"},
     *     summary="Obtenir la liste des utilisateurs ingénieurs",
     *     @OA\Response(response=200, description="Liste des utilisateurs récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function index()
    {
        $users = User::with('plans')->where('role', 'engineer')->get();
        return response()->json(['data' => $users], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     operationId="createUser",
     *     tags={"Users"},
     *     summary="Enregistrer un nouvel utilisateur ingénieur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", description="Nom de l'utilisateur"),
     *             @OA\Property(property="email", type="string", description="Email de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Utilisateur créé avec succès"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
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

        $password = Str::random(10);

        $user = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'role' => 'engineer',
            'last_login' => now(),
        ];

        // Envoi de l'email d'enregistrement de l'ingénieur
        Mail::to($user['email'])->send(new EngineerRegisterMailable($user));

        // Hashage du mot de passe avant de l'enregistrer dans la base
        $user['password'] = Hash::make($password);
        $user = User::create($user);

        return response()->json(['message' => 'Ingénieur créé avec succès', 'data' => $user], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user_id}",
     *     operationId="getUser",
     *     tags={"Users"},
     *     summary="Afficher les détails d'un utilisateur spécifique",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Utilisateur récupéré avec succès"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function show($user_id)
    {
        $user = User::with('plans')->findOrFail($user_id);
        return response()->json(['data' => $user], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{user_id}",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     summary="Mettre à jour un utilisateur existant",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Nom de l'utilisateur"),
     *             @OA\Property(property="email", type="string", description="Email de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Utilisateur mis à jour avec succès"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function update(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $rules = [
            'name' => 'sometimes|string|max:255'
        ];

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
     * @OA\Delete(
     *     path="/api/users/{user_id}",
     *     operationId="deleteUser",
     *     tags={"Users"},
     *     summary="Supprimer un utilisateur existant",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Utilisateur supprimé avec succès"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->delete();
        return response()->json(['message' => 'Ingénieur supprimé avec succès'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users/accept/plan/{plan_id}",
     *     operationId="acceptPlan",
     *     tags={"Plans"},
     *     summary="Accepter ou rejeter un plan créé par un ingénieur",
     *    
     *     @OA\Parameter(
     *         name="plan_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comment", "accept"},
     *             @OA\Property(property="comment", type="string", description="Commentaire sur la décision"),
     *             @OA\Property(property="accept", type="boolean", description="Accepter ou rejeter le plan")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Décision prise avec succès"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=404, description="Plan non trouvé")
     * )
     */
    public function accept_plan(Request $request, $user_id, $plan_id)
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

        return response()->json([
            'message' => 'Décision prise avec succès.',
            'data' => $mailData,
        ], 200);
    }
}
