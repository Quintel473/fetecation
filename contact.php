<?php

$pageTitle = "Contact Us";

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
                    <p>+1 473 459-7407</p>
                </div>

            </div>


            <div class="contact-item">

                <div class="contact-icon">
                    ✉️
                </div>

                <div>
                    <h3>Email</h3>
                    <p>fetecation9@gmail.com</p>
                </div>

            </div>


            <div class="contact-item">

                <div class="contact-icon">
                    📍
                </div>

                <div>
                    <h3>Location</h3>
                    <p>Grenada, Caribbean</p>
                </div>

            </div>

        </div>


        <div class="contact-form-card">

            <h2>
                Send Us a Message
            </h2>

            <form method="POST" action="">

                <div class="form-group">

                    <label for="name">
                        Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Your name"
                    >

                </div>


                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Your email"
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
                    >

                </div>


                <div class="form-group">

                    <label for="subject">
                        Subject
                    </label>

                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        placeholder="How can we help?"
                    >

                </div>


                <div class="form-group">

                    <label for="message">
                        Message
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        placeholder="Your message"
                    ></textarea>

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