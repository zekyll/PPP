<?php $title = 'Tilaustiedot'; require 'top.php'; ?>

<?php
	require_once 'util/queries.php';
	require_once 'util/util.php';

	$id = array_key_exists('id', $_GET) ? $_GET['id'] : NULL;
	$order = $queries->select_order($id);

	require 'util/messages.php';

	$products = $queries->select_products();
?>

<?php if (!have_errors()) { ?>

<?php require 'util/orderinfo.php'; ?>

<form action="editorder.php<?= $id ? '?id=' . htmlspecialchars($id) : '' ?>" method="post">
	<input type="submit" value="Muokkaa tilausta">
	<input type="submit" name="delete" value="Peru tilaus">
</form>

<?php } ?>

<?php require 'bottom.php'; ?>