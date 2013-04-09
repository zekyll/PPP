<?php
	require_once dirname(__file__) . '/../util/util.php';
	require_once dirname(__file__) . '/../util/session.php';
	require_once dirname(__file__) . '/../util/authentication.php';

	require_login();

	if (array_key_exists('change', $_POST)) {
		if (strlen($_POST['newpassword']) == 0)
			add_error('Salasana ei saa olla tyhjä');
		if ($_POST['newpassword'] != $_POST['newpassword2'])
			add_error('Uuden salasanan toisto oli väärin');

		if (!authenticate($session->username, $_POST['password']))
			add_error('Vanha salasana oli väärin');

		if (!have_errors()) {
			change_password($session->username, $_POST['newpassword']);
			if (!have_errors()) {
				add_msg('Salasana vaihdettu.');
				redirect('.');
			}
		}
	}
?>

<?php $title = 'Salasanan vaihto'; require 'top.php'; ?>

<?php require dirname(__file__) . '/../util/messages.php'; ?>

<form action="changepswd.php" method="post">
	<div>
		Vanha salasana:<br>
		<input type="password" name="password">
	</div>

	<div>
		Uusi salasana:<br>
		<input type="password" name="newpassword">
	</div>

	<div>
		Toista uusi salasana:<br>
		<input type="password" name="newpassword2">
	</div>

	<input type="submit" name="change" value="Vaihda">
</form>

<?php require 'bottom.php'; ?>