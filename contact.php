<?php

$pageTitle = "Contact Us";

require_once __DIR__ . "/includes/mailer.php";
require_once __DIR__ . "/includes/database.php";

/* ---------------------------------------------------------
   Handle form submission
   --------------------------------------------------------- */
$errors    = [];
$success   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '')    $errors[] = 'Please enter your name.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
                         $errors[] = 'Please enter a valid email address.';
    if ($subject === '') $errors[] = 'Please enter a subject.';
    if ($message === '') $errors[] = 'Please write your message.';

    if (empty($errors)) {

        $submittedAt = date('Y-m-d H:i:s');

        $contact = [
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'subject'      => $subject,
            'message'      => $message,
            'submitted_at' => $submittedAt,
            'ip'           => $_SERVER['REMOTE_ADDR'] ?? '',
        ];

        /* Save to MySQL */
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO messages
                    (Name, Email, Phone, Subject, Message, Status, CreatedAt, IP)
                 VALUES
                    (:name, :email, :phone, :subject, :msg, :status, :created, :ip)'
            );

            $stmt->execute([
                ':name'    => $name,
                ':email'   => $email,
                ':phone'   => $phone,
                ':subject' => $subject,
                ':msg'     => $message,
                ':status'  => 'Unread',
                ':created' => $submittedAt,
                ':ip'      => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

        } catch (Throwable $e) {
            error_log('message insert failed: ' . $e->getMessage());
        }

        /* Keep JSON backup */
        $dir = __DIR__ . '/data/messages';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $filename = $dir . '/' . date('Y-m-d_His') . '_' . bin2hex(random_bytes(3)) . '.json';
        @file_put_contents($filename, json_encode($contact, JSON_PRETTY_PRINT));

        /* Send to business + auto-reply to sender */
        send_contact_notification($contact);
        send_contact_autoreply($contact);

        $success = true;

        /* Clear form values after successful submit */
        $_POST = [];
    }
}

require_once __DIR__ . "/includes/header.php";

?>

<section class="page-hero">
    <div class="container page-hero-content">
        <p class="section-label">GET IN TOUCH</p>
        <h1>Contact FeteCation</h1>
        <p>Have a question or need transportation? We'd love to hear from you.</p>
    </div>
</section>


<section class="content-section">
    <div class="container contact-container">

        <div class="contact-information">
            <p class="section-label">CONTACT INFORMATION</p>
            <h2>Let's Talk</h2>
            <p>
                Get in touch with FeteCation Taxi & Tours for
                transportation, tour information or general
                enquiries.
            </p>

            <!-- Phone -->
            <div class="contact-item">
                <div class="contact-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <div>
                    <h3>Phone</h3>
                    <p>+1 473 459-7404</p>
                </div>
            </div>

            <!-- Email -->
            <div class="contact-item">
                <div class="contact-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
                <div>
                    <h3>Email</h3>
                    <p>fetecation9@gmail.com</p>
                </div>
            </div>

            <!-- Location -->
            <div class="contact-item">
                <div class="contact-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <div>
                    <h3>Location</h3>
                    <p>Grenada</p>
                </div>
            </div>

            <!-- WhatsApp -->
            <div class="contact-item">
                <div class="contact-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                </div>
                <div>
                    <h3>WhatsApp</h3>
                    <p>
                        <a href="https://wa.me/14734597407?text=Hi%20FeteCation!%20I%20have%20a%20question."
                           target="_blank"
                           rel="noopener"
                           style="color:var(--fete-orange-dark);font-weight:700;">
                            Chat with us →
                        </a>
                    </p>
                </div>
            </div>

        </div>


        <div class="contact-form-card">
            <h2>Send Us a Message</h2>

            <?php if ($success): ?>
                <div class="form-success">
                    <strong>✅ Message sent!</strong>
                    <p>
                        Thanks for reaching out. We've received your message
                        and will reply within 24 hours.
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="form-errors">
                    <strong>Please fix the following:</strong>
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="/fetecation/contact.php">

                <div class="form-group">
                    <label for="name">Name *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Your name"
                        required
                        value="<?= htmlspecialchars($_POST['name'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Your email"
                        required
                        value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="Your phone number"
                        value="<?= htmlspecialchars($_POST['phone'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        placeholder="How can we help?"
                        required
                        value="<?= htmlspecialchars($_POST['subject'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        placeholder="Your message"
                        required
                    ><?= htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="primary-button">
                    Send Message
                </button>

            </form>

        </div>

    </div>
</section>


<!-- ============ GOOGLE MAP ============ -->
<section class="contact-map-section">
    <div class="container">
        <div class="contact-map">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d499050.5732002024!2d-61.920646549878185!3d12.259604939929211!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8c381789f4877bd1%3A0x36d8c7015a1f4f8e!2sGrenada!5e0!3m2!1sen!2s!4v1791650126440!5m2!1sen!2s"
                width="100%"
                height="420"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin">
            </iframe>
        </div>
    </div>
</section>


<?php

require_once __DIR__ . "/includes/footer.php";

?>