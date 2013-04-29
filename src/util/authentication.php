<?php
/*
 * Apufunktiot käyttäjän tunnistautumista ja salasanan hashausta varten.
 */

require_once 'queries.php';
require_once 'session.php';

function require_login() {
	global $session;
	if (!isset($session->username))
		redirect('login.php');
}

// Muodostaa suolatun salasanan hajautusarvon.
function pswd_hash($password) {
	$cost = 10;
	$rand_bytes = openssl_random_pseudo_bytes(16);
	$salt = '$2a$' . str_pad($cost, 2, '0', STR_PAD_LEFT) . '$' .
			substr(strtr(base64_encode($rand_bytes), '+', '.'), 0, 22);

	return crypt($password, $salt);
}

// Tarkistaa käyttäjänimen ja salasanan oikeellisuuden tietokannasta.
function authenticate($username, $password) {
	global $queries;
	$user = $queries->select_user($username);
	if (crypt($password, $user->password_hash) == $user->password_hash)
		return $user;
	else
		return NULL;
}

// Yrittää kirjautua sisään annetuilla käyttäjätiedoilla.
function login($username, $password) {
	global $session;
	$user = authenticate($username, $password);
	if ($user)
		$session->username = $user->username;
	return $user;
}

// Vaihtaa käyttäjän salasanan.
function change_password($username, $new_password) {
	global $queries;
	$queries->change_password($username, pswd_hash($new_password));
}