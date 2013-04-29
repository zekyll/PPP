<?php
	/*
	 * Tilauksen tarkastelu ja toimitustietojen kirjaus. Tilaustunnus
	 * annetaan "id"-parametrissa.
	 *
	 * Ilman muita parametreja näytetään tilauksen tiedot ja aikaisemmat
	 * toimitustiedot.
	 *
	 * Jos $_POST-parametrina on annettu "update", tallennetaan toimitustiedot.
	 *
	 * Jos $_POST-parametrina on annettu "delete", poistetaan tilaus.
	 */

	require_once dirname(__file__) . '/../config.php';
	require_once dirname(__file__) . '/../util/queries.php';
	require_once dirname(__file__) . '/../util/util.php';
	require_once dirname(__file__) . '/../util/session.php';
	require_once dirname(__file__) . '/../util/authentication.php';

	require_login();

	$id = array_key_exists('id', $_GET) ? $_GET['id'] : NULL;
	$order = NULL;

	if (array_key_exists('update', $_POST)) {
		$order = new stdClass();
		if($_POST['when_delivered']) {
			$order->when_delivered = date_create_from_format('j.n.Y H:i', $_POST['when_delivered']);
			if (!$order->when_delivered) {
				add_error('Toimitusajankohta ei ole kelvollinen päivämäärä/aika.');
				$order->when_delivered = $_POST['when_delivered'];
			}
		}
		if($_POST['price_on_delivery']) {
			$order->price_on_delivery = str_replace(',', '.', $_POST['price_on_delivery']);
			if (!is_numeric($order->price_on_delivery)) {
				add_error('Lopullinen hinta ei ole kelvollinen desimaaliluku.');
				$order->price_on_delivery = $_POST['price_on_delivery'];
			}
		}
		$order->notes = $_POST['notes'];
		$order->prevent = array_key_exists('prevent', $_POST);

		if (!have_errors()) {
			$order->id = $id;
			$queries->update_delivery_info($order);
			if(!have_errors()) {
				add_msg('Toimitustiedot tallennettu');
				redirect('order.php?id=' . $id);
			}
		}
	} elseif (array_key_exists('delete', $_POST)) {
		$queries->delete_order($id);
		if(!have_errors()) {
			add_msg('Tilaus poistettu');
			redirect('orders.php');
		}
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

	require dirname(__file__) . '/../util/messages.php';

	$products = $queries->select_products();
?>

<?php require dirname(__file__) . '/../util/orderinfo.php'; ?>

<h2>Toimituksen kirjaus</h2>

<form action="order.php<?= $id ? '?id=' . htmlspecialchars($id) : '' ?>" method="post">
	<p>Toimitusaika: <br>
	<input type="text" name="when_delivered" value="<?= htmlspecialchars(datefmt($order->when_delivered)) ?>"></p>

	<p>Lopullinen hinta:<br>
	<input type="text" name="price_on_delivery" value="<?= htmlspecialchars(pricefmt($order->price_on_delivery)) ?>"></p>

	<p>Lisätiedot:<br>
	<textarea rows="3" cols="40" name="notes"><?= htmlspecialchars($order->notes) ?></textarea></p>

	<p><input type="checkbox" id="prevent" name="prevent" value="1"<?= $order->prevent ? ' checked' : '' ?>>
	<label for="prevent">Estä tilaukset tästä osoitteesta</label></p>

	<input type="submit" name="update" value="Tallenna">
	<input type="submit" name="delete" value="Poista tilaus">
</form>

<?php require 'bottom.php'; ?>