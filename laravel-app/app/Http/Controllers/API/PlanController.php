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

class PlanController extends Controller
{
    
    public function index()
    {
        $plans = Plan::all()->where('user_id', Auth::id());

        // Inclure les URLs des fichiers
        foreach ($plans as $plan) {
            $plan->cover_path = asset('storage/' . $plan->cover_path);
            $plan->pdf_path = asset('storage/' . $plan->pdf_path);
            $plan->zip_path = asset('storage/' . $plan->zip_path);
        }

        return response()->json($plans);
    }

   
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

        return response()->json($plan, 201);
    }

    
    public function show($plan_id)
    {
        $plan = Plan::findOrFail($plan_id);
        // Ajouter les URLs des fichiers à la réponse
        $plan->cover_path = asset('storage/' . $plan->cover_path);
        $plan->pdf_path = asset('storage/' . $plan->pdf_path);
        $plan->zip_path = asset('storage/' . $plan->zip_path);

        return response()->json($plan);
    }

   
    public function update(Request $request, $plan_id)
    {
        $plan = Plan::findOrFail($plan_id);

        // Validation des données
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'free' => 'nullable|boolean',
            'cover_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_path' => 'nullable|mimes:pdf|max:10240',
            'zip_path' => 'nullable|mimes:zip,rar|max:10240',
        ]);

        // Mettre à jour les champs du plan
        if ($request->has('category_id')) {
            $plan->category_id = $request->category_id;
        }
        if ($request->has('title')) {
            $plan->title = $request->title;
        }
        if ($request->has('description')) {
            $plan->description = $request->description;
        }
        if ($request->has('price')) {
            $plan->price = $request->price;
        }
        if ($request->has('free')) {
            $plan->free = $request->free;
        }

        // Gérer les fichiers (si nouveaux fichiers sont fournis)
        if ($request->hasFile('cover_path')) {
            if ($plan->cover_path) {
                Storage::disk('public')->delete($plan->cover_path);
            }
            $plan->cover_path = $request->file('cover_path')->store('covers', 'public');
        }

        if ($request->hasFile('pdf_path')) {
            if ($plan->pdf_path) {
                Storage::disk('public')->delete($plan->pdf_path);
            }
            $plan->pdf_path = $request->file('pdf_path')->store('pdfs', 'public');
        }

        if ($request->hasFile('zip_path')) {
            if ($plan->zip_path) {
                Storage::disk('public')->delete($plan->zip_path);
            }
            $plan->zip_path = $request->file('zip_path')->store('zips', 'public');
        }

        // Sauvegarder les modifications
        $plan->save();

        // Ajouter les URLs des fichiers à la réponse
        $plan->cover_path = $plan->cover_path ? asset('storage/' . $plan->cover_path) : null;
        $plan->pdf_path = $plan->pdf_path ? asset('storage/' . $plan->pdf_path) : null;
        $plan->zip_path = $plan->zip_path ? asset('storage/' . $plan->zip_path) : null;

        return response()->json($plan);
    }

   
    public function destroy($plan_id)
    {
        $plan = Plan::findOrFail($plan_id);
        // Supprimer les fichiers associés
        Storage::disk('public')->delete($plan->cover_path);
        Storage::disk('public')->delete($plan->pdf_path);
        Storage::disk('public')->delete($plan->zip_path);

        // Supprimer le plan de la base de données
        $plan->delete();

        return response()->json(['message' => 'Plan supprimé avec succès.']);
    }
}
