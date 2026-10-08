<?php
/**
 * Mailer helper — sends booking emails via Gmail SMTP + PHPMailer.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';


/* =========================================================
   CONFIG
   ========================================================= */

const FETE_SMTP_USER = 'qunitelcharles@gmail.com';
const FETE_SMTP_PASS = 'yrob tfxz zmml mqdf';  // ← paste your NEW App Password here
const FETE_SMTP_TO   = 'qunitelcharles@gmail.com';


/**
 * Build a configured PHPMailer instance.
 */
function fete_mailer()
{
    $mail = new PHPMailer(true);

    $mail->SMTPDebug   = 2;
    $mail->Debugoutput = 'echo';

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = FETE_SMTP_USER;
    $mail->Password   = FETE_SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom(FETE_SMTP_USER, 'FeteCation Taxi & Tours');
    $mail->isHTML(false);
    $mail->CharSet = 'UTF-8';

    return $mail;
}


/**
 * Send the "new booking" notification to the business.
 */
function send_booking_notification($booking)
{
    try {
        $mail = fete_mailer();
        $mail->addAddress(FETE_SMTP_TO);
        $mail->addReplyTo($booking['email'], $booking['name']);

        $mail->Subject = 'New Booking: ' . $booking['tour'] . ' — ' . $booking['name'];

        $mail->Body =
              "New booking received.\n\n"
            . "Tour:         {$booking['tour']}\n"
            . "Name:         {$booking['name']}\n"
            . "Email:        {$booking['email']}\n"
            . "Phone:        {$booking['phone']}\n"
            . "Date:         {$booking['date']}\n"
            . "Group size:   {$booking['guests']}\n"
            . "Pickup:       {$booking['pickup']}\n"
            . "Notes:        {$booking['notes']}\n"
            . "Submitted:    {$booking['submitted_at']}\n"
            . "Reference:    {$booking['reference']}\n";

        $mail->send();

        echo "<p style='color:green;font-weight:bold;'>✅ Booking notification sent.</p>";

    } catch (Exception $e) {
        echo "<div style='background:#ffe;border:1px solid #e9783f;padding:12px;margin:12px;font-family:monospace;'>";
        echo "<strong>Booking notification FAILED:</strong><br>";
        echo htmlspecialchars($mail->ErrorInfo);
        echo "</div>";
    }
}


/**
 * Send the "thanks, we got it" confirmation to the customer.
 */
function send_customer_confirmation($booking)
{
    try {
        $mail = fete_mailer();
        $mail->addAddress($booking['email'], $booking['name']);
        $mail->addReplyTo(FETE_SMTP_TO, 'FeteCation Taxi & Tours');

        $mail->Subject = 'We got your booking — ' . $booking['tour'];

        $mail->Body =
              "Hi {$booking['name']},\n\n"
            . "Thanks for booking with FeteCation!\n\n"
            . "Here's a summary of your request:\n\n"
            . "Tour:         {$booking['tour']}\n"
            . "Date:         {$booking['date']}\n"
            . "Group size:   {$booking['guests']}\n"
            . "Pickup:       {$booking['pickup']}\n"
            . "Reference:    {$booking['reference']}\n\n"
            . "We'll confirm availability and send you the final details within 24 hours.\n\n"
            . "If you need to reach us sooner:\n"
            . "Phone/WhatsApp: +1 (555) 000-0000\n\n"
            . "See you soon,\n"
            . "The FeteCation Team\n";

        $mail->send();

        echo "<p style='color:green;font-weight:bold;'>✅ Customer confirmation sent.</p>";

    } catch (Exception $e) {
        echo "<div style='background:#ffe;border:1px solid #e9783f;padding:12px;margin:12px;font-family:monospace;'>";
        echo "<strong>Customer confirmation FAILED:</strong><br>";
        echo htmlspecialchars($mail->ErrorInfo);
        echo "</div>";
    }
}