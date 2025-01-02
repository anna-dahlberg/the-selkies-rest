<?php

declare(strict_types=1);

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/functions.php');
require(__DIR__ . '/calendar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/styles/styles.css">
    <link rel="stylesheet" href="https://use.typekit.net/ucb3kmg.css">

    <!-- Flatpicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>The Selkies Rest</title>
</head>

<body>

    <header>

        <div class="hero">
            <h1>The Selkies Rest</h1>
        </div>

    </header>

    <main>
        <div id="calendar"></div>

        <section class="bookingForm">
            <h2>Book Your Stay</h2>

            <form action="booking.php" method="POST">


                <div class="formGroup">
                    <label for="name">Your Name:</label>
                    <input type="text" name="name" id="name" required>
                </div>


                <div class="formGroup">
                    <label for="email">Your E-mail:</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required>
                </div>

                <div class="formGroup">
                    <label for="arrivalDate">Arrival Date:</label>
                    <input type="date" id="arrivalDate" name="arrivalDate" min="2025-01-01" max="2025-01-31" required>
                </div>


                <div class="formGroup">
                    <label for="departureDate">Departure Date:</label>
                    <input type="date" id="departureDate" name="departureDate" min="2025-01-01" max="2025-01-31" required>
                </div>


                <div class="formGroup">
                    <label for="roomType">Room Type:</label>
                    <select id="roomType" name="roomType" required>
                        <option value="budget">Budget</option>
                        <option value="standard">Standard</option>
                        <option value="luxury">Luxury</option>
                    </select>
                </div>


                <div class="formGroup">
                    <fieldset>
                        <legend>Features:</legend>
                        <div>
                            <label for="sauna">Sauna $2</label>
                            <input type="checkbox" name="features[]" id="sauna" value="sauna">
                        </div>
                        <div>
                            <label for="bicycle">Bicycle $2</label>
                            <input type="checkbox" name="features[]" id="bicycle" value="bicycle">
                        </div>
                        <div>
                            <label for="radio">Radio $2</label>
                            <input type="checkbox" name="features[]" id="radio" value="radio">
                        </div>
                    </fieldset>
                </div>

                <div class="formGroup">
                    <label for="transferCode">Your transferCode:</label>
                    <input type="password" name="transferCode" id="transferCode" required>
                </div>

                <div class="formGroup">
                    <button type="submit">Make a reservation!</button>
                    <button type="reset">Reset form</button>
                </div>


            </form>
        </section>
    </main>

    <!-- <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="calendar.js"></script> -->

    <!-- <script>
        config = {
            minDate: "2025-01-01",
            maxDate: "2025-01-31"
        }

        flatpickr("input[type=date]", config);
    </script> -->


</body>

</html>