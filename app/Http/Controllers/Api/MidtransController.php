<?php

namespace App\Http\Controllers\API;

use Midtrans\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        // Set config Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitizied');
        Config::$is3ds = config('services.midtrans.is3ds');

        // Create instance midtrans notif
        $notification = new Notification();

        // Assign ke varieble untuk memudahkan config
        $status = $notification->transaction_status;
        $type = $notification->type;
        $fraud = $notification->fraud_status;
        $order_id = $notification->order_id;

        // seacrh transaction with id
        $transaction = Transaction::findOrFail($order_id);

        // handle notif status midtrans
        if ($status == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $transaction->status = 'PENDING';
                } else {
                    $transaction->status = 'SUCCESS';
                }
            }
        } else if ($status == 'settlement') {
            $transaction->status = 'SUCCESS';
        } else if ($status == 'pending') {
            $transaction->status = 'PENDING';
        } else if ($status == 'deny') {
            $transaction->status = 'CANCELLED';
        } else if ($status == 'expired') {
            $transaction->status = 'CANCELLED';
        } else if ($status == 'cancel') {
            $transaction->status = 'CANCELLED';
        }
        // save transaction
        $transaction->save();

        
    }
}
