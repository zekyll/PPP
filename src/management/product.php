<?php
	require_once dirname(__file__).'/../util/queries.php';
	require_once dirname(__file__).'/../util/util.php';

	$id = array_key_exists('id', $_GET) ? $_GET['id'] : NULL;
	$product = NULL;

	if (array_key_exists('update', $_POST) || array_key_exists('insert', $_POST)) {
		$product = new stdClass();
		$product->name = $_POST['name'];
		$product->type = $_POST['type'];
		//$product->price = str_replace(',', '.', $_POST["price"]);
		$product->price = $_POST['price'];
		$product->description = $_POST['description'];
		if ($_FILES['image']['error'] > 0)
			$product->image_name = NULL;
		else {
			$product->image_name = generateRandomId(16) . '.' .
					end(explode('.', $_FILES["image"]["name"]));
			move_uploaded_file($_FILES['image']['tmp_name'],
					'../images/products/' . $product->image_name);
		}

		if (strlen($product->name) == 0)
			add_error('Tuotteen nimi puuttuu.');
		if (strlen($product->type) == 0)
			add_error('Tuoteryhmä puuttuu.');
		if (!is_numeric($product->price))
			add_error('Hinta tulee antaa desimaalilukuna.');
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
			if(!have_errors())
				redirect('product.php?id=' . $id);
		}
	} elseif (array_key_exists('delete', $_POST)) {
		$queries->delete_product($id);
		if (!have_errors())
			redirect('products.php');
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

	require dirname(__file__).'/../util/messages.php';
?>

<form action="product.php<?= $id ? '?id=' . htmlspecialchars($id) : '' ?>" method="post"
	  enctype="multipart/form-data">
	<p>Nimi: <br>
	<input type="text" name="name" value="<?= htmlspecialchars($product->name) ?>"></p>

	<p>Tuoteryhmä: <br>
	<input type="text" name="type" value="<?= htmlspecialchars($product->type) ?>">	</p>

	<p>Hinta: <br>
	<input type="text" name="price" value="<?= htmlspecialchars($product->price) ?>"></p>

	<p>Kuvaus:<br>
	<textarea name="description"><?= htmlspecialchars($product->description) ?></textarea></p>

	<p>Kuva: <br>
	<input type="file" name="image"></p>
	<?php
		if($id && $product->image_name)
			echo '<p><img src="../images/products/', htmlspecialchars($product->image_name), '"></p>';
	?>

	<?php
		if($id) {
			echo '<input type="submit" name="update" value="Päivitä tiedot">';
			echo '<input type="submit" name="delete" value="Poista tuote">';
		} else
			echo '<input type="submit" name="insert" value="Lisää tuote">';
	?>
</form>

<?php require 'bottom.php'; ?>