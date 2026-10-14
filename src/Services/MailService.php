<?php
/**
 * TechFusion Email & Notification Service
 * Dispatches order confirmations and invoice notifications.
 */

declare(strict_types=1);

namespace TechFusion\Services;

class MailService {
    public static function sendOrderConfirmation(array $order): bool {
        $to = $order['customer_email'];
        $subject = "TechFusion Order Confirmation #" . $order['id'] . " - " . $order['tracking_number'];
        $body = "Dear " . $order['customer_name'] . ",\n\n"
              . "Thank you for shopping at TechFusion. Your order has been placed and is currently in status: " . $order['order_status'] . ".\n\n"
              . "Tracking ID: " . $order['tracking_number'] . "\n"
              . "Total Amount: $" . number_format((float)$order['total_amount'], 2) . "\n\n"
              . "Track your shipment live at: " . env('APP_URL') . "/track-order.php?tracking=" . urlencode($order['tracking_number']) . "\n\n"
              . "Best regards,\nTechFusion Fulfillment Team";

        // If mail driver is log, log to error_log
        if (env('MAIL_DRIVER', 'log') === 'log') {
            error_log("[TechFusion Notification Log]: Sent to {$to} | Subject: {$subject}");
            return true;
        }

        $headers = 'From: ' . env('MAIL_FROM_ADDRESS', 'support@techfusion.com') . "\r\n" .
                   'Reply-To: ' . env('MAIL_FROM_ADDRESS', 'support@techfusion.com') . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        return @mail($to, $subject, $body, $headers);
    }
}
