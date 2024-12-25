<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Catégories",
 *     description="Gestion des catégories"
 * )
 */
class CategorieController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     operationId="getCategories",
     *     tags={"Catégories"},
     *     summary="Récupérer la liste des catégories",
     *     @OA\Response(response=200, description="Liste des catégories récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function index()
    {
        $categories = Categorie::all();
        return response()->json([
            'message' => 'Liste des catégories récupérée avec succès.',
            'data' => $categories
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     operationId="createCategory",
     *     tags={"Catégories"},
     *     summary="Créer une nouvelle catégorie",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description"},
     *             @OA\Property(property="name", type="string", example="Électronique"),
     *             @OA\Property(property="description", type="string", example="Catégorie pour les produits électroniques")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Catégorie créée avec succès"),
     *     @OA\Response(response=400, description="Données invalides")
     * )
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Création de la catégorie
        $categorie = Categorie::create($validated);

        return response()->json([
            'message' => 'Catégorie créée avec succès.',
            'data' => $categorie
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{categorie_id}",
     *     operationId="getCategory",
     *     tags={"Catégories"},
     *     summary="Récupérer les détails d'une catégorie",
     *     @OA\Parameter(
     *         name="categorie_id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Catégorie récupérée avec succès"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
    public function show(Categorie $categorie)
    {
        return response()->json([
            'message' => 'Catégorie récupérée avec succès.',
            'data' => $categorie
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{categorie_id}",
     *     operationId="updateCategory",
     *     tags={"Catégories"},
     *     summary="Mettre à jour une catégorie",
     *     @OA\Parameter(
     *         name="categorie_id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description"},
     *             @OA\Property(property="name", type="string", example="Informatique"),
     *             @OA\Property(property="description", type="string", example="Catégorie pour les produits informatiques")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Catégorie mise à jour avec succès"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
    public function update(Request $request, $categorie_id)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Mise à jour de la catégorie
        $categorie = Categorie::findOrFail($categorie_id);
        $categorie->update($validated);

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès.',
            'data' => $categorie
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{categorie_id}",
     *     operationId="deleteCategory",
     *     tags={"Catégories"},
     *     summary="Supprimer une catégorie",
     *     @OA\Parameter(
     *         name="categorie_id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Catégorie supprimée avec succès"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
    public function destroy($categorie_id)
    {
        $categorie = Categorie::findOrFail($categorie_id);
        $categorie->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès.'
        ], 200);
    }
}
