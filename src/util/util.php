<?php
/*
 * Yleiskäyttöisiä apufunktioita.
 */

// Generoi merkeistä A-Z, 0-9 satunnaisen merkkijonon, jolla on annettu pituus.
function generateRandomId($length) {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		$id = '';
		for($i = 0; $i < $length; ++$i)
			$id = $id . $chars[rand(0, strlen($chars) - 1)];

		return $id;
}

// Palauttaa DateTime-objektin tekstinä suomalaisessa muodossa.
function datefmt($date)
{
	if ($date instanceof DateTime)
		return $date->format('j.n.Y H:i');
	else
		return $date;
}

// Palauttaa luvun tekstinä käyttäen pilkkua desimaalierottimena.
function pricefmt($price)
{
	return str_replace('.', ',', $price);
}

// Testaa onko merkkijono positiivinen kokonaisluku.
function isPositiveInteger($str)
{
	return is_numeric($str) && is_int(($str + 0)) && $str > 0;
}

// Uudelleenohjaa sivun annettuun osoitteeseen.
function redirect($location)
{
	header("Location: $location");
	exit();
}
