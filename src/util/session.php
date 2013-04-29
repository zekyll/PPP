<?php
/*
 * Apuluokka muuttujien tallentamiseksi $_SESSION-taulukkoon.
 */

require_once 'util.php';

class Session {

	public function __construct() {
		session_start();
	}

	public function __set($avain, $arvo) {
		$_SESSION[$avain] = $arvo;
	}

	public function __get($avain) {
		if ($this->__isset($avain)) {
			return $_SESSION[$avain];
		}
		return null;
	}

	public function __isset($avain) {
		return isset($_SESSION[$avain]);
	}

	public function __unset($avain) {
		unset($_SESSION[$avain]);
	}

}

$session = new Session();

function add_msg($msg) {
	$_SESSION['messages'][] = $msg;
}

function add_error($error_msg) {
	$_SESSION['errors'][] = $error_msg;
}

function have_errors() {
	return !empty($_SESSION['errors']);
}
