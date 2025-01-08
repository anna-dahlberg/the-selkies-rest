<?php
require_once __DIR__ . "/setup.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . "/booking.php";
    exit;
}

require_once __DIR__ . '/views/header.php';
?>

<div class="mainContainer">

    <main>

        <section class="hotelContainer">
            <div class="hotelInfo">
                <h2>Welcome to the Selkie’s Rest<br>Your Island Sanctuary</h2>
                <p>Nestled on the enchanting shores of Blackthorn Isle, The Selkie's Rest is a haven where tranquility meets adventure. Our charming retreat offers an unforgettable blend of comfort, warmth, and natural beauty. With stunning ocean views, modern amenities, and a touch of rustic charm, every moment here is designed to inspire and rejuvenate. <br> <br>

                    Whether you’re savoring local delicacies in our dining room, unwinding in the cozy embrace of your room, or exploring the island’s hidden treasures, you’ll find that The Selkie’s Rest isn’t just a place to stay—it’s a place to belong.</p>
            </div>
            <img src="assets/images/blackthornIsle.png" alt="">
        </section>

        <section class="roomContainer">
            <div class="titleContainer">
                <h3>A Room for Every Story</h3>
            </div>

            <?php foreach ($rooms as $room_id => $room_data): ?>
                <div class="room<?php echo $room_id; ?> roomInfoContainer">
                    <div class="roomText">
                        <h3 class="roomTitle"><?php echo htmlspecialchars($room_data['name']); ?></h3>
                        <p class="roomDescription"><?php echo htmlspecialchars($room_data['description1']); ?> <br><br> <?php echo htmlspecialchars($room_data['description2']); ?></p>
                    </div>
                    <img src="<?php echo htmlspecialchars($room_data['image']); ?>" alt="<?php echo htmlspecialchars($room_data['name']); ?>" class="roomImg">
                    <div class="roomCalendar">
                        <?php echo $room_data['calendar']->show(); ?>
                    </div>
                </div>
            <?php endforeach; ?>


        </section>

        <section class="featureContainer">
            <div class="featureInfo">
                <h3 class="featureTitle">An unforgettable stay awaits -</h3>
                <p class="featureText">At The Selkie's Rest, we take pride in offering a variety of features that make your stay truly unique and memorable. From the soothing warmth of our <em>sauna</em> to the thrilling adventures of a <em>Loch Monster Hunt</em>, there's something here for every traveler. Whether you're cycling through the island's scenic trails on our bicycles or indulging in the rich flavors of the <em>Scotch Whiskey Experience</em>, each moment is crafted to inspire and delight.<br><br>

                    For those seeking a touch of whimsy, join our <em>Highland Cow Cuddles</em> or explore the local wildlife on a <em>Selkie Safari</em>. And if you're in the mood for a relaxing evening, tune into a vintage <em>radio</em> as you unwind in the charm of your room.<br><br>

                    Discover the perfect blend of relaxation, adventure, and authentic Scottish charm—all in one magical destination.</p>
            </div>
            <img src="assets/images/cow.png" alt="a highland cow">
        </section>

        <section class="bookingSection">
            <h3 class="bookingTitle">Book your stay now!</h3>

            <div class="bookingContainer">

                <div class="bookingForm" id="bookYourStay">

                    <div class="formContainer">
                        <form action="booking.php" method="POST" class="form">
                            <div class="formGroup formGroupFlex">
                                <label for="name">Your Name:</label>
                                <input type="text" name="name" id="name" required>
                            </div>


                            <div class="formGroup formGroupFlex">
                                <label for="email">Your E-mail:</label>
                                <input type="email" id="email" name="email" placeholder="you@example.com" required>
                            </div>

                            <div class="formGroup formGroupFlex">
                                <label for="arrivalDate">Arrival Date:</label>
                                <input type="date" id="arrivalDate" name="arrivalDate" min="2025-01-01" max="2025-01-31" required>
                            </div>


                            <div class="formGroup formGroupFlex">
                                <label for="departureDate">Departure Date:</label>
                                <input type="date" id="departureDate" name="departureDate" min="2025-01-01" max="2025-01-31" required>
                            </div>


                            <div class="formGroup formGroupFlex">
                                <label for="roomType">Room Type:</label>
                                <select id="roomType" name="roomType" required>
                                    <option value="budget">The Nook $3/day</option>
                                    <option value="standard">The Refuge $6/day</option>
                                    <option value="luxury">The Haven $9/day</option>
                                </select>
                            </div>


                            <div class="formGroup formGroupFlex">
                                <fieldset>
                                    <legend>Features:</legend>
                                    <div class="checkboxContainer">
                                        <input type="checkbox" name="features[]" id="sauna" value="sauna">
                                        <label for="sauna" id="fw400">Sauna $3</label>
                                    </div>
                                    <div class="checkboxContainer">
                                        <input type="checkbox" name="features[]" id="bicycle" value="bicycle">
                                        <label for="bicycle" id="fw400">Bicycle $3</label>
                                    </div>
                                    <div class="checkboxContainer">
                                        <input type="checkbox" name="features[]" id="radio" value="radio">
                                        <label for="radio" id="fw400">Radio $3</label>
                                    </div>

                                    <div class="checkboxContainer">
                                        <input type="checkbox" name="features[]" id="whiskeyExperience" value="whiskeyExperience">
                                        <label for="whiskeyExperience" id="fw400">Scoth Whiskey Experience $3</label>
                                    </div>

                                    <div class="checkboxContainer">
                                        <input type="checkbox" name="features[]" id="lochMonsterHunt" value="lochMonsterHunt">
                                        <label for="lochMonsterHunt" id="fw400">Loch Monster Hunt $3</label>
                                    </div>

                                    <div class="checkboxContainer">
                                        <input type="checkbox" name="features[]" id="highlandCowCuddles" value="highlandCowCuddles">
                                        <label for="highlandCowCuddles" id="fw400">Highland Cow Cuddles $3</label>
                                    </div>

                                </fieldset>
                            </div>

                            <div class="totalCost">
                                <span>Total cost: </span>
                                <span id="totalCost"> </span>

                            </div>

                            <div class="formGroup formGroupFlex">
                                <label for="transferCode">Your transferCode:</label>
                                <input type="password" name="transferCode" id="transferCode" required>
                            </div>

                            <div class="formGroup buttonContainer">
                                <button type="submit" class="formButton">Book now!</button>
                                <button type="reset" class="formButton">Reset form</button>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="bookingInfo">
                    <h3>Relax a little longer,<br>save a little extra</h3>
                    <p>Enjoy a little extra with us. When you stay three days or more at Selkies Rest, we’ll offer you $2 off your total booking. It’s our way of saying thank you for choosing to spend more time with us and experiencing the beauty of the Scottish coast.</p>
                    <img src="assets/images/selkie.jpg" alt="">
                </div>

            </div>

        </section>

    </main>

</div>

<script src="assets/scripts/script.js"></script>


<?php
require_once(__DIR__ . '/views/footer.php');
?>