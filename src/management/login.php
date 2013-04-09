<?php
	require_once dirname(__file__) . '/../util/util.php';
	require_once dirname(__file__) . '/../util/session.php';
	require_once dirname(__file__) . '/../util/authentication.php';

	if (array_key_exists('login', $_GET)) {
		$user = login($_POST['username'], $_POST['password']);
		if ($user) {
			add_msg('Sisäänkirjautuminen onnistui.');
			redirect('.');
		} else {
			add_error('Käyttäjänimi tai salasana oli väärin.');
			redirect('login.php');
		}
	} elseif (array_key_exists('logout', $_GET)) {
		unset($session->username);
		redirect('.');
	}
?>

<?php $title = 'Kirjautuminen'; require 'top.php'; ?>

<?php require dirname(__file__) . '/../util/messages.php'; ?>

<form action="login.php?login" method="post">
	<div>
		Käyttäjänimi:<br>
		<input type="text" name="username">
	</div>

	<div>
		Salasana:<br>
		<input type="password" name="password">
	</div>

	<input type="submit" name="login" value="Kirjaudu">
</form>

<?php require 'bottom.php'; ?>