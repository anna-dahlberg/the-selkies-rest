<?php

declare(strict_types=1);

require(__DIR__ . '/vendor/autoload.php')
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/styles/styles.css">
    <link rel="stylesheet" href="https://use.typekit.net/ucb3kmg.css">
    <title>The Selkies Rest</title>
</head>

<body>

    <header>

        <div class="hero">
            <h1>The Selkies Rest</h1>
        </div>

    </header>

    <main>
        <section class="booking-form">
            <h2>Book Your Stay</h2>

            <form action="booking.php" method="POST">


                <div class="form-group">
                    <label for="name">Your Name:</label>
                    <input type="text" name="name" id="name" required>
                </div>


                <div class="form-group">
                    <label for="email">Your E-mail:</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required>
                </div>

                <div class="form-group">
                    <label for="arrival-date">Arrival Date:</label>
                    <input type="date" id="arrival-date" name="arrival_date" min="2025-01-01" max="2025-01-31" required>
                </div>


                <div class="form-group">
                    <label for="departure-date">Departure Date:</label>
                    <input type="date" id="departure-date" name="departure_date" min="2025-01-01" max="2025-01-31" required>
                </div>


                <div class="form-group">
                    <label for="room-type">Room Type:</label>
                    <select id="room-type" name="room_type" required>
                        <option value="budget">Budget</option>
                        <option value="standard">Standard</option>
                        <option value="luxury">Luxury</option>
                    </select>
                </div>


                <div class="form-group">
                    <fieldset>
                        <legend>Features:</legend>
                        <div>
                            <label for="sauna">Sauna $2</label>
                            <input type="checkbox" name="sauna" id="sauna">
                        </div>

                        <div>
                            <label for="bicycle">Bicycle $2</label>
                            <input type="checkbox" name="bicycle" id="bicycle">
                        </div>

                        <div>
                            <label for="radio">Radio $2</label>
                            <input type="checkbox" name="radio" id="radio">
                        </div>

                    </fieldset>
                </div>

                <div class="form-group">
                    <label for="transferCode">Your transferCode:</label>
                    <input type="password" name="transferCode" id="transferCode" required>
                </div>

                <div class="form-group">
                    <button type="submit">Make a reservation!</button>
                    <button type="reset">Reset form</button>
                </div>


            </form>
        </section>
    </main>


</body>

</html>