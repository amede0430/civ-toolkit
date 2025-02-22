<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan;
use App\Models\Command;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::where('user_id', Auth::id())->get();
        return response()->json([
            'success' => true,
            'message' => 'Liste des paiements récupérée avec succès.',
            'data' => $payments
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|min:1',
            'product_type' => 'required|in:plan,command',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string|max:255',
            'reference' => 'required|string|unique:payments,reference',
            'command_state' => 'exclude_if:product_type,plan|required_if:product_type,command|in:first_half,second_half',
            'paid' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        if ($request->product_type == 'plan' && !((bool) Plan::find($request->product_id))) {
            return response()->json([
                // 'success' => false,
                'message' => [
                    'product_id' => ['Le plan n\'existe pas']
                ]
            ], 422);
        }
        else if ($request->product_type == 'command' && !((bool) Command::find($request->product_id))) {
            return response()->json([
                // 'success' => false,
                'message' => [
                    'product_id' => ['La commande n\'existe pas']
                ]
            ], 422);
        }

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'product_type' => $request->product_type,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'command_state' => $request->command_state,
            'paid' => $request->paid,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Paiement enregistré avec succès',
            'data' => $payment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                // 'success' => false,
                'message' => 'Paiement non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Paiement récupéré avec succès',
            'data' => $payment
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'message' => 'Paiement non trouvé'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|min:1',
            'product_type' => 'required|in:plan,command',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string|max:255',
            'reference' => 'required|string|unique:payments,reference',
            'command_state' => 'required_if:product_type,command|in:first_half,second_half',
            'paid' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'Paiement mis à jour avec succès',
            'data' => $payment,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                // 'success' => false,
                'message' => 'Paiement non trouvé'
            ], 404);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paiement supprimé avec succès.'
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $payment = Payment::where('reference', $request->reference)->first();

        if (!$payment) {
            return response()->json([
                // 'success' => false,
                'message' => 'Paiement non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Paiement trouvé',
            'data' => $payment,
        ], 200);
    }
}
