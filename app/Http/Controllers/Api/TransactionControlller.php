<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class TransactionControlller extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $food_id = $request->input('food_id');
        $status = $request->input('status');

        

        if ($id) {
            $transaction = Transaction::with(['food','id'])->find($id);
            if ($transaction) {
                return ResponseFormatter::success($transaction, 'Data transaksi berhasil diambil');
            } else {
                return ResponseFormatter::error(null, 'Data transaksi tidak ada', 404);
            }
        }

        $transaction = Transaction::with(['food','user'])->where('user_id',Auth::user()->id);

        if ($food_id) {
            $transaction->where('food_id',$food_id);
        }

        if ($status) {
            $transaction->where('status',$status);
        }
        
        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data List Transaksi berhasil diambil'
        );
    }

    public function update(Request $request,$id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());

        return ResponseFormatter::success($transaction,'Transaksi berhasil diperbaharui');
    }
}
