<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Command;
use App\Models\CommandProcessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommandProcessMailable;

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
            'name' => 'required|string|max:255',
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
            'name' => $request->name,
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
            'name' => 'required|string:max:255',
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

    public function processCommand(Request $request, string $command_id) {
        $command = Command::find($command_id);

        if (!$command) {
            return response()->json([
                // 'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'engineer_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $command->engineer_id = $request->engineer_id;
        $command->price = $request->price;
        $command->status = 'treated';
        
        do {
            $token = Str::random(60);
            $tokenExist = CommandProcessToken::whereNotNull('token')->first(
                function ($cmd) use ($token) {
                    return Hash::check($token, $cmd->token);
                }
            )->exists();
        } while ($tokenExist);
        
        $commandProcessTokenData = [
            'command_id' => $command->id,
            'token' => $token,
            'comment' => $request->comment,
            'created_at' => now(),
        ];

        Mail::to($command->user()->email)->send(new CommandProcessMailable($commandProcessTokenData));

        $commandProcessTokenData['token'] = Hash::make($commandProcessTokenData['token']);
        CommandProcessToken::create($commandProcessTokenData);
        $command->save();

        return response()->json([
            'success' => true,
            'message' => "Commande traitée avec succès. Un email a été envoyé à l'utilisateur pour validation.",
            'data' => $command,
        ]);

    }

    public function validateCommand($token, $answer) {
        $commandProcess = CommandProcessToken::whereNotNull('token')->first(
                            function ($cmd) use ($token) {
                                return Hash::check($token, $cmd->token);
                            }
                        );

        if (!$commandProcess->exists()) {
            return response()->json([
                // 'success' => false,
                'message' => 'Aucune commande en attente de traitement avec ce token.'
            ], 404);
        }

        $command = Command::find($commandProcess->command_id);

        if ($command->status != 'treated') {
            if ($command->status == 'pending') {
                $message = "Cette commande n'a pas encore été traitée par l'admin.";
            } else {
                $message = "Cette commande a déjà été " . $command->status == 'accepted' ? 'acceptée' : 'rejetée' . ".";
            }
            return response()->json([
                // 'success' => false,
                'message' => $message,
            ], 400);
        }

        if ($answer == 'accept') {
            $command->status = 'accepted';
        }
        else if ($answer == 'reject') {
            $command->status = 'rejected';
        }
        else {
            return response()->json([
                // 'success' => false,
                'message' => "Veuillez fournir une réponse valide (accept ou reject).",
            ], 400);
        }

        $command->save();
        $commandProcess->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Commande '. $command->status == 'accepted' ? 'acceptée' : 'rejetée' .' avec succès.',
            'data' => $command,
        ]);
    }

}
