<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SoumissionPlanMailable;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Plans",
 *     description="Gestion des plans"
 * )
 */
class PlanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/engineer/plans",
     *     operationId="getPlans",
     *     tags={"Plans"},
     *     summary="Récupérer la liste des plans d'un ingénieur connecté",
     *     @OA\Response(response=200, description="Liste des plans récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function index()
    {
        $plans = Plan::all()->where('user_id', Auth::id());

        // Inclure les URLs des fichiers
        foreach ($plans as $plan) {
            $plan->cover_path = asset('storage/' . $plan->cover_path);
            $plan->pdf_path = asset('storage/' . $plan->pdf_path);
            $plan->zip_path = asset('storage/' . $plan->zip_path);
        }

        return response()->json([
            'success' => true,
            'message' => 'Liste des plans récupérée avec sucees.',
            'data' => $plans
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/engineer/plans",
     *     operationId="storePlan",
     *     tags={"Plans"},
     *     summary="Créer un nouveau plan",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "description", "price", "free", "cover_path", "pdf_path", "zip_path"},
     *                 @OA\Property(property="title", type="string", description="Titre du plan"),
     *                 @OA\Property(property="description", type="string", description="Description du plan"),
     *                 @OA\Property(property="price", type="number", format="float", description="Prix du plan"),
     *                 @OA\Property(property="free", type="boolean", description="Plan gratuit ou payant"),
     *                 @OA\Property(property="cover_path", type="string", format="binary", description="Image de couverture du plan"),
     *                 @OA\Property(property="pdf_path", type="string", format="binary", description="PDF du plan"),
     *                 @OA\Property(property="zip_path", type="string", format="binary", description="Fichier ZIP du plan")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Plan créé avec succès"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'free' => 'required|boolean',
            // Validation des fichiers
            'cover_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_path' => 'required|mimes:pdf|max:10240',
            'zip_path' => 'required|mimes:zip,rar|max:10240',
        ]);

        // Enregistrement des fichiers dans les répertoires appropriés
        $coverPath = $request->file('cover_path')->store('covers', 'public');
        $pdfPath = $request->file('pdf_path')->store('pdfs', 'public');
        $zipPath = $request->file('zip_path')->store('zips', 'public');

        // Création du plan avec les chemins des fichiers
        $plan = [
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'free' => $validated['free'],
            'cover_path' => $coverPath,
            'pdf_path' => $pdfPath,
            'zip_path' => $zipPath,
        ];

        Mail::to(User::where('role', 'admin')->first()->email)->send(new SoumissionPlanMailable($plan));

        // Création du plan
        $plan = Plan::create($plan);

        // Ajouter les URLs des fichiers à la réponse
        $plan->cover_path = asset('storage/' . $plan->cover_path);
        $plan->pdf_path = asset('storage/' . $plan->pdf_path);
        $plan->zip_path = asset('storage/' . $plan->zip_path);

        return response()->json([
            'success' => true,
            'message' => 'Plan créé avec succès.',
            'data' => $plan
    ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/engineer/plans/{plan_id}",
     *     operationId="getPlan",
     *     tags={"Plans"},
     *     summary="Récupérer les détails d'un plan",
     *     @OA\Parameter(
     *         name="plan_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Détails du plan récupérés avec succès"),
     *     @OA\Response(response=404, description="Plan non trouvé"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function show($plan_id)
    {
        $plan = Plan::find($plan_id);

        if (!$plan) {
            return response()->json([
                // 'success' => false,
                'message' => 'Plan non trouvé'
            ], 404);
        }

        // Ajouter les URLs des fichiers à la réponse
        $plan->cover_path = asset('storage/' . $plan->cover_path);
        $plan->pdf_path = asset('storage/' . $plan->pdf_path);
        $plan->zip_path = asset('storage/' . $plan->zip_path);

        return response()->json([
            'success' => true,
            'message' => 'Détails du plan récupérés avec succès.',
            'data' => $plan
    ]);
    }

    /**
     * @OA\Put(
     *     path="/api/engineer/plans/{plan_id}",
     *     operationId="updatePlan",
     *     tags={"Plans"},
     *     summary="Mettre à jour un plan",
     *     @OA\Parameter(
     *         name="plan_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "description", "price", "free"},
     *                 @OA\Property(property="title", type="string", description="Titre du plan"),
     *                 @OA\Property(property="description", type="string", description="Description du plan"),
     *                 @OA\Property(property="price", type="number", format="float", description="Prix du plan"),
     *                 @OA\Property(property="free", type="boolean", description="Plan gratuit ou payant"),
     *                 @OA\Property(property="cover_path", type="string", format="binary", description="Image de couverture du plan"),
     *                 @OA\Property(property="pdf_path", type="string", format="binary", description="PDF du plan"),
     *                 @OA\Property(property="zip_path", type="string", format="binary", description="Fichier ZIP du plan")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Plan mis à jour avec succès"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=404, description="Plan non trouvé"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function update(Request $request, $plan_id)
    {
        // Recherche du plan existant
        $plan = Plan::find($plan_id);

        if (!$plan) {
            return response()->json([
                // 'success' => false,
                'message' => 'Plan non trouvé'
            ], 404);
        }

        // Validation des données
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'free' => 'required|boolean',
            'cover_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_path' => 'nullable|mimes:pdf|max:10240',
            'zip_path' => 'nullable|mimes:zip,rar|max:10240',
        ]);

        // Mise à jour des propriétés
        $plan->title = $validated['title'];
        $plan->description = $validated['description'];
        $plan->price = $validated['price'];
        $plan->free = $validated['free'];
        $plan->category_id = $validated['category_id'] ?? $plan->category_id;

        // Si des fichiers sont envoyés, les enregistrer et mettre à jour les chemins
        if ($request->hasFile('cover_path')) {
            $coverPath = $request->file('cover_path')->store('covers', 'public');
            $plan->cover_path = $coverPath;
        }

        if ($request->hasFile('pdf_path')) {
            $pdfPath = $request->file('pdf_path')->store('pdfs', 'public');
            $plan->pdf_path = $pdfPath;
        }

        if ($request->hasFile('zip_path')) {
            $zipPath = $request->file('zip_path')->store('zips', 'public');
            $plan->zip_path = $zipPath;
        }

        // Sauvegarde du plan
        $plan->save();

        // Ajouter les URLs des fichiers à la réponse
        $plan->cover_path = asset('storage/' . $plan->cover_path);
        $plan->pdf_path = asset('storage/' . $plan->pdf_path);
        $plan->zip_path = asset('storage/' . $plan->zip_path);

        return response()->json([
            'success' => true,
            'message' => 'Plan mis à jour avec succès.',
            'data' => $plan
        ], 200);
    }


    /**
     * @OA\Delete(
     *     path="/api/engineer/plans/{plan_id}",
     *     operationId="deletePlan",
     *     tags={"Plans"},
     *     summary="Supprimer un plan",
     *     @OA\Parameter(
     *         name="plan_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Plan supprimé avec succès"),
     *     @OA\Response(response=404, description="Plan non trouvé"),
     *     @OA\Response(response=500, description="Erreur interne")
     * )
     */
    public function destroy($plan_id)
    {
        $plan = Plan::find($plan_id);

        if (!$plan) {
            return response()->json([
                // 'success' => false,
                'message' => 'Plan non trouvé'
            ], 404);
        }
        // Supprimer les fichiers associés
        Storage::disk('public')->delete($plan->cover_path);
        Storage::disk('public')->delete($plan->pdf_path);
        Storage::disk('public')->delete($plan->zip_path);

        // Supprimer le plan de la base de données
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan supprimé avec succès.'
        ]);
    }
}
