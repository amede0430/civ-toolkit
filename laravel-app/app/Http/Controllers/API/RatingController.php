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
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Rating::with('plan')->where('user_id', Auth::id())->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
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

        return response()->json(['message' => 'Note sauvegardee avec succes', 'rating' => $rating]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rating = Rating::with(['user', 'plan'])->find($id);
        if (!$rating) {
            return response()->json(['message' => 'Note non trouvee'], 404);
        }
        return response()->json($rating);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $rating = Rating::find($id);
        if (!$rating) {
            return response()->json(['message' => 'Note non trouvee'], 404);
        }
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

        return response()->json([
            'message' => "Note mise a jour avec succes",
            'rating' => $rating
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rating = Rating::find($id);
        if (!$rating) {
            return response()->json(['message' => 'Note non trouvee'], 404);
        }

        $rating->delete();

        return response()->json(['message' => 'Note supprimee avec succes']);
    }
}
