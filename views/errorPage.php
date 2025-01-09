<?php

declare(strict_types=1);

session_start();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']); // Clear errors after displaying
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles/error.css">
    <link rel="stylesheet" href="assets/styles/global.css">
    <title>Error!</title>
</head>

<body>
    <div class="errorContainer">
        <h1>Oh no an error occurred!</h1>
        <?php foreach ($errors as $error): ?>
            <p class="errorMessage"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
        <p><a href="../index.php">Take me back</a></p>
    </div>
</body>

</html>