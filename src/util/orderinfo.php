<table>
	<tr>
		<td class="nfotitle">Tunnus:</td>
		<td><?= htmlspecialchars($order->id) ?></td>
	</tr>
	<tr>
		<td class="nfotitle">Nimi:</td>
		<td><?= htmlspecialchars($order->name) ?></td>
	</tr>
	<tr>
		<td class="nfotitle">Osoite:</td>
		<td><?= htmlspecialchars($order->address) ?></td>
	</tr>
	<tr>
		<td class="nfotitle">Yhteystieto:</td>
		<td><?= htmlspecialchars($order->contact) ?></td>
	</tr>
	<tr>
		<td class="nfotitle">Toimitusaika:</td>
		<td><?= datefmt($order->when_to_deliver) ?></td>
	</tr>
	<tr>
		<td class="nfotitle">Lisätiedot:</td>
		<td><?= htmlspecialchars($order->additional_info) ?></td>
	</tr>
	<tr>
		<td class="nfotitle">Sisältö:</td>
		<td>
			<table>
				<?php
				$product_map = array();
				foreach ($products as $p)
					$product_map[$p->id] = $p;

				foreach ($order->items as $item) {
					echo '<tr>';
					echo '<td class="number">', htmlspecialchars($item->count), ' x</td>';
					echo '<td>';
					echo htmlspecialchars($product_map[$item->product]->name);
					foreach ($item->extras as $extra)
						echo "<br>- ", htmlspecialchars($product_map[$extra->product]->name);
					echo '</td>';
					echo '<td class="number">', htmlspecialchars(pricefmt($item->price)), ' €</td>';
					echo "</tr>\n";
				}
				?>
				<tr>
					<td></td>
					<td>Yht:</td>
					<td class="number"><?= htmlspecialchars(pricefmt($order->price)), ' €' ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>