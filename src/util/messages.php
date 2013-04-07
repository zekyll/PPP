<?php require_once 'util.php' ?>

<?php foreach ($GLOBALS['errors'] as $e) { ?>
	<p class="error"><?= htmlspecialchars($e) ?></p>
<?php } ?>
