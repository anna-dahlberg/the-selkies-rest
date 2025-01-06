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
            <img src="/assets/images/blackthornIsle.png" alt="">
        </section>

        <section class="roomContainer">
            <div class="titleContainer">
                <h3>A Room for Every Story</h3>
            </div>

            <?php foreach ($rooms as $room_id => $room_data): ?>
                <div class="room<?php echo $room_id; ?>">
                    <div class="roomText">
                        <h3 class="roomTitle"><?php echo htmlspecialchars($room_data['name']); ?></h3>
                        <p class="roomDescription"><?php echo htmlspecialchars($room_data['description']); ?></p>
                    </div>
                    <img src="<?php echo htmlspecialchars($room_data['image']); ?>" alt="<?php echo htmlspecialchars($room_data['name']); ?>" class="roomImg">
                    <div class="roomCalendar">
                        <?php echo $room_data['calendar']->show(); ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </section>

        <section class="featureContainer"></section>

        <section class="bookingContainer">
            <div class="bookingTitle"></div>
            <div class="formContainer">
                <div class="bookingForm"></div>
                <div class="bookingInfo">
                    <h3></h3>
                    <p></p>
                    <img src="" alt="">
                </div>
            </div>
        </section>

    </main>

</div>


<?php
require_once(__DIR__ . '/views/footer.php');
?>