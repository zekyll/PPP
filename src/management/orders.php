<?php
	require_once dirname(__file__) . '/../util/authentication.php';
	require_login();
?>

<?php $title = 'Tilaukset'; require 'top.php'; ?>

<table border="1">
<tr>
<th>Tunnus</th>
<th>Nimi</th>
<th>Osoite</th>
<th>Toimitusaika</th>
<th>Toimitettu</th>
</tr>

<?php
	require_once dirname(__file__) . '/../util/queries.php';

	$orders = $queries->select_orders();

	foreach($orders as $o) {
		echo '<tr>';
		echo '<td><a href="order.php?id=', htmlspecialchars($o->id), '">',
				htmlspecialchars($o->id), '</a></td>';
		echo '<td>', htmlspecialchars($o->name), '</td>';
		echo '<td>', htmlspecialchars($o->address), '</td>';
		echo '<td>', datefmt($o->when_to_deliver), '</td>';
		echo '<td>', htmlspecialchars($o->when_delivered), '</td>';
		echo '</tr>';
	}
?>

</table>

<?php require 'bottom.php'; ?>