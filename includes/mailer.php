<?php
/**
 * Mailer helper — sends booking + contact + account emails via Gmail SMTP + PHPMailer.
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
const FETE_SMTP_PASS = 'yrob tfxz zmml mqdf';
const FETE_SMTP_TO   = 'qunitelcharles@gmail.com';


/**
 * Build a configured PHPMailer instance.
 */
function fete_mailer()
{
    $mail = new PHPMailer(true);

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


/* =========================================================
   BOOKING EMAILS
   ========================================================= */

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

    } catch (Exception $e) {
        error_log('Booking notification failed: ' . $mail->ErrorInfo);
    }
}


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
            . "Phone/WhatsApp: +1 (473) 456-0954\n\n"
            . "See you soon,\n"
            . "The FeteCation Team\n";

        $mail->send();

    } catch (Exception $e) {
        error_log('Customer confirmation failed: ' . $mail->ErrorInfo);
    }
}


/* =========================================================
   CONTACT FORM EMAILS
   ========================================================= */

function send_contact_notification($contact)
{
    try {
        $mail = fete_mailer();
        $mail->addAddress(FETE_SMTP_TO);
        $mail->addReplyTo($contact['email'], $contact['name']);

        $mail->Subject = 'New Contact Message: ' . $contact['subject'];

        $mail->Body =
              "New contact form message received.\n\n"
            . "Name:      {$contact['name']}\n"
            . "Email:     {$contact['email']}\n"
            . "Phone:     {$contact['phone']}\n"
            . "Subject:   {$contact['subject']}\n\n"
            . "Message:\n"
            . "{$contact['message']}\n\n"
            . "Submitted: {$contact['submitted_at']}\n";

        $mail->send();

    } catch (Exception $e) {
        error_log('Contact notification failed: ' . $mail->ErrorInfo);
    }
}


function send_contact_autoreply($contact)
{
    try {
        $mail = fete_mailer();
        $mail->addAddress($contact['email'], $contact['name']);
        $mail->addReplyTo(FETE_SMTP_TO, 'FeteCation Taxi & Tours');

        $mail->Subject = 'We got your message — FeteCation';

        $mail->Body =
              "Hi {$contact['name']},\n\n"
            . "Thanks for reaching out to FeteCation!\n\n"
            . "We've received your message and will get back to you\n"
            . "within 24 hours.\n\n"
            . "For reference, here's a copy of what you sent:\n\n"
            . "Subject: {$contact['subject']}\n"
            . "{$contact['message']}\n\n"
            . "If you need a faster reply, message us on WhatsApp:\n"
            . "https://wa.me/14734560954\n\n"
            . "Talk soon,\n"
            . "The FeteCation Team\n";

        $mail->send();

    } catch (Exception $e) {
        error_log('Contact autoreply failed: ' . $mail->ErrorInfo);
    }
}


/* =========================================================
   ACCOUNT EMAILS — admin notifications
   ========================================================= */

function send_admin_new_customer_notification(array $customer): bool
{
    try {
        $mail = fete_mailer();
        $mail->addAddress(FETE_SMTP_TO);
        $mail->addReplyTo($customer['email'], $customer['name']);

        $mail->Subject = 'New Customer Signup — ' . $customer['name'];

        $mail->Body =
              "A new customer just created an account.\n\n"
            . "Name:      {$customer['name']}\n"
            . "Email:     {$customer['email']}\n"
            . "Phone:     " . ($customer['phone'] ?: '—') . "\n"
            . "Signed up: {$customer['submitted_at']}\n"
            . "Verified:  No (waiting on email confirmation)\n\n"
            . "View customer in admin panel:\n"
            . "http://localhost/fetecation/staff-7742/index.php\n";

        $mail->send();
        return true;

    } catch (Throwable $e) {
        error_log('Admin signup notification failed: ' . $e->getMessage());
        return false;
    }
}

/* =========================================================
   BOOKING STATUS EMAILS (sent by admin action)
   ========================================================= */

/**
 * Notify the customer that their booking has been confirmed.
 */
function send_booking_confirmed_email(array $booking): bool
{
    try {
        $mail = fete_mailer();
        $mail->addAddress($booking['Email'], $booking['Name']);
        $mail->addReplyTo(FETE_SMTP_TO, 'FeteCation Taxi & Tours');

        $tourLabel = ($booking['TourSlug'] === 'taxi' || empty($booking['TourSlug']))
                     ? 'Taxi Service'
                     : ($booking['TourSlug'] ?: 'Tour');

        $mail->Subject = '✅ Your booking is confirmed — ' . $booking['Reference'];

        $body =
              "Hi {$booking['Name']},\n\n"
            . "Great news — your booking is confirmed!\n\n"
            . "Here are the final details:\n\n"
            . "Reference:    {$booking['Reference']}\n"
            . "Experience:   {$tourLabel}\n"
            . "Date:         {$booking['BookingDate']}\n"
            . "Guests:       {$booking['NumberOfPassengers']}\n";

        if (!empty($booking['PickupLocation'])) {
            $body .= "Pickup:       {$booking['PickupLocation']}\n";
        }

        if (!empty($booking['Notes'])) {
            $body .= "Notes:        {$booking['Notes']}\n";
        }

        $body .=
              "\n"
            . "We'll be in touch the day before with any final reminders.\n\n"
            . "If anything has changed, just reply to this email or message us on WhatsApp:\n"
            . "https://wa.me/14734560954\n\n"
            . "Looking forward to seeing you!\n"
            . "The FeteCation Team\n";

        $mail->Body = $body;

        $mail->send();
        return true;

    } catch (Throwable $e) {
        error_log('Booking confirmed email failed: ' . $e->getMessage());
        return false;
    }
}


/**
 * Notify the customer that their booking has been cancelled.
 */
function send_booking_cancelled_email(array $booking): bool
{
    try {
        $mail = fete_mailer();
        $mail->addAddress($booking['Email'], $booking['Name']);
        $mail->addReplyTo(FETE_SMTP_TO, 'FeteCation Taxi & Tours');

        $tourLabel = ($booking['TourSlug'] === 'taxi' || empty($booking['TourSlug']))
                     ? 'Taxi Service'
                     : ($booking['TourSlug'] ?: 'Tour');

        $mail->Subject = 'Your booking was cancelled — ' . $booking['Reference'];

        $mail->Body =
              "Hi {$booking['Name']},\n\n"
            . "We're sorry, but your booking has been cancelled.\n\n"
            . "Booking details:\n\n"
            . "Reference:    {$booking['Reference']}\n"
            . "Experience:   {$tourLabel}\n"
            . "Date:         {$booking['BookingDate']}\n\n"
            . "If this is unexpected, or you'd like to rebook,\n"
            . "please reach out to us:\n\n"
            . "Phone/WhatsApp: +1 (473) 456-0954\n"
            . "Email:          qunitelcharles@gmail.com\n\n"
            . "We'd love another chance to make it right.\n\n"
            . "— The FeteCation Team\n";

        $mail->send();
        return true;

    } catch (Throwable $e) {
        error_log('Booking cancelled email failed: ' . $e->getMessage());
        return false;
    }
}

/* =========================================================
   PAYMENT EMAILS
   ========================================================= */

/**
 * Email the customer a PayPal payment link.
 */
function send_payment_link_email(array $booking): bool
{
    try {
        $mail = fete_mailer();
        $mail->addAddress($booking['Email'], $booking['Name']);
        $mail->addReplyTo(FETE_SMTP_TO, 'FeteCation Taxi & Tours');

        $payUrl = 'http://localhost/fetecation/payment-start.php?ref=' . urlencode($booking['Reference']);

        $tourLabel = ($booking['TourSlug'] === 'taxi' || empty($booking['TourSlug']))
                     ? 'Taxi Service'
                     : ($booking['TourSlug'] ?: 'Tour');

        $mail->Subject = '💳 Complete your FeteCation payment — ' . $booking['Reference'];

        $mail->Body =
              "Hi {$booking['Name']},\n\n"
            . "Thanks for booking with FeteCation!\n\n"
            . "Here's a secure link to complete your payment:\n\n"
            . $payUrl . "\n\n"
            . "Booking details:\n\n"
            . "Reference:    {$booking['Reference']}\n"
            . "Experience:   {$tourLabel}\n"
            . "Date:         {$booking['BookingDate']}\n"
            . "Guests:       {$booking['NumberOfPassengers']}\n\n"
            . "Payment is secured by PayPal. You can pay with a card or PayPal balance.\n\n"
            . "If you have any questions, just reply to this email.\n\n"
            . "See you soon,\n"
            . "The FeteCation Team\n";

        $mail->send();
        return true;

    } catch (Throwable $e) {
        error_log('Payment link email failed: ' . $e->getMessage());
        return false;
    }
}