<?php
	require_once dirname(__file__) . '/../util/authentication.php';
	require_login();
?>

<?php $title = 'Etusivu'; require 'top.php'; ?>

<?php require dirname(__file__) . '/../util/messages.php'; ?>

<p><a href="changepswd.php">Vaihda salasanaa</a></p>

<?php require 'bottom.php'; ?>