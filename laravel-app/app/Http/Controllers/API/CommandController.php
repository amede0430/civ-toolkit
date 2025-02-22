<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Command;
use App\Models\CommandProcessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommandProcessMailable;
use App\Mail\CommandAssignationMailable;
use App\Models\User;

class CommandController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/customer/commands",
     *     operationId="getCommands",
     *     tags={"Commandes"},
     *     summary="Récupérer la liste des commandes",
     *     @OA\Response(response=200, description="Liste des commandes récupérée avec succès"),
     *     @OA\Response(response=500, description="Erreur interne"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $commands = Command::with(['user'])
                            ->where('user_id', Auth::id())
                            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des commandes récupérée avec succès.',
            'commands' => $commands
        ], 200);
    }

     /**
     * @OA\Post(
     *     path="/api/customer/commands",
     *     operationId="createCommand",
     *     tags={"Commandes"},
     *     summary="Créer une nouvelle commande",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "area", "levels_number", "materials", "zone", "construction_type", "command_type", "deadline"},
     *                 @OA\Property(property="name", type="string", description="Nom de la commande"),
     *                 @OA\Property(property="area", type="number", format="float", description="Superficie de l'oeuvre (m²)"),
     *                 @OA\Property(property="levels_number", type="integer", description="Nombre de niveaux (rez de chaussée + étages)"),
     *                 @OA\Property(property="materials", type="string", description="Liste des materiaux de construction"),
     *                 @OA\Property(property="zone", type="string", description="Zone de la construction", enum={"agglomeration", "village"}),
     *                 @OA\Property(property="construction_type", type="string", description="Type de construction", enum={"individual", "public"}),
     *                 @OA\Property(property="command_type", type="string", description="Type de commande", enum={"entire", "element"}),
     *                 @OA\Property(property="file", type="string", format="binary", description="Piece jointe pour appuyer la commande"),
     *                 @OA\Property(property="deadline", type="string", format="date", description="Date limite de traitement de la commande"),
     *                @OA\Property(property="comment", type="string", description="Commentaire et info supplémentaire"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Commande créée avec succès"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur interne"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'area' => 'required|numeric||min:0',
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
            'data' => $command
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/customer/commands/{id}",
     *     operationId="getCommand",
     *     tags={"Commandes"},
     *     summary="Récupérer une commande spécifique",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la commande",
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\Response(response=200, description="Commande récupérée avec succès"),
     *     @OA\Response(response=404, description="Commande non trouvée"),
     *     @OA\Response(response=500, description="Erreur interne"),
     *     security={{"bearerAuth":{}}}
     * )
     */
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
        $command->file_path = asset('storage/' . $command->file_path);
        return response()->json([
            'success' => true,
            'message' => 'Commande récupérée avec succès',
            'data' => $command
        ], 200);
    }

     /**
     * @OA\Put(
     *     path="/api/customer/commands/{id}",
     *     operationId="updateCommand",
     *     tags={"Commandes"},
     *     summary="Mettre à jour une commande",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la commande",
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "area", "levels_number", "materials", "zone", "construction_type", "command_type", "deadline"},
     *                 @OA\Property(property="name", type="string", description="Nom de la commande"),
     *                 @OA\Property(property="area", type="number", format="float", description="Superficie de l'oeuvre (m²)"),
     *                 @OA\Property(property="levels_number", type="integer", description="Nombre de niveaux (rez de chaussée + étages)"),
     *                 @OA\Property(property="materials", type="string", description="Liste des materiaux de construction"),
     *                 @OA\Property(property="zone", type="string", description="Zone de la construction", enum={"agglomeration", "village"}),
     *                 @OA\Property(property="construction_type", type="string", description="Type de construction", enum={"individual", "public"}),
     *                 @OA\Property(property="command_type", type="string", description="Type de commande", enum={"entire", "element"}),
     *                 @OA\Property(property="file", type="string", format="binary", description="Piece jointe pour appuyer la commande"),
     *                 @OA\Property(property="deadline", type="string", format="date", description="Date limite de traitement de la commande"),
     *                @OA\Property(property="comment", type="string", description="Commentaire et info supplémentaire"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Commande mise à jour avec succès"),
     *     @OA\Response(response=404, description="Commande non trouvée"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur interne"),
     *     security={{"bearerAuth":{}}}
     * )
     */
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
            'area' => 'required|numeric||min:0',
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

    /**
     * @OA\Delete(
     *     path="/api/customer/commands/{id}",
     *     operationId="deleteCommand",
     *     tags={"Commandes"},
     *     summary="Supprimer une commande",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la commande",
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\Response(response=200, description="Commande supprimée avec succès"),
     *     @OA\Response(response=404, description="Commande non trouvée"),
     *     @OA\Response(response=500, description="Erreur interne"),
     *     security={{"bearerAuth":{}}}
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/admin/commands/{command_id}/process",
     *     operationId="processCommand",
     *     tags={"Commandes"},
     *     summary="Traiter une commande",
     *     @OA\Parameter(
     *         name="command_id",
     *         in="path",
     *         required=true,
     *         description="ID de la commande",
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"engineer_id", "price", "comment"},
     *                 @OA\Property(property="engineer_id", type="integer", description="ID de la commande a traiter"),
     *                 @OA\Property(property="price", type="number", format="float", description="Prix fixé a la commande"),
     *                 @OA\Property(property="comment", type="string", description="Commentaire de l'admin"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Commande traitée avec succès"),
     *     @OA\Response(response=422, description="Données invalides"),
     *     @OA\Response(response=400, description="Commande déjà traitée"),
     *     @OA\Response(response=404, description="Commande non trouvée"),
     *     security={{"bearerAuth":{}}}
     * )
     */
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
                'message' => $validator->errors(),
            ], 422);
        }

        if ($command->status != 'pending') {
            return response()->json([
                // 'success' => false,
                'message' => [
                    'status' => [
                        'Commande déjà traitée ('. $command->status .').',
                    ],
                    ],
            ], 400);
        }

        if (User::find($request->engineer_id)->role != 'engineer') {
            return response()->json([
                // 'success' => false,
                'message' => [
                    'engineer_id' => [
                        "L'ingénieur spécifié n'existe pas"
                    ],
                ],
            ], 422);
        }

        $command->engineer_id = $request->engineer_id;
        $command->price = $request->price;
        $command->status = 'treated';
        
        do {
            $token = Str::random(64);
            $tokenExist = CommandProcessToken::where('token', $token)->first();
        } while ($tokenExist);
        $encryptedToken = Crypt::encryptString($token);
        
        $commandProcessTokenData = [
            'command_id' => $command->id,
            'token' => $encryptedToken,
            'comment' => $request->comment,
            'created_at' => now(),
        ];

        Mail::to($command->user->email)->send(new CommandProcessMailable($commandProcessTokenData, $command));

        $commandProcessTokenData['token'] = $token;
        CommandProcessToken::create($commandProcessTokenData);
        $command->save();

        return response()->json([
            'success' => true,
            'message' => "Commande traitée avec succès. Un email a été envoyé à l'utilisateur pour validation.",
            'data' => $command,
        ]);

    }

    /**
     * @OA\Get(
     *     path="/api/customer/commands/{token}/validate/{answer}",
     *     operationId="validateCommand",
     *     tags={"Commandes"},
     *     summary="Valider une commande",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token de validation",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="answer",
     *         in="path",
     *         required=true,
     *         description="Réponse de validation ('accept'/'reject')",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Commande validée ou rejetée avec succès"),
     *     @OA\Response(response=400, description="Réponse invalide ou commande déjà traitée"),
     *     @OA\Response(response=404, description="Commande ou token non trouvé"),
     *     @OA\Response(response=500, description="Erreur interne"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function validateCommand($token, $answer) {
        try {
            $decryptedToken = Crypt::decryptString($token);
            $commandProcess = CommandProcessToken::where('token', $decryptedToken)->first();
        }
        catch (\Exception $e) {
            return response()->json([
                // 'success' => false,
                'message' => [
                    'token' => [
                        'Token invalide.'
                        ]
                    ],
            ], 404);
        }

        if (!$commandProcess) {
            return response()->json([
                // 'success' => false,
                'message' => [
                    'token' => [
                        'Aucune commande en attente de traitement avec ce token.'
                        ]
                    ],
            ], 404);
        }
            
        $command = Command::find($commandProcess->command_id);
        if (!$command) {
            return response()->json([
                // 'success' => false,
                'message' => [
                    'command' => [
                        'Aucune commande correspondante.'
                        ]
                    ],
            ], 404);
        }
        
        $commandProcess = $command->commandProcessToken;

        if ($command->status != 'treated') {
            if ($command->status == 'pending') {
                $message = "Cette commande n'a pas encore été traitée par l'admin.";
            } else {
                $message = "Cette commande a déjà été " .($command->status == 'accepted' ? 'acceptée' : 'rejetée'). ".";
            }
            return response()->json([
                // 'success' => false,
                'message' => $message,
            ], 400);
        }

        if ($answer == 'accept') {
            $command->status = 'accepted';
            Mail::to($command->engineer->email)->send(new CommandAssignationMailable($command));

        }
        else if ($answer == 'reject') {
            $command->status = 'rejected';
        }
        else {
            return response()->json([
                // 'success' => false,
                'message' => [
                    'answer' => [
                        'Veuillez fournir une réponse valide (accept ou reject).'
                        ]
                    ],
            ], 400);
        }


        $command->save();
        $commandProcess->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Commande '. ($command->status == 'accepted' ? 'acceptée' : 'rejetée') .' avec succès.',
            'data' => $command,
        ]);
    }

}
