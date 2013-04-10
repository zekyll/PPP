CREATE TABLE product (
    id                serial PRIMARY KEY,
    name              varchar NOT NULL,
    type              varchar NOT NULL,
    price             decimal(8,2) NOT NULL,
    description       varchar,
    image_name        varchar,
    deleted           bool NOT NULL DEFAULT false
);

CREATE TABLE customer_order (
    id                varchar PRIMARY KEY,
    price             decimal(8,2) NOT NULL,
    name              varchar NOT NULL,
    address           varchar NOT NULL,
    contact           varchar NOT NULL,
    when_to_deliver   timestamp NOT NULL,
    additional_info   varchar,
    confirmed         bool NOT NULL DEFAULT false,
    when_delivered    timestamp,
    price_on_delivery decimal(8,2),
    notes             varchar,
    prevent           bool NOT NULL DEFAULT false
);

CREATE TABLE order_item (
    id                serial PRIMARY KEY,
    customer_order    varchar NOT NULL references customer_order(id)
                           ON DELETE CASCADE,
    product           serial NOT NULL references product(id)
                           ON DELETE RESTRICT,
    price             decimal(8,2) NOT NULL,
    count             int NOT NULL CONSTRAINT pos CHECK(count > 0)
);

CREATE TABLE order_item_extra (
    id                serial PRIMARY KEY,
    order_item        serial NOT NULL references order_item(id)
                          ON DELETE CASCADE,
    product           serial NOT NULL references product(id)
                          ON DELETE RESTRICT,
    price             decimal(8,2) NOT NULL
);

CREATE TABLE management_user (
    username          varchar PRIMARY KEY,
    password_hash     varchar
);

INSERT INTO management_user (username, password_hash) VALUES
	('admin', '$2a$10$iZT9lHMlMzje89jClrCTdOpJgFJMxSz6AuuGX/XDI9iQjD1kc/2Uq')
;
