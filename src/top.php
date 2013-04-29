<?php
	/*
	 * HTML-koodi, joka liitetään jokaisen sivun yläosaan. Näyttää valikon,
	 * josta pääsee eri alasivuille.
	 */
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href="css/style.css" rel="stylesheet">
	<title><?= 'Pirkon Pizza Palvelu - ', htmlspecialchars($title) ?></title>
</head>

<body>
<a href=".">Etusivu</a>
<a href="products.php">Hinnasto</a>
<a href="order.php">Tilaus</a>
<h1><?= htmlspecialchars($title) ?></h1>
