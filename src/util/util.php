<?php

function generateRandomId($length) {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		$id = '';
		for($i = 0; $i < $length; ++$i)
			$id = $id . $chars[rand(0, strlen($chars) - 1)];

		return $id;
}

function datefmt($date)
{
	if ($date instanceof DateTime)
		return $date->format('j.n.Y H:i');
	else
		return $date;
}

function isPositiveInteger($str)
{
	return is_numeric($str) && is_int(($str + 0)) && $str > 0;
}

function add_error($error_msg)
{
	$GLOBALS['errors'][] = $error_msg;
}

function have_errors()
{
	return !empty($GLOBALS['errors']);
}

$errors = array();

?>
