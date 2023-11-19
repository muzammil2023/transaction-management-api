<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    //create transaction 
    public function create(Request $request)
    {

        // if get method
        if ($request->isMethod('get')) {

            //customer list select name and email and id
            $users = \App\Models\User::select('name', 'email', 'id')->where('role', 'customer')->get();
            return view('create', [
                'users' => $users
            ]);
        }

        //save transaction
        try {
            $request->validate([
                'amount' => 'required|numeric',
                'due_on' => 'required|date_format:Y-m-d',
                'is_vat_inclusive' => 'nullable|boolean', //set defalt value false
                'payer' => 'required|exists:users,id',
                'vat' => 'required|numeric|between:0,100',
                'paid_on' => 'nullable|date_format:Y-m-d|before_or_equal:today',
                'paid_amount' => 'nullable|numeric',
                'detail' => 'nullable|string',
            ]);

            //if is vat inclusive not set or null set defalt value false
            if (!$request->is_vat_inclusive || $request->is_vat_inclusive == null) {
                $request->merge(['is_vat_inclusive' => false]);
            }

            // Determine transaction status
            if ($request->paid_on) {
                $isOverdue = $request->paid_on > $request->due_on;
            } else if ($request->paid_amount) {
                // due_on is greater than today
                $isOverdue = date('Y-m-d') > $request->due_on;
            } else {
                $isOverdue = false;
            }

            if ($isOverdue) {
                $status = 'overdue';
            } else {
                // if it is inclusive
                if (!$request->is_vat_inclusive) {
                    $grossAmount = $request->amount + ($request->amount * ($request->vat / 100));
                } else {
                    $grossAmount = $request->amount;
                }
                if ($request->paid_amount) {
                    $isPaid = $grossAmount == $request->paid_amount;
                    if ($isPaid) {
                        $status = 'paid';
                    }
                }
            }
            // Merge status into request
            if (isset($status)) {
                $request->merge(['status' => $status]);
            }

            //create transaction
            $tran = Transaction::create([
                'amount' => $request->amount,
                'due_on' => $request->due_on,
                'is_vat_inclusive' => $request->is_vat_inclusive,
                'user_id' => $request->payer,
                'vat' => $request->vat,
                'status' => $request->status,
            ]);

            //create payment
            $tran->payments()->create([
                'amount' => $request->paid_amount,
                'paid_on' => $request->paid_on ?: date('Y-m-d'),
                'transaction_id' => $tran->id,
                'details' => $request->detail,
            ]);

            // Return a success response or do any additional processing
            return response()->json(['message' => 'Transaction created successfully'], 201);
        } catch (ValidationException  $e) {
            // Log the error for debugging
            Log::error($e->errors());

            // Determine the appropriate response based on the error
            if (!$request->expectsJson()) {
                //redirect to creat with error list $e->errors() with old inputs
                return redirect()->route('transaction.create')->withErrors($e->errors())->withInput();
            }
            // Return an error response
            return response()->json(['error' => 'Transaction creation failed'], 500);
        }
    }

    // index
    public function index(Request $request)
    {
        $q = Transaction::with('user', 'payments');
        if (auth()->user()->role == 'customer') {
            $q->where('user_id', auth()->user()->id);
        }
        $q->orderBy('status', 'desc');
        $data =  $q->simplePaginate(10);

        if (!$request->expectsJson()) {

            return view('index', [
                'data' => $data
            ]);
        }
        return response()->json([
            'data' => $data
        ], 200);
    }

    // generate report
    public function generateReport(Request $request)
    {

        //validate start date and end date
        $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after:start_date',
        ]);

        $data = Transaction::query()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as paid'),
                DB::raw('SUM(CASE WHEN status = "outstanding" THEN amount ELSE 0 END) as outstanding'),
                DB::raw('SUM(CASE WHEN status = "overdue" THEN amount ELSE 0 END) as overdue')
            )
            ->when($request->start_date, function ($query) use ($request) {
                $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
            })
            ->groupBy('year', 'month')
            ->simplePaginate(10);

        if (!$request->expectsJson()) {

            return view('report', [
                'data' => $data
            ]);
        }

        return response()->json([
            'data' => $data
        ], 200);
    }

    // record payment
    public function recordPayment(Request $request, Transaction $transaction)
    {

        // get method 
        if ($request->isMethod('get')) {

            return view('record_payment', [
                'transaction' => $transaction
            ]);
        }
        $request->validate([
            'amount' => 'required|numeric',
            'paid_on' => 'required|date_format:Y-m-d',
            'details' => 'nullable|string',
        ]);

        $transaction->payments()->create([
            'amount' => $request->amount,
            'paid_on' => $request->paid_on,
            'details' => $request->details,
        ]);

        // Determine transaction status
        $isOverdue = $request->paid_on > $transaction->due_on;

        if ($isOverdue) {
            $status = 'overdue';
            $transaction->update(['status' => $status]);
        } else {
            //sum of all payments from user for this transaction is paid_amount
            $paid_amount = $transaction->amountPaid();
            $isPaid = $transaction->grossAmount() <= $paid_amount;
            if ($isPaid) {
                $status = 'paid';
                $transaction->update(['status' => $status]);
            }
        }

        if (!$request->expectsJson()) {
            return redirect()->route('transaction.payment', $transaction->id);
        }

        return response()->json([
            'success' => true
        ], 201);
    }
}
