<?php
/**
 * Simple mailer helper.
 * Sends booking notification emails.
 */

/**
 * Send the "new booking" email to the business.
 */
function send_booking_notification($booking)
{
    $to      = 'quintelcharles@ccagrenada.com';
    $subject = 'New Booking: ' . $booking['tour'] . ' — ' . $booking['name'];

    $body = "New booking received.\n\n"
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

    $headers = "From: FeteCation Bookings <quintelcharles@ccagrenada.com>\r\n"
             . "Reply-To: {$booking['email']}\r\n"
             . "Content-Type: text/plain; charset=UTF-8\r\n";

    @mail($to, $subject, $body, $headers);
}


/**
 * Send the "thanks, we got it" confirmation to the customer.
 */
function send_customer_confirmation($booking)
{
    $to      = $booking['email'];
    $subject = 'We got your booking — ' . $booking['tour'];

    $body = "Hi {$booking['name']},\n\n"
          . "Thanks for booking with FeteCation!\n\n"
          . "Here's a summary of your request:\n\n"
          . "Tour:         {$booking['tour']}\n"
          . "Date:         {$booking['date']}\n"
          . "Group size:   {$booking['guests']}\n"
          . "Pickup:       {$booking['pickup']}\n"
          . "Reference:    {$booking['reference']}\n\n"
          . "We'll confirm availability and send you the final details within 24 hours.\n\n"
          . "If you need to reach us sooner:\n"
          . "📞 +1 (555) 000-0000\n"
          . "💬 WhatsApp: https://wa.me/15550000000\n\n"
          . "See you soon,\n"
          . "The FeteCation Team\n";

    $headers = "From: FeteCation <quintelcharles@ccagrenada.com>\r\n"
             . "Reply-To: quintelcharles@ccagrenada.com\r\n"
             . "Content-Type: text/plain; charset=UTF-8\r\n";

    @mail($to, $subject, $body, $headers);
}