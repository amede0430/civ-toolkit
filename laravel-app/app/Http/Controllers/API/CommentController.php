<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class CommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/comments",
     *     operationId="getComments",
     *     tags={"Commentaires"},
     *     summary="Récupérer la liste des commentaires",
     *     @OA\Response(response=200, description="Liste des commentaires récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function index()
    {
        return response()->json(Comment::with('plan')->where('user_id', Auth::id())->get());
    }

    /**
     * @OA\Post(
     *     path="/api/comments",
     *     operationId="createComment",
     *     tags={"Commentaires"},
     *     summary="Créer un nouveau commentaire",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plan_id", "comment"},
     *             @OA\Property(property="plan_id", type="integer", example=1),
     *             @OA\Property(property="comment", type="string", example="Ceci est un commentaire.")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Commentaire créé avec succès"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'comment' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment = Comment::create([
            'user_id' => Auth::user()->id,
            'plan_id' => $request->plan_id,
            'comment' => $request->comment
        ]);

        return response()->json(['message' => 'Commentaire ajouté avec succès', 'comment' => $comment], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/comments/{id}",
     *     operationId="getComment",
     *     tags={"Commentaires"},
     *     summary="Récupérer un commentaire spécifique",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du commentaire",
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\Response(response=200, description="Commentaire récupéré avec succès"),
     *     @OA\Response(response=404, description="Commentaire non trouvé")
     * )
     */
    public function show(string $id)
    {
        $comment = Comment::with('plan')->find($id);
        if (!$comment) {
            return response()->json(['message' => 'Commentaire non trouvé'], 404);
        }
        return response()->json($comment);
    }

    /**
     * @OA\Put(
     *     path="/api/comments/{id}",
     *     operationId="updateComment",
     *     tags={"Commentaires"},
     *     summary="Mettre à jour un commentaire",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du commentaire",
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plan_id", "comment"},
     *             @OA\Property(property="plan_id", type="integer", example=1),
     *             @OA\Property(property="comment", type="string", example="Mise à jour du commentaire.")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Commentaire mis à jour avec succès"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=404, description="Commentaire non trouvé")
     * )
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::find($id);        
        if (!$comment) {
            return response()->json(['message' => 'Commentaire non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'comment' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment->update([
            'user_id' => Auth::user()->id,
            'plan_id' => $request->plan_id,
            'comment' => $request->comment
        ]);

        return response()->json(['message' => 'Commentaire mis à jour avec succès', 'comment' => $comment], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     operationId="deleteComment",
     *     tags={"Commentaires"},
     *     summary="Supprimer un commentaire",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du commentaire",
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\Response(response=200, description="Commentaire supprimé avec succès"),
     *     @OA\Response(response=404, description="Commentaire non trouvé")
     * )
     */
    public function destroy(string $id)
    {
        $comment = Comment::find($id);        
        if (!$comment) {
            return response()->json(['message' => 'Commentaire non trouvé'], 404);
        }

        $comment->delete();
        
        return response()->json(['message' => 'Commentaire supprimé avec succès']);
    }
}
