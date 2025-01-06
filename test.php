<?php
require_once __DIR__ . "/setup.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . "/booking.php";
    exit;
}

require_once __DIR__ . '/views/header.php';
?>
<main>

</main>


<?php
require_once(__DIR__ . '/views/footer.php');
?>