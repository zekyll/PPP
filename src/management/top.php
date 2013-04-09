<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href="../css/style.css" rel="stylesheet">
	<title><?= htmlspecialchars($title) ?></title>
</head>

<body>
<a href=".">Etusivu</a>
<a href="products.php">Tuotteet</a>
<a href="orders.php">Tilaukset</a>
<?php if($session->username) { ?>
	<a href="login.php?logout">Kirjaudu ulos</a>
<?php } ?>
<h1><?= htmlspecialchars($title) ?></h1>
