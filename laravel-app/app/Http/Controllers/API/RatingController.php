<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\RatingNotificationMailable;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan;

/**
 * @OA\Tag(
 *     name="Ratings",
 *     description="Gestion des évaluations des plans"
 * )
 */
class RatingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ratings",
     *     operationId="getUserRatings",
     *     tags={"Ratings"},
     *     summary="Obtenir la liste des évaluations de l'utilisateur connecté",
     *     @OA\Response(response=200, description="Liste des évaluations récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function index()
    {
        $ratings = Rating::with(['user', 'plan'])
                         ->where('user_id', Auth::id())
                         ->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des évaluations récupérée avec succès.',
            'ratings' => $ratings
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/ratings",
     *     operationId="storeRating",
     *     tags={"Ratings"},
     *     summary="Créer ou mettre à jour une évaluation pour un plan",
     *     @OA\Parameter(
     *         name="plan_id",
     *         in="query",
     *         required=true,
     *         description="ID du plan a noter",
     *         @OA\Schema(type="integer", example="1")
     *     ),
     *      @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         required=true,
     *         description="Note du plan (1-5)",
     *         @OA\Schema(type="integer", example="3")
     *     ),
     *     @OA\Response(response=201, description="Évaluation enregistrée avec succès"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // Création ou mise à jour de l'évaluation
        $rating = Rating::updateOrCreate(
            [
                'plan_id' => $request->plan_id,
                'user_id' => Auth::user()->id,
            ],
            ['rating' => $request->rating]
        );

        // Envoi du mail de notification à l'utilisateur du plan
        Mail::to(User::find(Plan::find($rating->plan_id)->user_id)->email)
            ->send(new RatingNotificationMailable($rating));

        return response()->json([
            'success' => true,
            'message' => 'Note sauvegardée avec succès',
            'data' => $rating
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ratings/{id}",
     *     operationId="getRating",
     *     tags={"Ratings"},
     *     summary="Afficher les détails d'une évaluation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Évaluation récupérée avec succès"),
     *     @OA\Response(response=404, description="Évaluation non trouvée"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function show(string $id)
    {
        $rating = Rating::with(['user', 'plan'])->find($id);

        if (!$rating) {
            return response()->json([
                // 'success' => false,
                'message' => 'Note non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Note récupérée avec succès',
            'data' => $rating
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/ratings/{id}",
     *     operationId="updateRating",
     *     tags={"Ratings"},
     *     summary="Mettre à jour une évaluation existante",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plan_id", "rating"},
     *             @OA\Property(property="plan_id", type="integer", description="ID du plan évalué"),
     *             @OA\Property(property="rating", type="integer", description="Note du plan (1-5)")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Évaluation mise à jour avec succès"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=404, description="Évaluation non trouvée"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function update(Request $request, string $id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json([
                // 'success' => false,
                'message' => 'Note non trouvée'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // Mise à jour de l'évaluation
        $rating->update([
            'rating' => $request->rating
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note mise à jour avec succès',
            'data' => $rating
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/ratings/{id}",
     *     operationId="deleteRating",
     *     tags={"Ratings"},
     *     summary="Supprimer une évaluation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Évaluation supprimée avec succès"),
     *     @OA\Response(response=404, description="Évaluation non trouvée"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function destroy($id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json([
                // 'success' => false,
                'message' => 'Note non trouvée'
            ], 404);
        }

        $rating->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note supprimée avec succès'
        ]);
    }
}
