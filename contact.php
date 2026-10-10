<?php

$pageTitle = "Contact Us";

require_once __DIR__ . "/includes/mailer.php";

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

        /* Save to disk */
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

        <p class="section-label">
            GET IN TOUCH
        </p>

        <h1>
            Contact FeteCation
        </h1>

        <p>
            Have a question or need transportation?
            We'd love to hear from you.
        </p>

    </div>

</section>


<section class="content-section">

    <div class="container contact-container">

        <div class="contact-information">

            <p class="section-label">
                CONTACT INFORMATION
            </p>

            <h2>
                Let's Talk
            </h2>

            <p>
                Get in touch with FeteCation Taxi & Tours for
                transportation, tour information or general
                enquiries.
            </p>


            <div class="contact-item">

                <div class="contact-icon">
                    📞
                </div>

                <div>
                    <h3>Phone</h3>
                    <p>+1 473 456-0954</p>
                </div>

            </div>


            <div class="contact-item">

                <div class="contact-icon">
                    ✉️
                </div>

                <div>
                    <h3>Email</h3>
                    <p>qunitelcharles@gmail.com</p>
                </div>

            </div>


            <div class="contact-item">

                <div class="contact-icon">
                    📍
                </div>

                <div>
                    <h3>Location</h3>
                    <p>Grenada</p>
                </div>

            </div>


            <div class="contact-item">

                <div class="contact-icon">
                    💬
                </div>

                <div>
                    <h3>WhatsApp</h3>
                    <p>
                        <a href="https://wa.me/14734560954?text=Hi%20FeteCation!%20I%20have%20a%20question."
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

            <h2>
                Send Us a Message
            </h2>

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

                    <label for="name">
                        Name *
                    </label>

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

                    <label for="email">
                        Email *
                    </label>

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

                    <label for="phone">
                        Phone
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="Your phone number"
                        value="<?= htmlspecialchars($_POST['phone'] ?? ''); ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="subject">
                        Subject *
                    </label>

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

                    <label for="message">
                        Message *
                    </label>

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


<?php

require_once __DIR__ . "/includes/footer.php";

?>