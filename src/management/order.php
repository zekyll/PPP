<?php
	require_once dirname(__file__).'/../config.php';
	require_once dirname(__file__).'/../util/queries.php';
	require_once dirname(__file__).'/../util/util.php';

	$id = array_key_exists('id', $_GET) ? $_GET['id'] : NULL;
	$order = NULL;

	if (array_key_exists('update', $_POST)) {
		$order = new stdClass();
		$order->when_delivered = $_POST['when_delivered'];
		$order->price_on_delivery = $_POST['price_on_delivery'];
		$order->notes = $_POST['notes'];
		$order->prevent = array_key_exists('prevent', $_POST);

		//if (...)
		//	add_error('...');
		//TODO

		if (!have_errors()) {
			$order->id = $id;
			$queries->update_delivery_info($order);
			if(!have_errors())
				redirect('order.php?id=' . $id);
		}
	} elseif (array_key_exists('delete', $_POST)) {
		$queries->delete_order($id);
		if(!have_errors())
			redirect('orders.php');
	}
?>

<?php $title = 'Tilaustiedot'; require 'top.php'; ?>

<?php
	if ($id && $order == NULL) {
		$order = $queries->select_order($id);
	} else if ($order == NULL) {
		$order = new stdClass();
		$order->when_delivered = '';
		$order->price_on_delivery = '';
		$order->notes = '';
		$order->prevent = '';
	}

	require dirname(__file__).'/../util/messages.php';

	$products = $queries->select_products();
?>

<?php require dirname(__file__).'/../util/orderinfo.php'; ?>

<h2>Toimituksen kirjaus</h2>

<form action="order.php<?= $id ? '?id=' . htmlspecialchars($id) : '' ?>" method="post">
	<p>Toimitusaika: <br>
	<input type="text" name="when_delivered" value="<?= htmlspecialchars($order->when_delivered) ?>"></p>

	<p>Lopullinen hinta:<br>
	<input type="text" name="price_on_delivery" value="<?= htmlspecialchars($order->price_on_delivery) ?>"></p>

	<p>Lisätiedot:<br>
	<textarea name="notes"><?= htmlspecialchars($order->notes) ?></textarea></p>

	<p><input type="checkbox" id="prevent" name="prevent" value="1"<?= $order->prevent ? ' checked' : '' ?>>
	<label for="prevent">Estä tilaukset tästä osoitteesta</label></p>

	<input type="submit" name="update" value="Tallenna">
	<input type="submit" name="delete" value="Poista tilaus">
</form>

<?php require 'bottom.php'; ?>