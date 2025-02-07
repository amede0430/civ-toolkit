<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CommandController extends Controller
{
    public function index()
    {
        $commands = Command::with(['user'])
                         ->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des commandes récupérée avec succès.',
            'commands' => $commands
        ], 200);
    }

    
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'area' => 'required|numeric',
            'levels_number' => 'required|integer|min:1',
            'materials' => 'required|string',
            'zone' => 'required|in:agglomeration,village',
            'construction_type' => 'required|in:individual,public',
            'command_type' => 'required|in:entire,element',
            'file' => 'nullable|file|mimes:dwg,pdf|max:2048',
            'deadline' => 'required|date',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors()
            ], 422);
        }
    
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('commands', 'public');
        }

        $command = Command::create([
            'user_id' => Auth::id(),
            'area' => $request->area,
            'levels_number' => $request->levels_number,
            'materials' => $request->materials,
            'zone' => $request->zone,
            'construction_type' => $request->construction_type,
            'command_type' => $request->command_type,
            'file_path' => $filePath,
            'deadline' => $request->deadline,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        $command->file_path = asset('storage/' . $command->file_path);
    
        return response()->json([
            'success' => true,
            'message' => 'Commande enregistrée avec succès',
            'command' => $command
        ], 201);
    }

   
    public function show(string $id)
    {
        $command = Command::find($id);

        if (!$command) {
            return response()->json([
                // 'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        // Ajouter les URLs des fichiers à la réponse
        $command->file_path = asset('storage/' . $command->cover_path);
        return response()->json([
            'success' => true,
            'message' => 'Commande récupérée avec succès',
            'data' => $command
        ], 200);
    }

    
    public function update(Request $request, string $id)
    {
        $command = Command::find($id);

        if (!$command) {
            return response()->json([
                // 'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'area' => 'required|numeric',
            'levels_number' => 'required|integer|min:1',
            'materials' => 'required|string',
            'zone' => 'required|in:agglomeration,village',
            'construction_type' => 'required|in:individual,public',
            'command_type' => 'required|in:entire,element',
            'file' => 'nullable|file|mimes:dwg,pdf|max:2048',
            'deadline' => 'required|date',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('file')) {
            $command->file_path = $request->file('file')->store('commands', 'public');
        }
        
        $command->save();

        // Ajouter les URLs des fichiers à la réponse
        $command->file_path = asset('storage/' . $command->filePath);
        
        return response()->json([
            'success' => true,
            'message' => 'Commande mise à jour avec succès',
            'data' => $command,
        ]);
    }

    
    public function destroy($id)
    {
        $command = Command::find($id);

        if (!$command) {
            return response()->json([
                // 'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        // Supprimer les fichiers associés
        Storage::disk('public')->delete($command->file_path);

        // Supprimer le plan de la base de données
        $command->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commande supprimée avec succès.'
        ]);
    }

}
