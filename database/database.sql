-- Types

CREATE TYPE purchase_state AS ENUM ('Waiting for payment', 'Waiting for payment approval', 'Paid', 'Shipped', 'Completed');

--Tables

CREATE TABLE category (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL CONSTRAINT category_name_uk UNIQUE 
);

CREATE TABLE product (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    price FLOAT NOT NULL CONSTRAINT product_price_ck CHECK (price >= 0),
    description TEXT NOT NULL,
    discount FLOAT NOT NULL CONSTRAINT product_discount_ck CHECK (((discount >= 0) OR (discount <= 1))),
    stock INTEGER NOT NULL CONSTRAINT product_stock_ck CHECK (stock >= 0),
    is_enabled BOOLEAN DEFAULT TRUE NOT NULL,
    id_category INTEGER REFERENCES category (id)
);

CREATE TABLE user (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL CONSTRAINT user_username_uk UNIQUE,
    password TEXT NOT NULL
);

CREATE TABLE client (
    id INTEGER REFERENCES user (id) PRIMARY KEY,
    username TEXT NOT NULL CONSTRAINT client_username_uk UNIQUE,
    email TEXT NOT NULL CONSTRAINT client_email_uk UNIQUE,
    password TEXT NOT NULL
);

CREATE TABLE staff_member (
    id INTEGER REFERENCES user (id) PRIMARY KEY,
    username TEXT NOT NULL CONSTRAINT client_username_uk UNIQUE,
    password TEXT NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE NOT NULL
);

CREATE TABLE wishlist (
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_client INTEGER NOT NULL REFERENCES client (id),
    PRIMARY KEY (id_product, id_client)
);

CREATE TABLE cart (
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_client INTEGER NOT NULL REFERENCES client (id),
    quantity INTEGER NOT NULL CONSTRAINT quantity_ck CHECK (quantity >= 0),
    PRIMARY KEY (id_product, id_client)
);