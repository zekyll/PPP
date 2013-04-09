<?php
	require_once dirname(__file__) . '/../util/authentication.php';
	require_login();
?>

<?php $title = 'Tuotteet'; require 'top.php'; ?>

<?php require dirname(__file__) . '/../util/messages.php'; ?>

<a href="product.php">Lisää uusi tuote</a>

<table border="1">
<tr>
<th>Nimi</th>
<th>Tuoteryhmä</th>
<th>Hinta</th>
<th>Kuvaus</th>
<th>Kuva</th>
</tr>

<?php
	require_once dirname(__file__) . '/../util/queries.php';

	$products = $queries->select_products();

	foreach($products as $p) {
		echo '<tr>';
		echo '<td><a href="product.php?id=', htmlspecialchars($p->id), '">',
				htmlspecialchars($p->name), '</a></td>';
		echo '<td>', htmlspecialchars($p->type), '</td>';
		echo '<td>', htmlspecialchars(pricefmt($p->price)), '</td>';
		echo '<td>', htmlspecialchars($p->description), '</td>';
		if($p->image_name != NULL)
			echo '<td><a href="../images/products/',
				htmlspecialchars($p->image_name), '"><img src="../images/products/',
				htmlspecialchars($p->image_name), '"></a></td>';
		else
			echo '<td></td>';
		echo '</tr>';
	}
?>

</table>

<?php require 'bottom.php'; ?>