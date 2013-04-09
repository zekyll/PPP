<?php $title = 'Tilaus'; require 'top.php'; ?>

<p><a href="editorder.php">Tee uusi tilaus</a></p>

<p>Voit myös tarkastella ja muokata aikaisempaa tilausta:
 </p>

<form action="vieworder.php" method="get">
	<input type="text" name="id" value="" placeholder="Anna tilaustunnus">
	<input type="submit" value="Hae tilaus">
</form>

<p>Huom! Muokkaus on mahdollista vain jos toimitukseen on aikaa yli tunti.</p>

<?php require 'bottom.php'; ?>