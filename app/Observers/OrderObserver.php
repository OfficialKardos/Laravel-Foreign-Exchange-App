<?php

namespace App\Observers;

use App\Models\Order;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
         $currencyCode = $order->currency->code ?? null;
        
        Log::info('OrderObserver: Order created event triggered', [
            'order_id' => $order->id,
            'currency_id' => $order->currency_id,
            'currency_code' => $currencyCode
        ]);
        
        if ($currencyCode === 'GBP') {
            Log::info('OrderObserver: Sending GBP email notification', [
                'order_id' => $order->id,
                'currency_code' => $currencyCode
            ]);
            $this->sendGbpOrderEmail($order, $currencyCode);
        } else {
            Log::info('OrderObserver: Not sending email - currency is not GBP', [
                'order_id' => $order->id,
                'currency_code' => $currencyCode
            ]);
        }
    }

    /**
     * Send email notification for GBP orders
     */
    private function sendGbpOrderEmail($order)
    {
        Log::info('OrderObserver: Preparing to send email', [
            'order_id' => $order->id,
            'mail_host' => env('MAIL_HOST'),
            'mail_port' => env('MAIL_PORT', 587),
            'mail_username' => env('MAIL_USERNAME')
        ]);
        
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', PHPMailer::ENCRYPTION_STARTTLS);
            $mail->Port       = env('MAIL_PORT', 587);
            
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function($str, $level) {
                Log::debug("PHPMailer Debug: $str");
            };
            
            $mail->setFrom('no-reply@currencies.com', 'Order System Foreign Currencies');
            $mail->addAddress('karl@currencies.com');
            
            $mail->isHTML(true);
            $mail->Subject = 'New GBP Order';
            $mail->Body    = "
                <h2>New GBP Order</h2>
                <p><strong>Order Number:</strong> {$order->id}</p>
                <p><strong>Foreign Amount:</strong> {$order->foreign_amount}</p>
                <p><strong>Exchange Rate:</strong> {$order->exchange_rate}</p>
                <p><strong>Surcharge %:</strong> {$order->surcharge_percentage}%</p>
                <p><strong>Surcharge Amount:</strong> {$order->surcharge_amount}</p>
                <p><strong>ZAR Amount:</strong> {$order->zar_amount}</p>
            ";
            
            $mail->send();
            Log::info('OrderObserver: Email sent successfully', ['order_id' => $order->id]);
            
        } catch (Exception $e) {
            Log::error('OrderObserver: Email sending failed', [
                'order_id' => $order->id,
                'error' => $mail->ErrorInfo,
                'exception' => $e->getMessage()
            ]);
        }
    }
}