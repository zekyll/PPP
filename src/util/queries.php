<?php
/*
 * Tietokantaoperaatiot.
 */

require_once dirname(__file__) . '/../config.php';
require_once 'util.php';
require_once 'session.php';

class Queries {

	private $_pdo;

	public function __construct($pdo) {
		$this->_pdo = $pdo;
	}

	// Lisää uuden tuotteen.
	public function insert_product($p) {
		$q = $this->_pdo->prepare('INSERT INTO product (name, type,'
				. ' price, description, image_name) VALUES (?, ?, ?, ?, ?)'
				. ' RETURNING id');
		try {
			$q->execute(array($p->name, $p->type, $p->price,
					$p->description, $p->image_name));
			$p->id = $q->fetchObject()->id;
		} catch (PDOException $e) {
			add_error('Tuotteen lisäys epäonnistui.');
			$p->id = NULL;
		}
	}

	// Tallentaa tuotteen tiedot.
	public function update_product($p) {
		try {
			if($p->image_name) {
				$q = $this->_pdo->prepare('UPDATE product SET name=?, type=?, price=?, '
						. 'description=?, image_name=? WHERE id = ?');
				$q->execute(array($p->name, $p->type, $p->price,
						$p->description, $p->image_name, $p->id));
			} else {
				$q = $this->_pdo->prepare('UPDATE product SET name=?, type=?, price=?, '
						. 'description=? WHERE id = ?');
				$q->execute(array($p->name, $p->type, $p->price,
						$p->description, $p->id));
			}
		} catch (PDOException $e) {
			add_error('Tuotetietojen muokkaus epäonnistui.');
		}
	}

	// Hakee tuotteen tiedot.
	public function select_product($id) {
		try {
			$q = $this->_pdo->prepare('SELECT * FROM product WHERE id = ?');
			$q->execute(array($id));
		} catch (PDOException $e) {
			add_error('Tuotetietojen haku epäonnistui.');
		}
		return $q->fetchObject();
	}

	// Hakee kaikki tuotteet järjestettynä ja ryhmiteltynä siten, että
	// merkittävimmät tuoteryhmät tulevat ensiksi.
	public function select_products() {
		try {
			$q = $this->_pdo->prepare('SELECT id, name, product.type, price, '
				. 'description, image_name FROM product, (SELECT TYPE, '
				. '-sum(price) as srt FROM product GROUP BY type ORDER BY srt) '
				. 'typepri WHERE product.type = typepri.type ORDER BY srt, price, name');
			$q->execute();

			$product_list = array();
			while($product = $q->fetchObject())
				$product_list[] = $product;
		} catch (PDOException $e) {
			add_error('Tuotetietojen haku epäonnistui.');
			$product_list = NULL;
		}
		return $product_list;
	}

	// Poistaa tuotteen annetulla tuotetunnuksella.
	public function delete_product($id) {
		try {
			$q = $this->_pdo->prepare('DELETE FROM product WHERE id = ?');
			$q->execute(array($id));
		} catch (PDOException $e) {
			if ($e->getCode() == 23503) // FOREIGN_KEY_VIOLATION
				add_error('Tuotetta ei voi poistaa koska sitä käyttäviä tilauksia '
					. 'on yhä voimassa.');
			else
				add_error('Tuotteen poisto epäonnistui.');
		}
	}

	// Hakee tuoteryhmät.
	public function select_types() {
		try {
			$q = $this->_pdo->prepare('SELECT type, sum(price) as srt FROM product'
				. ' GROUP BY type ORDER BY srt DESC;');
			$q->execute();

			$type_list = array();
			while($product = $q->fetchObject())
				$type_list[] = $product->type;
		} catch (PDOException $e) {
			add_error('Tuotetietojen haku epäonnistui.');
			$type_list = NULL;
		}

		return $type_list;
	}

	// Apufunktio tilauksen lisäämistä varten.
	private function insert_order_with_id($order) {
		// Tarkistetaan onko osoite estetty.
		$q = $this->_pdo->prepare('SELECT id FROM customer_order WHERE address = ? '
				. 'and prevent = true');
		$q->execute(array($order->address));
		if($q->fetchObject())
			throw new Exception('Tilausten tekeminen osoitteeseen on estetty.');

		// Lasketaan tilauksen hinta.
		$order->price = 0;
		$q = $this->_pdo->prepare('SELECT price, type FROM product WHERE id = ?');
		foreach ($order->items as $item) {
			$item->price = 0;
			$q->execute(array($item->product));
			$row = $q->fetchObject();
			$item->price = $row->price;

			if (!empty($item->extras) && $row->type != "Pizzat")
				throw new Exception('Lisätäytteitä voi valita ainoastaan pizzojen kanssa.');

			foreach ($item->extras as $extra) {
				$q->execute(array($extra->product));
				$extra->price = $q->fetchObject()->price;
				$item->price += $item->count * $extra->price;
			}

			$order->price += $item->price;
		}

		// Lisätään tilaus-tietue kantaan.
		$q = $this->_pdo->prepare('INSERT INTO customer_order (id, price, name, '
				. 'address, contact, when_to_deliver, additional_info) '
				. 'VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING id');
		$q->execute(array($order->id, $order->price, $order->name,
				$order->address, $order->contact,
				date_format($order->when_to_deliver, 'Y-m-d H:i'),
				$order->additional_info));
		$order->id = $q->fetchObject()->id;

		// Lisätään tilaukseen kuuluvat tuotteet ja lisukkeet.
		$q = $this->_pdo->prepare('INSERT INTO order_item (customer_order, '
				. 'product, price, count) VALUES (?, ?, ?, ?) RETURNING id');
		$q2 = $this->_pdo->prepare('INSERT INTO order_item_extra (order_item, '
				. 'product, price) VALUES (?, ?, ?)');
		foreach ($order->items as $item) {
			$q->execute(array($order->id, $item->product, $item->price, $item->count));
			$item->id = $q->fetchObject()->id;
			foreach ($item->extras as $extra)
				$q2->execute(array($item->id, $extra->product, $extra->price));
		}
	}

	// Lisää uuden tilauksen.
	public function insert_order($order) {
		// Generoidaan tilaukselle tunniste.
		$order->id = generateRandomId(10);

		try {
			$this->_pdo->beginTransaction();

			$this->insert_order_with_id($order);

			$this->_pdo->commit();
		} catch (PDOException $e) {
			try { $this->_pdo->rollBack(); } catch (PDOException $e2) { }
			$order->id = NULL;
			if($e->getCode() == 22007) //INVALID DATETIME FORMAT
				add_error('Toimitusajankohdan muoto on väärä.');
			else
				add_error('Tilauksen lisäys epäonnistui.');
		} catch (Exception $e) {
			try { $this->_pdo->rollBack(); } catch (PDOException $e2) { }
			$order->id = NULL;
			add_error($e->getMessage());
		}
	}

	// Hakee tilauksen annetulla tunnuksella.
	public function select_order($id) {
		try {
			$this->_pdo->beginTransaction();

			// Haetaan tilaus-tietue.
			$q = $this->_pdo->prepare('SELECT * FROM customer_order WHERE id = ?');
			$q->execute(array($id));
			$order = $q->fetchObject();
			if (!$order) {
				add_error('Tilausta ei löytynyt.');
				$this->_pdo->rollBack();
				return FALSE;
			}

			// Haetaan tilaukseen kuuluvat tuotteet.
			$order_item_map = array();
			$order->items = array();
			$q = $this->_pdo->prepare('SELECT * FROM order_item WHERE customer_order = ?');
			$q->execute(array($id));
			while ($item = $q->fetchObject()) {
				$item->extras = array();
				$order->items[] = $item;
				$order_item_map[$item->id] = $item;
			}

			// Haetaan lisukkeet.
			$q = $this->_pdo->prepare('SELECT * FROM order_item, order_item_extra '
					. 'WHERE order_item.id = order_item_extra.order_item and '
					. 'order_item.customer_order = ? ORDER BY order_item_extra.id');
			$q->execute(array($id));
			while ($extra = $q->fetchObject())
				$order_item_map[$extra->order_item]->extras[] = $extra;

			$this->_pdo->commit();
		} catch (PDOException $e) {
			try { $this->_pdo->rollBack(); } catch (PDOException $e2) { }
			add_error('Tilaustietojen haku epäonnistui.');
			$order = NULL;
		}

		if ($order)
			$order->when_to_deliver = date_create_from_format('Y-m-d H:i:s', $order->when_to_deliver);
		if ($order)
			$order->when_delivered = date_create_from_format('Y-m-d H:i:s', $order->when_delivered);

		return $order;
	}

	// Apufunktio tilauksen poistoa varten.
	private function delete_order_impl($id) {
		// Poistaa myös vastaavat order_item ja order_item_extra-tietueet (CASCADE).
		$q = $this->_pdo->prepare('DELETE FROM customer_order WHERE id = ?');
		$q->execute(array($id));
	}

	// Poistaa tilauksen.
	public function delete_order($id) {
		try {
			$this->delete_order_impl($id);
		} catch (PDOException $e) {
			add_error('Tilauksen poisto epäonnistui.');
		}
	}

	// Tallentaa tilauksen tiedot.
	public function update_order($order) {
		try {
			$this->_pdo->beginTransaction();

			$this->delete_order_impl($order->id);
			$this->insert_order_with_id($order);

			$this->_pdo->commit();
		} catch (PDOException $e) {
			try { $this->_pdo->rollBack(); } catch (PDOException $e2) { }
			add_error('Tilaustietojen tallennus epäonnistui.');
		} catch (Exception $e) {
			try { $this->_pdo->rollBack(); } catch (PDOException $e2) { }
			add_error($e->getMessage());
		}
	}

	// Hakee kaikki tilaukset.
	public function select_orders() {
		try {
			$q = $this->_pdo->prepare('SELECT * FROM customer_order ORDER BY '
					. 'when_delivered, when_to_deliver');
			$q->execute();

			$order_list = array();
			while($order = $q->fetchObject())
				$order_list[] = $order;
		} catch (PDOException $e) {
			add_error('Tilaustietojen haku epäonnistui.');
			$order_list = NULL;
		}

		return $order_list;
	}

	// Tallentaa tilauksen toimitustiedot.
	public function update_delivery_info($order) {
		try {
			$datestr = $this->_pdo->quote(date_format($order->when_delivered, 'Y-m-d H:i'));
			$query_str = 'UPDATE customer_order SET when_delivered='
				. ($order->when_delivered ? $datestr : 'when_to_deliver')
				. ', price_on_delivery='
				. ($order->price_on_delivery ? $this->_pdo->quote($order->price_on_delivery) : 'price')
				. ', notes=?, prevent=? WHERE id = ?';

			$q = $this->_pdo->prepare($query_str);
			$q->execute(array($order->notes, $order->prevent ? 'true' : 'false',
					$order->id));
		} catch (PDOException $e) {
			add_error('Toimitustietojen tallennus epäonnistui.');
		}
	}

	// Hakee käyttäjän.
	public function select_user($username) {
		try {
			$q = $this->_pdo->prepare('SELECT * FROM management_user WHERE username = ?');
			$q->execute(array($username));
		} catch (PDOException $e) {
			add_error('Käyttäjätietojen tarkistus epäonnistui.');
		}
		return $q->fetchObject();
	}

	// Muuttaa salasanan.
	public function change_password($username, $password_hash) {
		try {
			$q = $this->_pdo->prepare('UPDATE management_user SET '
					. 'password_hash = ? WHERE username = ?');
			$q->execute(array($password_hash, $username));
		} catch (PDOException $e) {
			add_error('Salasanan vaihto epäonnistui.');
		}
	}
}

try {
	$pdo = new PDO("pgsql:host=localhost;dbname=$DATABASE_USERNAME",
			$DATABASE_USERNAME, $DATABASE_PASSWORD);
} catch (PDOException $e) {
	die('VIRHE: ' . $e->getMessage());
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$queries = new Queries($pdo);
