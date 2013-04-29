<?php
	/*
	 * Uuden tuotteen lisäys ja tuotetietojen muokkaus.
	 *
	 * Ilman parametreja näytetään tyhjä lomake uuden tuotteen lisäämiseksi.
	 *
	 * Jos $_GET-parametrina annettu "id", näytetään muokkauslomake kyseiselle
	 * tuotteelle.
	 *
	 * Jos $_POST-parametrina annettu "insert" tai "update", tallennetaan
	 * lomakkeen tiedot tietokantaan (uusi tuote tai aiemman muokkaus).
	 *
	 * Jos $_POST-parametrina annettu "delete", poistetaan tuote kannasta.
	 */

	require_once dirname(__file__) . '/../util/queries.php';
	require_once dirname(__file__) . '/../util/util.php';
	require_once dirname(__file__) . '/../util/authentication.php';

	require_login();

	$id = array_key_exists('id', $_GET) ? $_GET['id'] : NULL;
	$product = NULL;

	if (array_key_exists('update', $_POST) || array_key_exists('insert', $_POST)) {
		$product = new stdClass();
		$product->name = $_POST['name'];
		$product->type = $_POST['type'];
		$product->price = str_replace(',', '.', $_POST["price"]);
		$product->description = $_POST['description'];
		if (strlen($_FILES['image']['name']) != 0) {
			if ($_FILES['image']['error'] > 0) {
				$product->image_name = NULL;
				add_error('Kuvatiedoston siirrossa tapahtui virhe.');
			} else if ($_FILES['image']['size'] > 102400) {
				$product->image_name = NULL;
				add_error('Kuvatiedoston koko voi olla enintään 100 KiB.');
			} else {
				$ext = strtolower(end(explode('.', $_FILES["image"]["name"])));
				if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'bmp'
						&& $ext != 'gif' && $ext != 'png') {
					add_error('Kuvatiedoston tyyppi tulee olla JPG, BMP, GIF tai PNG.');
				} else {
					$product->image_name = generateRandomId(16) . '.' . $ext;
					move_uploaded_file($_FILES['image']['tmp_name'],
							'../images/products/' . $product->image_name);
				}
			}
		}

		if (strlen($product->name) == 0)
			add_error('Tuotteen nimi puuttuu.');
		if (strlen($product->type) == 0)
			add_error('Tuoteryhmä puuttuu.');
		if (!is_numeric($product->price))
			add_error('Hinta ei ole kelvollinen desimaaliluku.');
		if (round($product->price * 100) >= 100000000)
			add_error('Hinnan tulee olla pienempi kuin 1000000.');

		if (!have_errors()) {
			if (array_key_exists('update', $_POST)) {
				$product->id = $id;
				$queries->update_product($product);
			} else {
				$queries->insert_product($product);
				$id = $product->id;
			}
			if(!have_errors()) {
				add_msg('Tuotetiedot tallennettu');
				redirect('product.php?id=' . $id);
			}
		}
	} elseif (array_key_exists('delete', $_POST)) {
		$queries->delete_product($id);
		if (!have_errors()) {
			add_msg('Tuote poistettu');
			redirect('products.php');
		}
	}
?>

<?php $title = 'Tuotetiedot'; require 'top.php'; ?>

<?php
	if ($id && $product == NULL) {
		$product = $queries->select_product($id);
	} else if ($product == NULL) {
		$product = new stdClass();
		$product->name = '';
		$product->type = '';
		$product->price = '';
		$product->description = '';
		$product->image_name = NULL;
	} else {
		$product->image_name = $queries->select_product($id)->image_name;
	}

	require dirname(__file__) . '/../util/messages.php';
?>

<form action="product.php<?= $id ? '?id=' . htmlspecialchars($id) : '' ?>" method="post"
	  enctype="multipart/form-data">
	<p>Nimi: <br>
	<input type="text" name="name" value="<?= htmlspecialchars($product->name) ?>"></p>

	<p>Tuoteryhmä: <br>
	<input type="text" name="type" value="<?= htmlspecialchars($product->type) ?>">	</p>

	<p>Hinta: <br>
	<input type="text" name="price" value="<?= htmlspecialchars(pricefmt($product->price)) ?>"></p>

	<p>Kuvaus:<br>
	<textarea rows="3" cols="40" name="description"><?= htmlspecialchars($product->description) ?></textarea></p>

	<p>Kuva: <br>
	<input type="hidden" name="MAX_FILE_SIZE" value="102400" />
	<input type="file" name="image"></p>
	<?php
		if($id && $product->image_name)
			echo '<p><img src="../images/products/', htmlspecialchars($product->image_name), '"></p>';
	?>

	<?php
		if($id) {
			echo '<input type="submit" name="update" value="Tallenna tiedot">';
			echo '<input type="submit" name="delete" value="Poista tuote">';
		} else
			echo '<input type="submit" name="insert" value="Lisää tuote">';
	?>
</form>

<?php require 'bottom.php'; ?>