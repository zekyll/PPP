<?php
	require_once 'config.php';
	require_once 'util/queries.php';
	require_once 'util/util.php';

	$id = array_key_exists('id', $_GET) ? $_GET['id'] : NULL;
	$order = NULL;

	if (array_key_exists('update', $_POST) || array_key_exists('insert', $_POST)) {
		$order = new stdClass();
		$order->name = $_POST['name'];
		$order->address = $_POST['address'];
		$order->contact = $_POST['contact'];
		$order->when_to_deliver = date_create_from_format('j.n.Y H:i', $_POST['when_to_deliver']);
		if (!$order->when_to_deliver) {
			add_error('Toimitusajankohdan formaatti on väärin.');
			$order->when_to_deliver = $_POST['when_to_deliver'];
		}
		$order->additional_info = $_POST['additional_info'];

		$order->items = array();
		for($i = 0; $i < $MAX_ORDER_ITEMS; ++$i) {
			if (empty($_POST['product' . $i]))
				continue;

			$order->items[] = $item = new stdClass();
			$item->product = $_POST['product' . $i];
			$item->count = $_POST['count' . $i];
			if (!isPositiveInteger($item->count))
				add_error('Lukumäärän tulee olla positiivinen kokonaisluku.');

			// Lisukkeet.
			$item->extras = array();
			for($i2 = 0; $i2 < $MAX_EXTRAS; ++$i2) {
				if (empty($_POST['extra' . $i . '_' . $i2]))
					continue;
				$item->extras[] = $extra = new stdClass();
				$extra->product = $_POST['extra' . $i . '_' . $i2];
			}
		}

		if (empty($order->name))
			add_error('Nimi on vaadittava tieto.');
		if (empty($order->address))
			add_error('Toimitusosoite on vaadittava tieto.');
		if (empty($order->contact))
			add_error('Puhelinnumero tai sähköpostiosoite on vaadittava tieto.');
		if (empty($order->when_to_deliver))
			add_error('Toimitusajankohta on vaadittava tieto.');
		if (empty($order->items))
			add_error('Tilauksessa tulee olla vähintään yksi tuote.');

		//TODO

		if (!have_errors()) {
			if (array_key_exists('update', $_POST)) {
				$order->id = $id;
				$queries->update_order($order);
			} else {
				$queries->insert_order($order);
				$id = $order->id;
			}
			if(!have_errors())
				header('Location: vieworder.php?id=' . $id);
		}
	} elseif (array_key_exists('delete', $_POST)) {
		$queries->delete_order($id);
		if(!have_errors())
			header('Location: products.php');
	}
?>

<?php $title = 'Tilaus'; require 'top.php'; ?>

<?php
	if ($id && $order == NULL) {
		$order = $queries->select_order($id);
	} else if ($order == NULL) {
		$order = new stdClass();
		$order->name = '';
		$order->address = '';
		$order->contact = '';
		$order->when_to_deliver = '';
		$order->additional_info = NULL;
		$order->items = array();
	}

	require 'util/messages.php';

	$products = $queries->select_products();

// Luodaan vaihtoehdot tilattavien ruokien/lisukkeiden valitsemiseen.
function generate_options($products, $order_item_idx, $extra_idx, $selected)
{
	echo "<option value=\"\">---</option>\n";
	$type = '';
	foreach($products as $p) {
		// Tuoteryhmän otsikko.
		if ($extra_idx === NULL && $p->type != $type && $p->type != 'Lisätäytteet') {
			$type = $p->type;
			$tmp = htmlspecialchars(strtoupper($p->type));
			echo "<option disabled>--- $tmp ---</option>\n";
		}

		// Tuotteen nimi.
		if ($extra_idx === NULL xor $p->type == 'Lisätäytteet') {
			$selattr = $p->id == $selected ? ' selected' : '';
			echo '<option value="', htmlspecialchars($p->id), '"', $selattr, '>',
					$p->name, "</option>\n";
		}
	}
}

function generate_order_items($products, $order)
{
	for($i = 0; $i < $GLOBALS['MAX_ORDER_ITEMS']; ++$i) {
		echo '<tr>';
		echo "<td><select name=\"product$i\">";
		$sel = array_key_exists($i, $order->items) ? $order->items[$i]->product : NULL;
		generate_options($products, $i, NULL, $sel);
		echo '</select></td>';
		$val = array_key_exists($i, $order->items) ? $order->items[$i]->count : '1';
		echo "<td><input size=\"10\" type=\"text\" name=\"count$i\" value=\"$val\"></td>";
		echo '<td>';
		for($i2 = 0; $i2 < $GLOBALS['MAX_EXTRAS']; ++$i2) {
			echo "<select name=\"extra{$i}_{$i2}\">";
			$sel = array_key_exists($i, $order->items) &&
					array_key_exists($i2, $order->items[$i]->extras) ?
					$order->items[$i]->extras[$i2]->product : NULL;
			generate_options($products, $i, $i2, $sel);
			echo "</select>\n";
		}
		echo "</td></tr>\n";
	}
}
?>

<form action="editorder.php<?= $id ? '?id=' . htmlspecialchars($id) : '' ?>" method="post">
	<p>Nimi: <br>
	<input type="text" size="30" name="name" value="<?= htmlspecialchars($order->name) ?>"></p>

	<p>Osoite: <br>
	<input type="text" size="30" name="address" value="<?= htmlspecialchars($order->address) ?>">	</p>

	<p>Puhelinnumero tai sähköpostiosoite: <br>
	<input type="text" size="30" name="contact" value="<?= htmlspecialchars($order->contact) ?>"></p>

	<p>Toimituksen ajankohta (pp.kk.vvvv hh:mm):<br>
	<input type="text" size="30" name="when_to_deliver" value="<?= datefmt($order->when_to_deliver) ?>"></p>

	<p>Lisätiedot:<br>
	<textarea rows="3" cols="40" name="additional_info"><?= htmlspecialchars($order->additional_info) ?></textarea></p>

	<table id="ordered_products">
		<tr>
			<td>Ruoka</td>
			<td>Lukumäärä</td>
			<td>Lisätäytteet (vain pizzoille)</td>
		</tr>
		<?php generate_order_items($products, $order) ?>
	</table>

	<?php
		if($id) {
			echo '<input type="submit" name="update" value="Tallenna tiedot">';
			echo '<input type="submit" name="delete" value="Peru tilaus">';
		} else
			echo '<input type="submit" name="insert" value="Tee tilaus">';
	?>
</form>

<?php require 'bottom.php'; ?>