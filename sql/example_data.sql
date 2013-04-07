INSERT INTO product (name, type, price, description, image_name) VALUES
('Pitakebab', 'Kebabit', 8.00, 'Kebabliha, pitaleipää, salaattia.', NULL),
('Kebab riisillä', 'Kebabit', 8.00, 'Kebabliha, riisiä, salaattia.', NULL),
('Americano', 'Pizzat', 7.00, 'Täytteenä kinkku, ananas ja homejuusto.', 'samples/39GLLGM2NW9397B0.png'),
('Bolognese', 'Pizzat', 7.00, 'Täytteenä jauheliha.', NULL),
('Opera', 'Pizzat', 8.00, 'Täytteenä kinkku ja tonnikala.', NULL),
('Salami', 'Pizzat', 8.00, 'Täytteenä salami, herkkusieni ja sipuli.', NULL),
('Chicken Hawaii', 'Pizzat', 8.00, 'Täytteenä kana, ananas, aurajuusto ja currykastike.', 'samples/DBTDXVDNUMVOIEBB.png'),
('Coca Cola', 'Juomat', 2.00, NULL, NULL),
('Sprite', 'Juomat', 2.00, NULL, NULL),
('Jaffa', 'Juomat', 2.00, NULL, NULL),
('Pepsi', 'Juomat', 2.00, NULL, NULL),
('Kinkku', 'Lisätäytteet', 1.00, NULL, NULL),
('Ananas', 'Lisätäytteet', 1.00, NULL, NULL),
('Jauheliha', 'Lisätäytteet', 1.00, NULL, NULL),
('Katkarapu', 'Lisätäytteet', 1.00, NULL, NULL),
('Pekoni', 'Lisätäytteet', 1.00, NULL, NULL),
('Paprika', 'Lisätäytteet', 1.00, NULL, NULL),
('Homejuusto', 'Lisätäytteet', 0.10, NULL, NULL),
('Kana', 'Lisätäytteet', 1.00, NULL, NULL),
('Valkosipuli', 'Lisätäytteet', 0.10, NULL, NULL),
('Oregano', 'Lisätäytteet', 0.00, NULL, NULL)
;

INSERT INTO customer_order (id, price, name, address, contact, when_to_deliver, additional_info, confirmed) VALUES
('J2POY0O0F3', 16.20, 'Matti Meikäläinen', 'Kotikatu 13 01234 Kaupunki', '01-23456789', '2013-06-01 18:00:00', 'Laittakaa paljon oregaanoa!', True)
;

INSERT INTO customer_order (id, price, name, address, contact, when_to_deliver, additional_info, confirmed, when_delivered, price_on_delivery, notes, prevent) VALUES
('UJD2JE22MK', 24.00, 'Maija Suomalainen', 'Kotitie 1 04321 City', 'maija.suomalainen@mailidomain.fi', '2013-06-04 17:00:00', NULL, True, '2013-06-01 17:10:00', 20.00, '', False),
('J2FK8H2NSS', 20.00, 'Risto Rämä', 'Slummitie 4 54321 Peräkylä', '09-9876543321', '2013-06-01 20:05:00', NULL, True, '2013-06-01 20:00:00', 20.00, 'Asiakas ei maksanut.', True)
;

INSERT INTO order_item (customer_order, product, price, count) VALUES
('J2POY0O0F3', (SELECT id FROM product WHERE name = 'Americano'), 14.20, 2),
('J2POY0O0F3', (SELECT id FROM product WHERE name = 'Jaffa'), 2.00, 1),
('UJD2JE22MK', (SELECT id FROM product WHERE name = 'Chicken Hawaii'), 24.00, 3),
('J2FK8H2NSS', (SELECT id FROM product WHERE name = 'Pitakebab'), 16.00, 2),
('J2FK8H2NSS', (SELECT id FROM product WHERE name = 'Coca Cola'), 4.00, 2)
;

INSERT INTO order_item_extra (order_item, product, price) VALUES
((SELECT id FROM order_item WHERE customer_order = 'J2POY0O0F3' LIMIT 1 OFFSET 0),
	(SELECT id FROM product WHERE name = 'Oregano'), 0.00),
((SELECT id FROM order_item WHERE customer_order = 'J2POY0O0F3' LIMIT 1 OFFSET 0),
	(SELECT id FROM product WHERE name = 'Valkosipuli'), 0.10)
;

