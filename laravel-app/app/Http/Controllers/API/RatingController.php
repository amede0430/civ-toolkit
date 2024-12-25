<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    /**
     * Afficher la liste des ressources.
     */
    public function index()
    {
        $ratings = Rating::with(['user', 'plan'])
                         ->where('user_id', Auth::id())
                         ->get();

        return response()->json($ratings, 200);
    }

    /**
     * Stocker une nouvelle ressource dans le stockage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rating = Rating::updateOrCreate(
            [
                'plan_id' => $request->plan_id,
                'user_id' => Auth::user()->id,
            ],
            ['rating' => $request->rating]
        );

        return response()->json(['message' => 'Note sauvegardée avec succès', 'rating' => $rating], 201);
    }

    /**
     * Afficher la ressource spécifiée.
     */
    public function show(string $id)
    {
        $rating = Rating::with(['user', 'plan'])->find($id);

        if (!$rating) {
            return response()->json(['message' => 'Note non trouvée'], 404);
        }

        return response()->json($rating, 200);
    }

    /**
     * Mettre à jour la ressource spécifiée.
     */
    public function update(Request $request, string $id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json(['message' => 'Note non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rating->update([
            'rating' => $request->rating
        ]);

        return response()->json(['message' => 'Note mise à jour avec succès', 'rating' => $rating]);
    }

    /**
     * Supprimer la ressource spécifiée.
     */
    public function destroy($id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json(['message' => 'Note non trouvée'], 404);
        }

        $rating->delete();

        return response()->json(['message' => 'Note supprimée avec succès']);
    }
}
