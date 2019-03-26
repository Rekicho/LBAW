-- Types

CREATE TYPE state_purchase AS ENUM ('Waiting for payment', 'Waiting for payment approval', 'Paid', 'Shipped', 'Completed', 'Returned');

--Tables

CREATE TABLE IF NOT EXISTS category (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL CONSTRAINT category_name_uk UNIQUE 
);

CREATE TABLE IF NOT EXISTS product (
    id SERIAL PRIMARY KEY,
    id_category INTEGER REFERENCES category (id),
    name TEXT NOT NULL,
    price FLOAT NOT NULL CONSTRAINT product_price_ck CHECK (price >= 0),
    description TEXT NOT NULL,
    discount FLOAT NOT NULL CONSTRAINT product_discount_ck CHECK (((discount >= 0) OR (discount <= 1))),
    stock INTEGER NOT NULL CONSTRAINT product_stock_ck CHECK (stock >= 0),
    is_enabled BOOLEAN DEFAULT TRUE NOT NULL
);

CREATE TABLE IF NOT EXISTS "user" (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL CONSTRAINT user_username_uk UNIQUE,
    email TEXT NOT NULL CONSTRAINT client_email_uk UNIQUE,
    password TEXT NOT NULL,
    is_admin BOOLEAN,
    is_enabled BOOLEAN
);

CREATE TABLE IF NOT EXISTS wishlist (
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    PRIMARY KEY (id_product, id_client)
);

CREATE TABLE IF NOT EXISTS cart (
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    quantity INTEGER NOT NULL CONSTRAINT quantity_ck CHECK (quantity > 0),
    PRIMARY KEY (id_product, id_client)
);

CREATE TABLE IF NOT EXISTS review (
    id SERIAL PRIMARY KEY,
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    comment TEXT NOT NULL,
    rating INTEGER NOT NULL CONSTRAINT review_rating_ck CHECK (((rating > 0) OR (rating <= 5))),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    CONSTRAINT review_product_client_uk UNIQUE(id_product, id_client)
);

CREATE TABLE IF NOT EXISTS report (
    id SERIAL PRIMARY KEY,
    reason TEXT NOT NULL,
    id_review INTEGER NOT NULL REFERENCES review (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE IF NOT EXISTS report_log (
    report_id INTEGER REFERENCES report (id) PRIMARY KEY,
    id_staff_member INTEGER NOT NULL REFERENCES staff_member (id),
    has_deleted BOOLEAN NOT NULL,
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE IF NOT EXISTS billing_information (
    id SERIAL PRIMARY KEY,
    id_client INTEGER NOT NULL REFERENCES "user" (id)
    full_name TEXT NOT NULL,
    address TEXT NOT NULL,
    city TEXT NOT NULL,
    state TEXT NOT NULL,
    zip_code TEXT NOT NULL,
);

CREATE TABLE IF NOT EXISTS purchase (
    id SERIAL PRIMARY KEY,
    id_billing_information INTEGER NOT NULL REFERENCES billing_information (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE IF NOT EXISTS purchase_state (
    id SERIAL PRIMARY KEY,
    TYPE state_purchase CONSTRAINT state_purchase_uk UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS purchased_product (
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_purchase INTEGER NOT NULL REFERENCES purchase (id),
    name TEXT NOT NULL,
    price FLOAT NOT NULL CONSTRAINT purchased_product_price_ck CHECK (price >= 0),
    description TEXT NOT NULL,
    discount FLOAT NOT NULL CONSTRAINT purchased_product_discount_ck CHECK (((discount >= 0) OR (discount <= 1))),
    quantity INTEGER NOT NULL CONSTRAINT purchased_product_quantity_ck CHECK (quantity > 0),
    PRIMARY KEY (id_product, id_purchase)
);

CREATE TABLE IF NOT EXISTS purchase_log (
    id SERIAL PRIMARY KEY,
    id_purchase_state INTEGER NOT NULL REFERENCES purchase_state (id),
    id_purchase INTEGER NOT NULL REFERENCES purchase (id),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE IF NOT EXISTS ban (
    id SERIAL PRIMARY KEY,
    id_staff_member INTEGER NOT NULL REFERENCES staff_member (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    start TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    end TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reason TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS discount (
    id SERIAL PRIMARY KEY,
    id_category INTEGER REFERENCES Category (id),
    start TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    end TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);