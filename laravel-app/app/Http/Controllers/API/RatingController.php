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
        return response()->json(Rating::with('plan')->where('user_id', Auth::id())->get());
    }

    /**
     * Stocker une nouvelle ressource dans le stockage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required|exists:users,id',
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
        Mail::to(User::find(Plan::find($rating->plan_id)->user_id)->email)->send(new RatingNotificationMailable($rating));

        return response()->json(['message' => 'Note sauvegardee avec succes', 'rating' => $rating],201);
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
        return response()->json($rating);
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
        return response()->json([
            'message' => "Note mise a jour avec succes",
            'rating' => $rating
        ]);
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
