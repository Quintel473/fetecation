<?php

$pageTitle = "Book Now";

require_once __DIR__ . "/includes/header.php";

?>

<section class="page-hero">

    <div class="container page-hero-content">

        <p class="section-label">
            FETECATION TAXI & TOURS
        </p>

        <h1>
            Book Your Journey
        </h1>

        <p>
            Request a taxi ride or plan your next island experience.
        </p>

    </div>

</section>


<section class="content-section">

    <div class="container booking-container">

        <div class="booking-intro">

            <p class="section-label">
                BOOK WITH US
            </p>

            <h2>
                Tell Us About Your Trip
            </h2>

            <p>
                Complete the form and provide the details of your
                transportation or tour request.
            </p>

            <div class="booking-note">

                <strong>
                    Please Note
                </strong>

                <p>
                    Submitting a booking request does not yet
                    constitute a confirmed reservation. A member
                    of the FeteCation team can confirm availability
                    and details with you.
                </p>

            </div>

        </div>


        <div class="booking-form-card">

            <form method="POST" action="">

                <div class="form-row">

                    <div class="form-group">

                        <label for="first_name">
                            First Name
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="last_name">
                            Last Name
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            required
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="phone">
                            Phone
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            required
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
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="booking_type">
                        Booking Type
                    </label>

                    <select
                        id="booking_type"
                        name="booking_type"
                        required
                    >

                        <option value="">
                            Select booking type
                        </option>

                        <option value="Taxi">
                            Taxi
                        </option>

                        <option value="Tour">
                            Tour
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="pickup">
                        Pickup Location
                    </label>

                    <input
                        type="text"
                        id="pickup"
                        name="pickup"
                        placeholder="Where should we pick you up?"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="dropoff">
                        Drop-off Location
                    </label>

                    <input
                        type="text"
                        id="dropoff"
                        name="dropoff"
                        placeholder="Where are you going?"
                    >

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="booking_date">
                            Date
                        </label>

                        <input
                            type="date"
                            id="booking_date"
                            name="booking_date"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="booking_time">
                            Time
                        </label>

                        <input
                            type="time"
                            id="booking_time"
                            name="booking_time"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="passengers">
                        Number of Passengers
                    </label>

                    <input
                        type="number"
                        id="passengers"
                        name="passengers"
                        min="1"
                        value="1"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="special_requests">
                        Special Requests
                    </label>

                    <textarea
                        id="special_requests"
                        name="special_requests"
                        rows="5"
                        placeholder="Anything else we should know?"
                    ></textarea>

                </div>


                <button type="submit" class="primary-button">
                    Submit Booking Request
                </button>

            </form>

        </div>

    </div>

</section>


<?php

require_once __DIR__ . "/includes/footer.php";

?>