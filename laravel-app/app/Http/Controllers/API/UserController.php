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
 *     name="Engineers",
 *     description="Gestion des ingenieurs"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/engineers",
     *     operationId="getEngineers",
     *     tags={"Engineers"},
     *     summary="Obtenir la liste des ingénieurs",
     *     @OA\Response(response=200, description="Liste des ingénieurs récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur interne"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $users = User::with('plans')->where('role', 'engineer')->get();
        return response()->json([
            'success' => true,
            'message' => 'Liste des ingénieurs recuperée avec sucees.',
            'data' => $users
        ], 200);
    }

    // @OA\RequestBody(
    //     required=true,
    //     @OA\JsonContent(
    //         required={"name", "email"},
    //         @OA\Property(property="name", type="string", description="Nom de l'ingénieur"),
    //         @OA\Property(property="email", type="string", description="Email de l'ingénieur")
    //     )
    // ),

    /**
     * @OA\Post(
     *     path="/api/admin/engineers",
     *     operationId="createEngineer",
     *     tags={"Engineers"},
     *     summary="Enregistrer un nouvel ingénieur",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Engineer's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Engineer's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=201, description="Ingénieur créé avec succès"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors()
            ], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'Ingénieur créé avec succès',
            'data' => $user
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/engineers/{engineer_id}",
     *     operationId="getEngineer",
     *     tags={"Engineers"},
     *     summary="Afficher les détails d'un ingénieur spécifique",
     *     @OA\Parameter(
     *         name="engineer_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ingénieur récupéré avec succès"),
     *     @OA\Response(response=404, description="Ingénieur non trouvé"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($user_id)
    {
        $user = User::with('plans')->where('role', 'engineer')->find($user_id);
        if (!$user) {
            return response()->json([
                // 'success' => false,
                'message' => 'Ingénieur non trouvé'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Ingénieur récupéré avec succès',
            'data' => $user
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/engineers/{engineer_id}",
     *     operationId="updateEngineer",
     *     tags={"Engineers"},
     *     summary="Mettre à jour un ingénieur existant",
     *     @OA\Parameter(
     *         name="engineer_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Nom de l'ingénieur"),
     *             @OA\Property(property="email", type="string", description="Email de l'ingénieur")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ingénieur mis à jour avec succès"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=404, description="Ingénieur non trouvé"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                // 'success' => false,
                'message' => 'Ingénieur non trouvé'
            ], 404);
        }

        $rules = [
            'name' => 'sometimes|string|max:255'
        ];

        if ($request->has('email') && $request->email !== $user->email) {
            $rules['email'] = 'sometimes|string|email|max:255|unique:users,email,' . $user_id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Ingénieur mis à jour avec succès',
            'data' => $user
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/engineers/{engineer_id}",
     *     operationId="deleteEngineer",
     *     tags={"Engineers"},
     *     summary="Supprimer un ingénieur existant",
     *     @OA\Parameter(
     *         name="engineer_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ingénieur supprimé avec succès"),
     *     @OA\Response(response=404, description="Ingénieur non trouvé"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                // 'success' => false,
                'message' => 'Ingénieur non trouvé'
            ], 404);
        }

        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Ingénieur supprimé avec succès'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/accept/plan/{plan_id}",
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
     *     @OA\Response(response=404, description="Plan non trouvé"),
     *     security={{"bearerAuth":{}}}
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
        $plan = Plan::find($plan_id);

        if (!$plan) {
            return response()->json([
                // 'success' => false,
                'message' => 'Plan non trouvé.',
            ], 404);
        }

        // Mettre à jour le statut du plan (accepté ou non)
        $plan->accept = $validated['accept'];

        // Sauvegarder les changements (si le plan est accepté)
        if ($validated['accept']) {
            $plan->save();
        }

        // Récupérer l'ingénieur lié au plan
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
