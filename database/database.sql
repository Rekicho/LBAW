-----------------------------------------
-- Drop old schmema
-----------------------------------------

DROP TABLE IF EXISTS category CASCADE;
DROP TABLE IF EXISTS product CASCADE;
DROP TABLE IF EXISTS "user" CASCADE;
DROP TABLE IF EXISTS wishlist CASCADE;
DROP TABLE IF EXISTS cart CASCADE;
DROP TABLE IF EXISTS review CASCADE;
DROP TABLE IF EXISTS report CASCADE;
DROP TABLE IF EXISTS report_log CASCADE;
DROP TABLE IF EXISTS billing_information CASCADE;
DROP TABLE IF EXISTS purchase CASCADE;
DROP TABLE IF EXISTS purchase_state CASCADE;
DROP TABLE IF EXISTS purchased_product CASCADE;
DROP TABLE IF EXISTS purchase_log CASCADE;
DROP TABLE IF EXISTS ban CASCADE;
DROP TABLE IF EXISTS discount CASCADE;

DROP TYPE IF EXISTS state_purchase;

DROP FUNCTION IF EXISTS ensure_admin() CASCADE;
DROP FUNCTION IF EXISTS ensure_stock() CASCADE;
DROP FUNCTION IF EXISTS user_review() CASCADE;
DROP FUNCTION IF EXISTS product_search_update() CASCADE;

DROP TRIGGER IF EXISTS ensure_admin ON "user";
DROP TRIGGER IF EXISTS ensure_stock ON purchased_product;
DROP TRIGGER IF EXISTS user_review ON review;
DROP TRIGGER IF EXISTS product_search ON product;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE state_purchase AS ENUM ('Waiting for payment', 'Waiting for payment approval', 'Paid', 'Shipped', 'Completed', 'Returned');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE category (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL CONSTRAINT category_name_uk UNIQUE 
);

CREATE TABLE product (
    id SERIAL PRIMARY KEY,
    id_category INTEGER REFERENCES category (id),
    name TEXT NOT NULL,
    price FLOAT NOT NULL CONSTRAINT product_price_ck CHECK (price >= 0),
    description TEXT NOT NULL,
    discount FLOAT NOT NULL CONSTRAINT product_discount_ck CHECK (((discount >= 0) OR (discount <= 1))),
    stock INTEGER NOT NULL CONSTRAINT product_stock_ck CHECK (stock >= 0),
    is_enabled BOOLEAN DEFAULT TRUE NOT NULL,
    search tsvector
);

CREATE TABLE "user" (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL CONSTRAINT user_username_uk UNIQUE,
    email TEXT NOT NULL CONSTRAINT client_email_uk UNIQUE,
    password TEXT NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE NOT NULL
);

CREATE TABLE wishlist (
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    PRIMARY KEY (id_product, id_client)
);

CREATE TABLE cart (
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    quantity INTEGER NOT NULL CONSTRAINT quantity_ck CHECK (quantity > 0),
    PRIMARY KEY (id_product, id_client)
);

CREATE TABLE review (
    id SERIAL PRIMARY KEY,
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    comment TEXT NOT NULL,
    rating INTEGER NOT NULL CONSTRAINT review_rating_ck CHECK (((rating > 0) OR (rating <= 5))),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    CONSTRAINT review_product_client_uk UNIQUE(id_product, id_client)
);

CREATE TABLE report (
    id SERIAL PRIMARY KEY,
    reason TEXT NOT NULL,
    id_review INTEGER NOT NULL REFERENCES review (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    CONSTRAINT report_review_client_uk UNIQUE(id_review, id_client)
);

CREATE TABLE report_log (
    report_id INTEGER REFERENCES report (id) PRIMARY KEY,
    id_user INTEGER NOT NULL REFERENCES "user" (id),
    has_deleted BOOLEAN NOT NULL,
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE billing_information (
    id SERIAL PRIMARY KEY,
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    full_name TEXT NOT NULL,
    address TEXT NOT NULL,
    city TEXT NOT NULL,
    state TEXT NOT NULL,
    zip_code TEXT NOT NULL
);

CREATE TABLE purchase (
    id SERIAL PRIMARY KEY,
    id_billing_information INTEGER NOT NULL REFERENCES billing_information (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE purchase_state (
    id SERIAL PRIMARY KEY,
    TYPE state_purchase CONSTRAINT state_purchase_uk UNIQUE NOT NULL
);

CREATE TABLE purchased_product (
    id_product INTEGER NOT NULL REFERENCES product (id),
    id_purchase INTEGER NOT NULL REFERENCES purchase (id),
    name TEXT NOT NULL,
    price FLOAT NOT NULL CONSTRAINT purchased_product_price_ck CHECK (price >= 0),
    description TEXT NOT NULL,
    discount FLOAT NOT NULL CONSTRAINT purchased_product_discount_ck CHECK (((discount >= 0) OR (discount <= 1))),
    quantity INTEGER NOT NULL CONSTRAINT purchased_product_quantity_ck CHECK (quantity > 0),
    PRIMARY KEY (id_product, id_purchase)
);

CREATE TABLE purchase_log (
    id SERIAL PRIMARY KEY,
    id_purchase_state INTEGER NOT NULL REFERENCES purchase_state (id),
    id_purchase INTEGER NOT NULL REFERENCES purchase (id),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE ban (
    id SERIAL PRIMARY KEY,
    id_user INTEGER NOT NULL REFERENCES "user" (id),
    id_client INTEGER NOT NULL REFERENCES "user" (id),
    start_t TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    end_t TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reason TEXT NOT NULL
);

CREATE TABLE discount (
    id SERIAL PRIMARY KEY,
    id_category INTEGER REFERENCES Category (id),
    start_t TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    end_t TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

-----------------------------------------
-- INDEXES
-----------------------------------------


-----------------------------------------
-- TRIGGERS and UDFs
-----------------------------------------

-- TODO: mudar para um check na propria tabela

CREATE FUNCTION ensure_admin() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF (SELECT COUNT(*) FROM "user" WHERE is_admin = true) = 1
    THEN RAISE EXCEPTION 'There must be at least one administrator.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER ensure_admin
    BEFORE UPDATE OF is_admin ON "user"
    FOR EACH ROW
    EXECUTE PROCEDURE ensure_admin();


CREATE FUNCTION ensure_stock() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF EXISTS (SELECT * FROM product WHERE id_product = New.id_product AND stock >= New.quantity)
    THEN RAISE EXCEPTION 'A product must have available stock in order to be bought.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER ensure_stock
    BEFORE INSERT ON purchased_product
    FOR EACH ROW
    EXECUTE PROCEDURE ensure_stock();


CREATE FUNCTION user_review() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NOT EXISTS ( SELECT * FROM review, purchased_product, purchase
                    WHERE NEW.id_product == purchased_product.id_product
                        AND purchased_product.id_purchase == purchase.id_purchase
                        AND purchase.id_client == NEW.id_client
                        AND NEW.date_time > purchase.date_time
                )
    THEN RAISE EXCEPTION 'A client can only review a product after buying it';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER user_review
    BEFORE INSERT ON review
    FOR EACH ROW
    EXECUTE PROCEDURE user_review();


CREATE FUNCTION product_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.search = to_tsvector('english', NEW.name || ' ' || NEW.description);
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF NEW.name <> OLD.name OR NEW.description <> OLD.description THEN
            NEW.search = to_tsvector('english', NEW.name || ' ' || NEW.description);
        END IF;
    END IF;
    RETURN NEW;
END
$$ LANGUAGE plpgsql;

CREATE TRIGGER product_search
    BEFORE INSERT OR UPDATE ON product
    FOR EACH ROW
    EXECUTE PROCEDURE product_search_update()

-----------------------------------------
-- end
-----------------------------------------