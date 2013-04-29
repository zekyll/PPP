<?php
	/*
	 * Tulostaa virhe ym. viestit.
	 */
?>

<?php require_once 'session.php' ?>

<?php foreach ($session->errors as $e) { ?>
	<p class="error"><?= htmlspecialchars($e) ?></p>
<?php } ?>

<?php foreach ($session->messages as $m) { ?>
	<p class="msg"><?= htmlspecialchars($m) ?></p>
<?php } ?>

<?php
	$session->errors = array();
	$session->messages = array();
?>