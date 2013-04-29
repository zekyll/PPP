<?php
	/*
	 * Hinnasto/tuotelista. Hakee tuotelistan tietokannasta ja näyttää
	 * ne ryhmiteltynä tuoteryhmän mukaan.
	 */
?>

<?php $title = 'Hinnasto'; require 'top.php'; ?>

<?php
	require_once 'util/queries.php';

	// Haetaan tuoteryhmien nimet tietokannasta.
	$types = $queries->select_types();

	// Linkit hinnaston eri tuoteryhmiin.
	$i = 0;
	foreach($types as $t) {
		echo '<p><a href="#type', $i, '">', htmlspecialchars($t), "</a></p>\n";
		++$i;
	}

	// Haetaan tuotelista tietokannasta.
	$products = $queries->select_products();

	// Lista kaikista tuotteista ryhmiteltynä tuoteryhmän mukaan. Haku palauttaa
	// tuotteet valmiiksi oikeassa järjestyksessä, joten tulee ainoastaa
	// lisätä tuoteryhmien otsikot oikeisiin väleihin.
	$type = "";
	$i = 0;
	foreach($products as $p) {
		// Tuoteryhmän otsikko.
		if ($p->type != $type) {
			$type = $p->type;
			echo '<h2 id="type', $i, '">', htmlspecialchars($type), "</h2>\n";
			++$i;
		}

		// Tuotteen tiedot.
		echo '<div class="product">';
		echo '<div class="productText">';

		echo '<h4>', htmlspecialchars($p->name), ' - ', htmlspecialchars(pricefmt($p->price)), '€</h4>';
		echo '<p>';

		if ($p->description != NULL)
			echo htmlspecialchars($p->description);
		echo "</p></div>\n";
		if ($p->image_name != NULL)
			echo '<a href="images/products/' ,
				htmlspecialchars($p->image_name), '"><img alt="tuotekuva" class="productImg" src="images/products/' .
				htmlspecialchars($p->image_name), '"></a>';
		echo '</div>';
	}
?>

<?php require 'bottom.php'; ?>