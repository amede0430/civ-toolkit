<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Affiche la liste des catégories.
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
     * Enregistre une nouvelle catégorie.
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
     * Affiche les détails d'une catégorie.
     */
    public function show(Categorie $categorie)
    {
        return response()->json([
            'message' => 'Catégorie récupérée avec succès.',
            'data' => $categorie
        ], 200);
    }

    /**
     * Met à jour une catégorie existante.
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
     * Supprime une catégorie.
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
