-----------------------------------------
-- Drop old schmema
-----------------------------------------

DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS wishlists CASCADE;
DROP TABLE IF EXISTS carts CASCADE;
DROP TABLE IF EXISTS reviews CASCADE;
DROP TABLE IF EXISTS reports CASCADE;
DROP TABLE IF EXISTS report_log CASCADE;
DROP TABLE IF EXISTS billing_information CASCADE;
DROP TABLE IF EXISTS purchase CASCADE;
DROP TABLE IF EXISTS purchased_product CASCADE;
DROP TABLE IF EXISTS purchase_log CASCADE;
DROP TABLE IF EXISTS ban CASCADE;
DROP TABLE IF EXISTS discount CASCADE;

DROP TYPE IF EXISTS state_purchase;

DROP FUNCTION IF EXISTS ensure_admin() CASCADE;
DROP FUNCTION IF EXISTS ensure_stock() CASCADE;
DROP FUNCTION IF EXISTS user_review() CASCADE;
DROP FUNCTION IF EXISTS product_search_update() CASCADE;
DROP FUNCTION IF EXISTS add_initial_state() CASCADE;
DROP FUNCTION IF EXISTS ensure_discount() CASCADE;
DROP FUNCTION IF EXISTS update_user_status() CASCADE;

DROP TRIGGER IF EXISTS ensure_admin ON users;
DROP TRIGGER IF EXISTS ensure_stock ON purchased_product;
DROP TRIGGER IF EXISTS user_review ON reviews;
DROP TRIGGER IF EXISTS product_search ON products;
DROP TRIGGER IF EXISTS add_initial_state ON purchase;
DROP TRIGGER IF EXISTS ensure_discount ON discount;
DROP TRIGGER IF EXISTS update_user_status ON ban;


-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE state_purchase AS ENUM ('Waiting for payment', 'Waiting for payment approval', 'Paid', 'Shipped', 'Completed', 'Returned');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL CONSTRAINT category_name_uk UNIQUE 
);

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    id_category INTEGER REFERENCES categories (id),
    name TEXT NOT NULL,
    price FLOAT NOT NULL CONSTRAINT product_price_ck CHECK (price >= 0),
    description TEXT NOT NULL,
    discount FLOAT NOT NULL CONSTRAINT product_discount_ck CHECK (((discount >= 0) OR (discount <= 1))),
    stock INTEGER NOT NULL CONSTRAINT product_stock_ck CHECK (stock >= 0),
    is_enabled BOOLEAN DEFAULT TRUE NOT NULL,
    search tsvector
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL CONSTRAINT user_username_uk UNIQUE,
    email TEXT CONSTRAINT client_email_uk UNIQUE,
    password TEXT NOT NULL,
    is_staff_member BOOLEAN DEFAULT FALSE NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE NOT NULL,
    remember_token VARCHAR
);

CREATE TABLE wishlists (
    id_product INTEGER NOT NULL REFERENCES products (id),
    id_client INTEGER NOT NULL REFERENCES users (id),
    PRIMARY KEY (id_product, id_client)
);

CREATE TABLE carts (
    id_product INTEGER NOT NULL REFERENCES products (id),
    id_client INTEGER NOT NULL REFERENCES users (id),
    quantity INTEGER NOT NULL CONSTRAINT quantity_ck CHECK (quantity > 0),
    PRIMARY KEY (id_product, id_client)
);

CREATE TABLE reviews (
    id SERIAL PRIMARY KEY,
    id_product INTEGER NOT NULL REFERENCES products (id),
    id_client INTEGER NOT NULL REFERENCES users (id),
    comment TEXT NOT NULL,
    rating INTEGER NOT NULL CONSTRAINT review_rating_ck CHECK (((rating > 0) OR (rating <= 5))),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    CONSTRAINT review_product_client_uk UNIQUE(id_product, id_client)
);

CREATE TABLE reports (
    id SERIAL PRIMARY KEY,
    reason TEXT NOT NULL,
    id_review INTEGER NOT NULL REFERENCES reviews (id),
    id_client INTEGER NOT NULL REFERENCES users (id),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    CONSTRAINT report_review_client_uk UNIQUE(id_review, id_client)
);

CREATE TABLE report_log (
    report_id INTEGER REFERENCES reports (id) PRIMARY KEY,
    id_staff_member INTEGER NOT NULL REFERENCES users (id),
    has_deleted BOOLEAN NOT NULL,
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE billing_information (
    id SERIAL PRIMARY KEY,
    id_client INTEGER NOT NULL REFERENCES users (id),
    full_name TEXT NOT NULL,
    address TEXT NOT NULL,
    city TEXT NOT NULL,
    state TEXT NOT NULL,
    zip_code TEXT NOT NULL
);

CREATE TABLE purchase (
    id SERIAL PRIMARY KEY,
    id_billing_information INTEGER NOT NULL REFERENCES billing_information (id),
    id_client INTEGER NOT NULL REFERENCES users (id),
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE purchased_product (
    id_product INTEGER NOT NULL REFERENCES products (id),
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
    id_purchase INTEGER NOT NULL REFERENCES purchase (id),
    purchase_state state_purchase NOT NULL,
    "date_time" TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE ban (
    id SERIAL PRIMARY KEY,
    id_staff_member INTEGER NOT NULL REFERENCES users (id),
    id_client INTEGER NOT NULL REFERENCES users (id),
    start_t TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    end_t TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reason TEXT NOT NULL
);

CREATE TABLE discount (
    id SERIAL PRIMARY KEY,
    id_category INTEGER REFERENCES categories (id),
    value INTEGER NOT NULL,
    start_t TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    end_t TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

-----------------------------------------
-- INDEXES
-----------------------------------------

 CREATE INDEX product_id_category ON products USING hash (id_category); 

 CREATE INDEX username_user ON users USING hash (username); 

 CREATE INDEX email_user ON users USING hash (email); 

 CREATE INDEX review_id_product ON reviews USING hash (id_product); 

 CREATE INDEX billing_information_id_client ON billing_information USING hash (id_client); 

 CREATE INDEX purchased_product_id_purchase ON purchased_product USING hash (id_purchase); 

 CREATE INDEX product_price ON products USING btree (price); 

 CREATE INDEX product_discount ON products USING btree (discount); 

 CREATE INDEX start_discount ON discount USING btree (start_t); 

 CREATE INDEX end_discount ON discount USING btree (end_t); 

-----------------------------------------
-- FULL TEXT SEARCH
-----------------------------------------

 CREATE INDEX product_search_index ON PRODUCTS USING GIST (search);

-----------------------------------------
-- TRIGGERS and UDFs
-----------------------------------------

-- TODO: mudar para um check na propria tabela

CREATE FUNCTION ensure_admin() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF New.is_admin = false AND (SELECT COUNT(*) FROM users WHERE is_admin = true) = 1
    THEN RAISE EXCEPTION 'There must be at least one administrator.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER ensure_admin
    BEFORE UPDATE OF is_admin ON users
    FOR EACH ROW
    EXECUTE PROCEDURE ensure_admin();


CREATE FUNCTION ensure_stock() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF EXISTS (SELECT * FROM products WHERE products.id = New.id_product AND stock < New.quantity)
    THEN RAISE EXCEPTION 'A product must have available stock in order to be bought.';
    END IF;
    UPDATE products SET stock = stock - New.quantity
    WHERE id = New.id_product;
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
    IF NOT EXISTS ( SELECT * FROM purchased_product, purchase
                    WHERE NEW.id_product = purchased_product.id_product
                        AND purchased_product.id_purchase = purchase.id
                        AND purchase.id_client = NEW.id_client
                        AND NEW.date_time > purchase.date_time
                )
    THEN RAISE EXCEPTION 'A client can only review a product after buying it';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER user_review
    BEFORE INSERT ON reviews
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
    BEFORE INSERT OR UPDATE ON products
    FOR EACH ROW
    EXECUTE PROCEDURE product_search_update();
    
CREATE FUNCTION add_initial_state() RETURNS TRIGGER AS
$BODY$
BEGIN
    INSERT INTO purchase_log (id_purchase, purchase_state, "date_time")
    VALUES (New.id, 'Waiting for payment', New."date_time");
    RETURN NEW;
END
$BODY$ 
LANGUAGE plpgsql;

CREATE TRIGGER add_initial_state
AFTER INSERT ON purchase
FOR EACH ROW
EXECUTE PROCEDURE add_initial_state();

CREATE FUNCTION update_user_status() RETURNS TRIGGER AS
$BODY$
BEGIN
  IF EXISTS (SELECT * FROM ban WHERE id_client = New.id_client AND end_t > New.start_t)
  THEN RAISE EXCEPTION 'This user is already banned';
  ELSE
  UPDATE users SET is_enabled = false
  WHERE id = New.id_client;
  END IF;
  RETURN NULL;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER update_user_status
    BEFORE INSERT ON ban
    FOR EACH ROW
    EXECUTE PROCEDURE update_user_status();

CREATE FUNCTION ensure_discount() RETURNS TRIGGER AS
$BODY$
BEGIN
  IF EXISTS (SELECT * FROM discount WHERE id_category = New.id_category AND end_t > New.start_t)
  THEN RAISE EXCEPTION 'There''s already an active discount on this category';
  END IF;
  RETURN NULL;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER ensure_discount
    BEFORE INSERT ON discount
    FOR EACH ROW
    EXECUTE PROCEDURE ensure_discount();
-----------------------------------------
-- end
-----------------------------------------

-----------------------------------------
-- populate
-----------------------------------------

/* Category insert */
INSERT INTO categories (name) VALUES ('Watches');
INSERT INTO categories (name) VALUES ('Electronics');
INSERT INTO categories (name) VALUES ('Software.');
INSERT INTO categories (name) VALUES ('Pet Supplies.');
INSERT INTO categories (name) VALUES ('Video Games');
INSERT INTO categories (name) VALUES ('Art');
INSERT INTO categories (name) VALUES ('Home');
INSERT INTO categories (name) VALUES ('Books');
INSERT INTO categories (name) VALUES ('Beauty');
INSERT INTO categories (name) VALUES ('Toys and Games');

/* Product insert */
/* Watches */
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Mens Armani Exchange Watch AX2104',134.07,'Subtle mens Armani Exchange watch, with stylish stealth black Ion-plated steel case and bracelet. This sleek design has a black dial with black baton hour markers and detailing for maximum style. Inside the watch is a Japanese Quartz movement, featuring a date function at 3 o''clock. The dial also features the Armani Exchange logo at 12 o''clock for an added effect. It fastens with a push-button deployment on the black metal bracelet.',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Casio G-Shock DW-5600HRGRZ-1ER',80.13,'From G-SHOCK, which strives for toughness, comes a Black & Red Series that makes the most of G-SHOCK colors. A bi-color molding process makes it possible to create a band that is black on the outside and red on the inside. The flashes of red from the inside of the band when putting on or taking off the watch are a bold statement of G-SHOCK identity. FREE Exclusive Gorillaz track ''Tranz (Sibot Remix)'' with purchase.',0,400,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'HUGO Jump Watch',255.45,'HUGO Jump 1530028 is a functional and special Gents watch from JUMP collection. Material of the case is Black Ion-plated Steel and the Black dial gives the watch that unique look. 30 metres water resistancy will protect the watch and allows it to be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,351,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Tommy Hilfiger 1791121',262.31,'Stylish and unashamed, Luke by Tommy Hilfiger features a gold PVD plated case and bracelet with blue bezel, and is fitted with a quartz movement with day, date and 24 hour function shown on a silvery white dial, and is water resistant to 5atm.',0,450,True); 
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'STORM 47363/GY',139.88,'STORM 47363/GY is a functional and handsome Gents watch. Case material is Stainless Steel while the dial colour is Grey. In regards to the water resistance, the watch has got a resistancy up to 50 metres. It means it can be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports.',0,450,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Jack Wills Acland',128.24,'Jack Wills Acland JW003SLBR is a practical and very impressive Gents watch from Acland collection. Material of the case is Alloy, which stands for a high quality of the item and the Silver dial gives the watch that unique look. 30 metres water resistancy will protect the watch and allows it to be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,600,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Accurist Chronograph',139.88,'Accurist London Vintage 7033 is a super special Gents watch. Case material is Stainless Steel while the dial colour is Cream. The features of the watch include (among others) a chronograph and date function. 50 metres water resistancy will protect the watch and allows it to be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports.',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Tissot Classic Dream',157.38,'This men''s Tissot Classic Dream watch has a stainless steel case with sapphire crystal and is powered by a quartz movement. It is fastened with a brown leather strap and has a white dial with crisp roman numerals. The watch has a date function.',0,420,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Guess W0668G4',177.82,'Polished gold case, sunray champagne dial, brushed and polished gold bracelet.',0,510,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Bulova 96A208',406.87,'Sophisticated design with full exhibition dial and case-back. Stainless steel screw-back case, skeletonized black three-hand dial revealing the intricate workings of the self-winding 21-jewel movement, domed mineral crystal, stainless steel bracelet with push-button deployant clasp, and water resistance to 30 meters. Diameter: 43mm Thickness: 12.15mm',0,370,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Smart Turnout Master',112.59,'Smart Turnout Master Watch Lime Embossed Leather Strap STL3/RW/56/LIM is a trendy Unisex watch. Material of the case is PVD rose plating while the dial colour is Off white. 30 metres water resistancy will protect the watch and allows it to be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'HUGO Watch 1530069',186.59,'HUGO Focus 1530033 is an amazing and special Gents watch from FOCUS collection. Material of the case is Blue Ion-Plated Steel, which stands for a high quality of the item while the dial colour is Blue. In regards to the water resistance, the watch has got a resistancy up to 30 metres. It means it can be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'LLARSEN 147GWG3-GCAMEL20',207.77,'LLARSEN Oliver 147GWG3-GCAMEL20 is a practical and handsome Gents watch from LW47 collection. Material of the case is Stainless Steel while the dial colour is White. The watch is shipped with an original box and a guarantee from the manufacturer.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'G-Shock GA-100-1A1ER',66.16,'High-impact mens Casio X-Large G-Shock model in stealth black resin with black detailing, with white hour hand detailing. Features include chronograph, 5 daily alarms, countdown timer, world time from a Japanese Quartz movement with perpetual calendar and date function, LED backlight and digital tachymeter. The watch fastens with a sturdy rubber strap with double tang buckle for extra security. Impressively sized at 55mm and built as tough as possible, with 20 bar water resistance and the signature G-Shock design.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Bulova 98D149',672.06,'From the Precisionist Collection. New Champlain style in grey IP stainless steel with rose gold-tone accents on case and bracelet, 11 diamonds individually hand set on two-tone black/grey dial with calendar feature, curved mineral glass, screw-back case, fold-over buckle closure with safety lock and extender, and water resistance to 300 meters. Powered by Bulova’s proprietary three-prong quartz crystal Precisionist movement with a 262kHz vibrational frequency—eight times greater than standard watches—for unparalleled accuracy. Diameter: 46.5mm Thickness: 14.3mm',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Citizen CA0080-03E',265.81,'Citizen Red Arrows World Time model with a stainless steel round case. This intelligent time piece features 1/5 second chronograph, world time in 24 cities, 12.24 hour time, screw-back case and movement calibre: B612. It also has date function and is powered by Eco-Drive movement. The round black dial has high-visibility baton hour markers and hands which light up in the dark, red touches and date function. It''s 100 meter water resistant and fastens with a quality black with red stitching genuine leather strap.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Accurist MB921BX',104.47,'Mens Accurist Diamond watch in PVD gold plating, set around a black rectangular dial with gold baton hour markers.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Jean Paul Gaultier',104.47,'This iconic watch from the ENFANTS TERRIBLES collection by eponymous designer Jean Paul Gaultier features the unmistakeable stripes paired with a soft navy blue silicon strap for a sporty look and comfortable fit. Touches of warm rose gold make this a super trendy stand-out piece from the collection!',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Armani AX2103',113.56,'This stylish mens Armani Exchange watch in stainless steel features a 47mm case and centred on a black dial with silver baton hour markers and date function. The watch is fitted with quartz movement and fastens with a silver stainless steel bracelet.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Accurist 7033',77.77,'Accurist London Vintage 7033 is a super special Gents watch. Case material is Stainless Steel while the dial colour is Cream. The features of the watch include (among others) a chronograph and date function. 50 metres water resistancy will protect the watch and allows it to be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports. The watch is shipped with an original box and a guarantee from the manufacturer.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'STORM 47210/B',126.52,'STORM New Remi Lazer Blue 47210/B is a functional and special Gents watch. Material of the case is Stainless Steel while the dial colour is Blue. 50 metres water resistancy will protect the watch and allows it to be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports. The watch is shipped with an original box and a guarantee from the manufacturer.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'STORM 47388/B',95.62,'STORM Trionic 47388/B is a functional and special Gents watch. Material of the case is Stainless Steel while the dial colour is Blue. In regards to the water resistance, the watch has got a resistancy up to 50 metres. It means it can be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports. The watch is shipped with an original box and a guarantee from the manufacturer.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'STORM 47075/B',104.85,'Mens Storm Sotec Lazer watch in stainless steel, centred on a bright blue dial with date function and high-contrast hands.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'STORM 47155/B',104.45,'STORM Cyro XL 47155/B is a functional and handsome Gents watch. Case material is Stainless Steel and the Blue dial gives the watch that unique look. The features of the watch include (among others) a date function. This watch is market as water resistant. It means it can withstand slight splashes and rain, but is NOT to be immersed in water. We ship it with an original box and a guarantee from the manufacturer.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Guess W0799G4',275.40,'Polished silver and gold case with crystal detailing, sunray white glitz multi-functional dial, polished silver and gold bracelt with crystals.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Accurist MB933S',80.84,'This Men''s watch by Accurist is made from PVD gold plated steel and has a 42mm case with a silver dial. The dial features 3 mini dials, chronograph, date function and gold baton hour markers with gold hands. This sought after model is 50m water resistant and powered by a quality quartz movement. It fastens with a gold metal bracelet.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Swatch GB743',46.43,'The ever popular Swatch Once Again watch for men, proving that simple is always effective. This time piece in monochrome style comes with a water resistant resin case, with a reliable Swiss quartz movement with day / date function embedded into it, and an easy change battery cover. It has a minimal white dial with easy to read numeral hour markers in black, and a date magnification bubble in the acrylic glass. This model fastens with a black plastic strap.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Limit 5484.01',34.67,'Limit 5484.01 is an amazing and attractive Gents watch. Material of the case is PVD rose plating and the Black dial gives the watch that unique look. The features of the watch include (among others) a date function. In regards to the water resistance, the watch has got a resistancy up to 30 metres. It means it can be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'Bulova 97A138',405.10,'Sophisticated design with full exhibition dial and case-back. Gold-tone stainless steel screw-back case, skeletonized silver white three-hand dial revealing the intricate workings of the self-winding 21-jewel movement, domed mineral crystal, brown leather strap with push-button deployant buckle, and water resistance to 30 meters. Diameter: 43mm Thickness: 12.15mm',0,350,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (1,'G-Shock GWG-1000-1A1ER',766.08,'Casio G-Shock Premium Mudmaster Compass GWG-1000-1A1ER is an amazing and very impressive Gents watch. Case material is Stainless Steel and Resin, which stands for a high quality of the item while the dial colour is Black. The features of the watch include (among others) a chronograph and date function as well as an alarm. This model has got 200 metres water resistancy - it can be used for professional marine activity, skin diving and high impact water sports, but not deep sea or mixed gas diving. The watch is shipped with an original box and a guarantee from the manufacturer.',0,350,True);

/* Electronics */
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'SanDisk Ultra 32GB,',10.00,'microSDHC Memory Card + SD Adapter with A1 App Performance up to 98MB/s, Class 10, U1',0,17,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'4-Port USB 3.0 Hub',9.99,'Compatible for USB A Devices, 5Gbps Transfer Speed',1,15,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'COWIN E7,',45.89,'Noise Cancelling Bluetooth Headphones with Microphone Hi-Fi Deep Bass Wireless Headphones Over Ear',0,41,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'Mighty Rock 6110 ',25.49,'Bluetooth Speakers Portable Wireless Speaker with 16W Rich Deep Bass',0,35,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'Okun HD50MV',59,'Earbuds with Metal Housing Heavy Deep Bass Comfort-Fit',0,11,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'Anker Bluetooth Speaker',30,'Super Portable Speaker with 15-Hour Playtime',0,27,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'Mpow Wireless Headphones',22,' Up to 9 Hrs Playing Time IPX7 Waterproof Running Headphones In-ear Earbuds for Gym Cycling Workout',0,42,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'YOSH Car Phone Holder',54,'Vent Phone Holder for Car Cradle Mount for Cellphones',0,46,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'Jiayou Earphones',62,'Pure Sound and Powerful Bass compatible with Samsung Huawei Honor Mi with Volume Control and Microphone',1,6,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (2,'Earbuds IPX7',37,'Thanks to the most advanced Bluetooth 5.0 technology and antenna technology, TEMINICE M5 wireless headphones provides fast and stable transmission, 33-66ft bluetooh range, never worry about the connection issues. Save your money and hassle.',1,48,True);

/* Software */
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'Kaspersky Total Security 2019',7,'Kaspersky Total Security 2019 3 Devices 2 Years includes antivirus and firewall to protect your devices from viruses, attacks and malware',0,21,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'McAfee 2019',39,'McAfee 2019 total protection includes award-winning antivirus, blocks viruses, malware, ransomware, spyware, unwanted programs and more on your PC',0,38,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'Rescue your Videotapes',83,'Digitize and dub VHS: Complete package including software and hardware',0,39,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'Norton Security Deluxe',38,'Reliable data protection against cybercrime at home: Norton antivirus and firewall software provides security and protection against malware and viruses for up to 5 PCs, Macs, smartphones and tablets',0,18,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'McAfee Internet Security',81,'Stay safe from Trojans, viruses, spyware, rootkits, and more, with state-of-the-art anti-malware protection Detects, quarantines, and blocks viruses and malware to prevent damage to your PC Keep zero-day threats and botnets at bay-McAfee Active Protection and Global Threat Intelligence technology have you covered',0,46,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'BullGuard Internet Security',68,'All in one solution that protects PC''s, Mac and Android devices so you can shop, game and bank online with absolute confidence',0,28,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'AVG Internet Security ',93,'AVG Internet Security includes Internet Security for Windows, AntiVirus for Android and AntiVirus for Mac',1,21,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'ESET Security',52,'Antivirus and Antispyware - Provides all-round protection against all types of threats, including Viruses, Rootkits and Spyware',1,32,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'BullGuard Internet Security',83,'Antivirus - effectively catches all viruses',0,7,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (3,'Avast',26,'Intelligent antivirus - Stay safe from viruses, ransomware, trojans, and other types of malware.',0,24,True);

/* Pet Supplies */
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'Lintbells YuMOVE',54,'YuMOVE is the UK''s no.1 veterinary joint supplement (Kynetec Vet Trak Sales Data, MAT Values - January 2019)',1,12,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'Rocks Urine',79,'SAVE YOUR LAWN: Dog Rocks will help stop those unsightly urine burn patches ruining your luscious green lawn and shrubs. Your dog can save your lawn! Simply add a 200g pack of Dog Rocks for every 2 litres of water in the bowl and replace the rocks every 2 months. You can then sit back and enjoy your greener than green lawn knowing that no new burn patches will occur from 8-10 hours. With proper lawn maintenance, you will see vast improvements in 3-5 weeks.',1,28,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'SmugPets Premium.',82,'EXTRA STRONG DOG POOP BAGS: What makes these 5 star rated dog waste bags so popular is that they have a thickness rating of 19 microns. This makes them 50% stronger and more reliable than other waste bags on the market.',0,48,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'Idepet Dog Toy Ball',56,'NONTOXIC MATERIAL : the product is made by extra-tough rubber, good elasticity, bite-resistant and nontoxic,non-abrasive,safe for your sweet dog grinding and cleaning their teeth.',0,37,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'Front Seat',57,'Protects Your Car seat from Pet Hairs, Water & Mud etc - Also Suitable for Other Messy Loads',0,20,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'First Aid Kit',24.99,'Pet First Aid Kit in a durable, compact and lightweight design',0,45,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'Ear Cleaner',13.99,'DELIVERS RESULTS QUICKLY -Use For Excessive Head Shaking, Persistent Itching, Waxy Ears & Ear Odour. Scientifically Formulated to Clean Both Ears and Maintain Them in Good Condition.',0,46,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'Dog Clippers.',20.99,'This pet grooming kit come with 6 kinds of tools: 1× Pet clipper (including a rechargeable battery),1×Power Adapter,1× Cleaning Brush, 6× Comb Attachments (3/6/9/12mm/ Left /Right Oblique),1× Stainless Steel Scissor,1× Stainless Steel Comb. Pet clippers fit for all HAIRY PETS!',0,5,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'Collapsible Dog Bowls',4.95,'PORTABLE & CONVENIENT: Collapsible dog pet cat bowl is 5.1 inches wide, 2.1 inches height, each bowl holds up to 12 fluid ounces of water or 1.5 cups of dog food. - 0.5 inches when compact - simply Pop-Up and then fold away',0,44,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (4,'Pet Carrier Crate',75,'Mool''s large sized black pet crate is ideal for travelling, camping or around the house to provide your pet with a luxurious place to rest',0,40,True);

/* Video Games */
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'The Division 2',38.99,'In the wake of the virus, storms, flooding, and subsequent chaos have radically transformed Washington, D.C. Explore a living open world full of diverse environments, from flooded urban areas to historic sites and landmarks, during one of the hottest summers in history',0,35,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'Assassins Creed Odyssey',23.99,'Become a legendary Greek hero - In a first for the Assassin''s Creed franchise, you can choose which hero to embody throughout this epic journey, Alexios or Kassandra',0,50,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'Spider-Man',35.98,'Be Spider-Man - An experienced Spider-Man with several years of crime fighting under his belt, Peter Parker has sheer mastery of his powerful spider-sense, dynamic skills, acrobatic abilities, and new suit.',0,8,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'God of War (PS4)',44.99,'A New Beginning - His vengeance against the gods of Olympus far behind him, Kratos now lives as a man in the lands of Norse Gods and monsters. It is in this harsh, unforgiving world that he must fight to survive... and teach his son to do the same',0,4,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'Devil May Cry 5 (PS4)',39.99,'Nero, one of the series main protagonists and a young demon hunter who has the blood of Sparda, heads to Red Grave City to face the hellish onslaught of demons, with weapons craftswoman and new partner-in-crime, Nico',1,7,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'Mortal Kombat 11 (PS4)',49.99,'Featuring a roster of new and returning Klassic Fighters, Mortal Kombat''s best in class cinematic story mode continues the epic saga over 25 years in the making',1,41,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'World War Z (PS4)',34.99,'Battle swarms of hundreds of zombies - the Swarm Engine seamlessly renders hordes of zombies in incredible firefights. Advanced gore systems offer gruesomely satisfying action.',1,43,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'Borderlands 3',74.99,'Stop the fanatical Calypso Twins from uniting the bandit clans and claiming the galaxy',0,12,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'Pokemon Sword',54.99,'New entries in the main series of Pokémon RPGs arrive on Nintendo Switch simultaneously in late 2019',1,45,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (5,'Super Mario Maker 2',44.99,'Super Mario Maker 2, the sequel to Super Mario Maker, launches exclusively for Nintendo Switch this June. Let your imagination run wild with new tools, course parts, and features as you create the Super Mario courses of your dreams.',1,26,True);

/* Art */

INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'vulputate,',50,'risus. Morbi metus. Vivamus euismod urna. Nullam',0,36,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'nunc',6,'fermentum convallis',0,40,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'sed,',32,'dolor quam, elementum at, egestas a, scelerisque sed, sapien.',1,5,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'massa',32,'hendrerit a, arcu. Sed et libero. Proin mi.',0,6,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'lacus.',10,'mollis dui, in sodales elit erat vitae risus. Duis',0,20,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'est,',47,'leo elementum sem, vitae aliquam eros turpis non',1,43,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'massa.',40,'diam',0,37,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'mollis',11,'Donec porttitor tellus non',0,17,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'orci,',80,'arcu. Vestibulum',1,50,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (6,'morbi',22,'porttitor vulputate, posuere vulputate,',1,19,True);


/* Home */

INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'Integer',49,'et ipsum cursus vestibulum. Mauris magna. Duis dignissim tempor arcu.',1,24,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'Etiam',28,'Etiam vestibulum massa rutrum magna. Cras convallis',1,4,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'vestibulum,',28,'mi eleifend egestas. Sed pharetra, felis eget varius ultrices, mauris',1,34,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'sociis',14,'Aenean eget metus. In nec orci.',1,37,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'accumsan',84,'Donec tempor, est ac mattis semper,',1,12,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'ante.',20,'consectetuer adipiscing elit.',1,46,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'nonummy',68,'sed, est.',0,41,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'lacus.',10,'tempor diam dictum',1,8,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'massa.',70,'euismod et, commodo at, libero. Morbi accumsan laoreet',1,48,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (7,'vehicula',34,'congue turpis.',1,21,True);


/* Books */

INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'ut,',72,'in consectetuer ipsum nunc id',1,1,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'Quisque',49,'eu metus. In lorem. Donec elementum,',1,17,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'lobortis',3,'mauris ipsum porta elit, a feugiat tellus',1,14,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'aliquam,',62,'Maecenas ornare egestas ligula. Nullam feugiat',0,24,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'eu',57,'semper auctor.',1,35,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'consequat',14,'non justo. Proin non massa non ante bibendum ullamcorper.',0,44,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'egestas',29,'neque. Sed eget lacus. Mauris non dui nec urna',1,37,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'mus.',60,'arcu',1,50,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'vehicula',47,'vitae sodales nisi magna sed dui.',1,7,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (8,'ornare,',22,'Morbi metus. Vivamus euismod urna. Nullam lobortis quam',1,43,True);


/* Beauty */

INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'non,',67,'urna',1,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'lorem,',97,'venenatis vel, faucibus id, libero. Donec consectetuer mauris id',1,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'dolor,',77,'eleifend vitae, erat. Vivamus nisi. Mauris nulla. Integer urna. Vivamus',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'Suspendisse',23,'at, iaculis quis, pede. Praesent eu dui. Cum sociis natoque',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'nec,',82,'lacus. Aliquam rutrum lorem ac risus. Morbi metus. Vivamus euismod',1,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'ornare',65,'orci. Ut semper pretium neque. Morbi quis urna. Nunc quis',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'inceptos',68,'vitae, posuere at, velit. Cras lorem lorem,',1,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'tristique',82,'conubia nostra, per inceptos hymenaeos. Mauris ut',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'Suspendisse',88,'aliquet, sem ut cursus luctus, ipsum leo',1,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (9,'ut,',27,'metus urna convallis erat,',0,500,True);


/* Toys and Games */

INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'et',31,'ligula. Aliquam',1,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'pretium',30,'metus eu erat semper rutrum.',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'dapibus',10,'ornare, elit elit fermentum risus, at fringilla purus mauris a',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'et,',47,'luctus aliquet odio.',1,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'metus',45,'nunc. Quisque ornare tortor at risus. Nunc ac',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'Donec',5,'Phasellus in felis. Nulla',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'tortor.',11,'Vivamus nisi. Mauris nulla. Integer',0,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'non',31,'posuere at, velit. Cras lorem',1,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'ac,',80,'dolor, nonummy ac, feugiat non,',1,500,True);
INSERT INTO products (id_category,name,price,description,discount,stock,is_enabled) VALUES (10,'lacus.',89,'sed sem egestas blandit. Nam',1,500,True);


/* USERS */
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('AdminMaster','inceptos@laoreet.co.uk','parturient',True,True,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('moviecookie','Suspendisse.sagittis@sapien.co.uk','Maecenas',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('termeditor','lacinia.Sed@scelerisquescelerisque.com','est',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('trapezoidroll','tincidunt.pede.ac@Maurisblandit.com','magnis',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('surfacingblird','semper.dui@nisiCum.edu','euismod',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('andreev','ante.blandit@pulvinararcu.org','iaculis',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('art','magna.sed@orci.com','non',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('belousov','euismod.in.dolor@tortoratrisus.com','magnis',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('billing','tempus.eu.ligula@massa.edu','Aliquam',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('bogdanov','erat.eget.tincidunt@ipsumDonec.com','dui.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('buhgalter','Fusce@Vivamus.ca','vulputate',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('ALFREDO','Mauris@commodo.net','in',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Landon','Mauris.molestie@auctornunc.edu','erat',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Christopher','commodo@tristiqueneque.ca','augue',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('John','mauris.rhoncus@metusfacilisislorem.com','eu,',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Samuel','magna@tincidunttempusrisus.org','lobortis',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Daniel','nec.tempus@augueeu.net','non',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Alexander','ullamcorper@ipsumSuspendissesagittis.co.uk','posuere,',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('James','scelerisque@lorem.org','molestie',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Cras','tempor@vehicularisusNulla.com','Ut',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Jayden,','nascetur.ridiculus.mus@lorem.co.uk','turpis',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Jacob','id.mollis@tinciduntpede.net','bibendum',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Anthony','sollicitudin.adipiscing.ligula@auctorvitae.co.uk','elementum',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Joshua','et@feugiat.org','commodo',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Michael','orci.luctus.et@sollicitudinadipiscing.co.uk','luctus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Noah','id.nunc@egetmetus.edu','sollicitudin',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Nicholas','dictum.mi.ac@tristique.com','metus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Gavin','enim@tinciduntneque.org','mattis',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Proin','risus@rutrum.ca','Cras',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Logan','pharetra@Vivamussitamet.com','accumsan',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Larry,','molestie.orci.tincidunt@commodoatlibero.ca','ridiculus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Justin','dui.Cras@convallis.ca','Nullam',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Scott','egestas@pede.ca','placerat',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Brandon','Sed.nulla@egestasAliquamnec.com','Nulla',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Frank','congue.a.aliquet@urnaconvalliserat.org','ac',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Benjamin','euismod.et.commodo@egetmollislectus.net','ipsum',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Gregory','Nullam.enim.Sed@fringillaest.com','id',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Raymond','eget.tincidunt@facilisis.org','mi.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Samuel2','ante.dictum@acliberonec.com','pharetra',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Patrick','ultricies@ametconsectetueradipiscing.ca','tempor',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Alexander2','nunc.risus@facilisismagnatellus.co.uk','metus.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Jack,','molestie@consequatdolor.net','posuere',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Suspendisse','parturient.montes@volutpat.com','Donec',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Dennis','enim@Phasellusdolor.org','ligula.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Jerry','Integer.in.magna@inlobortistellus.org','tincidunt',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Tyler','erat.Vivamus.nisi@felispurusac.edu','Phasellus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Aaron','ut@dolor.net','mauris',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Henry','Cras.convallis@necanteMaecenas.com','ligula.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Jose','feugiat@magnaa.edu','Integer',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Douglas','nec@elitafeugiat.edu','Nunc',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Peter,','eu@Etiam.edu','tempor',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Adam','a.magna.Lorem@pedeultrices.edu','eu,',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Phasellus','nonummy.ut@acmattis.ca','augue.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Nathan','gravida.sagittis.Duis@loremauctorquis.edu','penatibus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Aliquam','Curae@aliquetlibero.co.uk','nibh',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Zachary,','Mauris.molestie@felis.org','interdum.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('interdum','ornare.sagittis.felis@nonloremvitae.org','auctor',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Walter.','nonummy.ac.feugiat@feugiatnec.net','lorem,',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Kyle','mus@nonenim.ca','quis,',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Harold','lacus.Quisque@velarcuCurabitur.co.uk','in,',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Carl','Vivamus@ullamcorper.com','sem',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('adipiscing','nec@sit.edu','massa.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Jeremy','Aenean.euismod@sagittislobortis.com','fringilla',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Gerald','condimentum.Donec@volutpat.ca','lorem',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Keith','ante.Vivamus.non@duiCum.co.uk','penatibus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Roger','velit@elit.edu','purus.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Arthur','lectus@velmauris.com','Duis',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Terry','hendrerit@dolordapibus.co.uk','eu',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Lawrence','semper.auctor@eratsemper.net','quam,',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Fusce','pede.malesuada.vel@felisDonectempor.org','id',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Sean','dictum.augue@sedconsequat.org','Sed',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Mauris','ipsum@facilisis.ca','Quisque',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('primis','nec@euismodacfermentum.com','',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('sem','nec.ante.blandit@aultricies.edu','Nam',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Ethan','quis.urna@nondapibusrutrum.co.uk','facilisi.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('tellus','Aliquam@magnaUttincidunt.org','lacus.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('orci','et@mattis.edu','tortor',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('senectus','penatibus.et@Quisque.net','ipsum',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('dictum','Donec@aliquam.com','condimentum',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('malesuada','orci@arcu.com','aliquet',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Austin','Aliquam.vulputate.ullamcorper@lectus.co.uk','at',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('aliquet,','orci.luctus.et@Aliquam.co.uk','dapibus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('vitae','odio.Nam@ametdiameu.edu','molestie',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Joe','dolor@odio.net','et',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('habitant','turpis.In.condimentum@diam.org','velit.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('congue.','at.nisi@felis.org','in,',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('cursus','litora.torquent.per@leo.edu','rhoncus.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Cras2','augue.ac@parturient.ca','Phasellus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Sed','luctus.Curabitur@magna.ca','dictum',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Jesse','ipsum.ac.mi@Nunc.co.uk','Nulla',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('viverra','at.nisi.Cum@feugiatnecdiam.co.uk','Sed',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('natoque','tellus.eu@purus.org','tempus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('tristique','sodales.nisi@Duisdignissim.net','pharetra.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Curabitur','Integer.in@nonenimMauris.co.uk','eros',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('aliquet','Curabitur.vel.lectus@consectetueripsumnunc.co.uk','erat.',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('bibendum.','dictum@anteiaculisnec.co.uk','tellus',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('lobortis.','hendrerit.a@eget.org','turpis',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Willie','Aliquam.fringilla.cursus@Quisqueliberolacus.com','turpis',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('consectetuer','in@nec.org','eget,',False,False,True);
INSERT INTO users (username,email,password,is_staff_member,is_admin,is_enabled) VALUES ('Billy.','Phasellus.ornare@Praesentinterdum.com','gravida.',False,False,True);
INSERT INTO users (username,password,is_staff_member,is_admin,is_enabled) VALUES ('admin','$2y$12$m0HQd2AFi/bT9/zyGcScb.tTW6vIHvOnWQPG6dozPcVn0/4synCtu',True,True,True);
INSERT INTO users (username,password,is_staff_member,is_admin,is_enabled) VALUES ('psilva','$2y$12$mOlFqoaBtLkMmPy/sMPATOaNuFIQYbjXHgTKyoU9UHlHJCN8Y3.M2',False,False,True);


/* WHISHLIST */
INSERT INTO wishlists (id_product,id_client) VALUES (31,2);
INSERT INTO wishlists (id_product,id_client) VALUES (15,77);
INSERT INTO wishlists (id_product,id_client) VALUES (3,49);
INSERT INTO wishlists (id_product,id_client) VALUES (9,35);
INSERT INTO wishlists (id_product,id_client) VALUES (12,16);
INSERT INTO wishlists (id_product,id_client) VALUES (47,32);
INSERT INTO wishlists (id_product,id_client) VALUES (6,89);
INSERT INTO wishlists (id_product,id_client) VALUES (12,33);
INSERT INTO wishlists (id_product,id_client) VALUES (8,80);
INSERT INTO wishlists (id_product,id_client) VALUES (6,28);
INSERT INTO wishlists (id_product,id_client) VALUES (9,11);
INSERT INTO wishlists (id_product,id_client) VALUES (8,54);
INSERT INTO wishlists (id_product,id_client) VALUES (13,49);
INSERT INTO wishlists (id_product,id_client) VALUES (6,57);
INSERT INTO wishlists (id_product,id_client) VALUES (17,48);
INSERT INTO wishlists (id_product,id_client) VALUES (9,2);
INSERT INTO wishlists (id_product,id_client) VALUES (8,56);
INSERT INTO wishlists (id_product,id_client) VALUES (24,8);
INSERT INTO wishlists (id_product,id_client) VALUES (7,10);
INSERT INTO wishlists (id_product,id_client) VALUES (7,84);
INSERT INTO wishlists (id_product,id_client) VALUES (9,55);
INSERT INTO wishlists (id_product,id_client) VALUES (7,17);
INSERT INTO wishlists (id_product,id_client) VALUES (48,45);
INSERT INTO wishlists (id_product,id_client) VALUES (21,35);
INSERT INTO wishlists (id_product,id_client) VALUES (2,96);
INSERT INTO wishlists (id_product,id_client) VALUES (18,41);
INSERT INTO wishlists (id_product,id_client) VALUES (7,77);
INSERT INTO wishlists (id_product,id_client) VALUES (16,38);
INSERT INTO wishlists (id_product,id_client) VALUES (7,52);
INSERT INTO wishlists (id_product,id_client) VALUES (47,34);
INSERT INTO wishlists (id_product,id_client) VALUES (29,86);
INSERT INTO wishlists (id_product,id_client) VALUES (19,73);
INSERT INTO wishlists (id_product,id_client) VALUES (10,34);
INSERT INTO wishlists (id_product,id_client) VALUES (8,86);
INSERT INTO wishlists (id_product,id_client) VALUES (5,60);
INSERT INTO wishlists (id_product,id_client) VALUES (9,72);
INSERT INTO wishlists (id_product,id_client) VALUES (4,60);
INSERT INTO wishlists (id_product,id_client) VALUES (6,91);
INSERT INTO wishlists (id_product,id_client) VALUES (31,46);
INSERT INTO wishlists (id_product,id_client) VALUES (9,69);
INSERT INTO wishlists (id_product,id_client) VALUES (6,76);
INSERT INTO wishlists (id_product,id_client) VALUES (9,38);
INSERT INTO wishlists (id_product,id_client) VALUES (21,26);
INSERT INTO wishlists (id_product,id_client) VALUES (34,3);
INSERT INTO wishlists (id_product,id_client) VALUES (5,72);
INSERT INTO wishlists (id_product,id_client) VALUES (13,84);
INSERT INTO wishlists (id_product,id_client) VALUES (8,67);
INSERT INTO wishlists (id_product,id_client) VALUES (3,73);
INSERT INTO wishlists (id_product,id_client) VALUES (3,75);
INSERT INTO wishlists (id_product,id_client) VALUES (24,55);
INSERT INTO wishlists (id_product,id_client) VALUES (6,29);
INSERT INTO wishlists (id_product,id_client) VALUES (5,68);
INSERT INTO wishlists (id_product,id_client) VALUES (18,79);
INSERT INTO wishlists (id_product,id_client) VALUES (48,87);
INSERT INTO wishlists (id_product,id_client) VALUES (50,6);
INSERT INTO wishlists (id_product,id_client) VALUES (3,22);
INSERT INTO wishlists (id_product,id_client) VALUES (50,97);
INSERT INTO wishlists (id_product,id_client) VALUES (43,21);
INSERT INTO wishlists (id_product,id_client) VALUES (5,45);
INSERT INTO wishlists (id_product,id_client) VALUES (9,28);
INSERT INTO wishlists (id_product,id_client) VALUES (43,82);
INSERT INTO wishlists (id_product,id_client) VALUES (7,20);
INSERT INTO wishlists (id_product,id_client) VALUES (9,1);
INSERT INTO wishlists (id_product,id_client) VALUES (5,16);
INSERT INTO wishlists (id_product,id_client) VALUES (33,95);
INSERT INTO wishlists (id_product,id_client) VALUES (46,38);
INSERT INTO wishlists (id_product,id_client) VALUES (18,40);
INSERT INTO wishlists (id_product,id_client) VALUES (45,30);
INSERT INTO wishlists (id_product,id_client) VALUES (1,21);
INSERT INTO wishlists (id_product,id_client) VALUES (5,25);
INSERT INTO wishlists (id_product,id_client) VALUES (5,80);
INSERT INTO wishlists (id_product,id_client) VALUES (22,14);
INSERT INTO wishlists (id_product,id_client) VALUES (44,62);
INSERT INTO wishlists (id_product,id_client) VALUES (11,99);
INSERT INTO wishlists (id_product,id_client) VALUES (34,93);
INSERT INTO wishlists (id_product,id_client) VALUES (38,27);
INSERT INTO wishlists (id_product,id_client) VALUES (5,98);
INSERT INTO wishlists (id_product,id_client) VALUES (7,40);
INSERT INTO wishlists (id_product,id_client) VALUES (2,54);
INSERT INTO wishlists (id_product,id_client) VALUES (42,77);
INSERT INTO wishlists (id_product,id_client) VALUES (4,44);
INSERT INTO wishlists (id_product,id_client) VALUES (2,12);
--INSERT INTO wishlists (id_product,id_client) VALUES (51,23);
INSERT INTO wishlists (id_product,id_client) VALUES (8,1);
INSERT INTO wishlists (id_product,id_client) VALUES (36,83);
INSERT INTO wishlists (id_product,id_client) VALUES (5,48);
INSERT INTO wishlists (id_product,id_client) VALUES (8,10);
INSERT INTO wishlists (id_product,id_client) VALUES (24,22);
INSERT INTO wishlists (id_product,id_client) VALUES (3,69);
INSERT INTO wishlists (id_product,id_client) VALUES (8,53);
INSERT INTO wishlists (id_product,id_client) VALUES (2,93);
INSERT INTO wishlists (id_product,id_client) VALUES (19,14);
INSERT INTO wishlists (id_product,id_client) VALUES (6,100);
INSERT INTO wishlists (id_product,id_client) VALUES (39,11);
INSERT INTO wishlists (id_product,id_client) VALUES (5,20);
INSERT INTO wishlists (id_product,id_client) VALUES (5,92);
INSERT INTO wishlists (id_product,id_client) VALUES (37,26);
INSERT INTO wishlists (id_product,id_client) VALUES (48,84);
INSERT INTO wishlists (id_product,id_client) VALUES (5,43);

/* CART */
INSERT INTO carts (id_product,id_client,quantity) VALUES (36,3,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (49,81,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (42,17,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (39,81,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (36,79,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (45,67,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (2,86,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (48,94,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (4,56,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (41,86,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (28,24,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (42,65,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (14,41,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (36,75,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (31,61,1);
--INSERT INTO carts (id_product,id_client,quantity) VALUES (52,12,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (39,8,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (32,65,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (8,38,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (37,53,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (36,71,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (36,57,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (30,12,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (31,26,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (38,66,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (32,83,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (36,16,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (31,45,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (29,86,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (20,3,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (28,99,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (24,71,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (10,79,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (20,4,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (28,58,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (20,60,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (49,28,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (13,53,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (13,2,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (7,40,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (43,42,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (35,44,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (36,48,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (16,18,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (18,90,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (13,68,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (10,16,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (8,2,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (2,19,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (22,49,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (24,5,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (46,37,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (37,19,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (33,93,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (31,57,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (26,60,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (37,33,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (13,87,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (10,100,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (17,15,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (11,72,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (43,16,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (17,92,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (29,79,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (31,87,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (35,58,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (32,28,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (44,11,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (5,13,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (41,75,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (3,22,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (4,93,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (10,1,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (43,99,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (44,73,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (41,18,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (23,100,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (15,93,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (18,82,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (7,59,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (8,5,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (8,30,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (5,53,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (6,91,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (3,36,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (9,12,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (5,98,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (10,5,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (33,3,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (23,32,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (6,81,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (49,44,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (4,35,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (24,17,1);
INSERT INTO carts (id_product,id_client,quantity) VALUES (2,57,3);
INSERT INTO carts (id_product,id_client,quantity) VALUES (42,81,4);
INSERT INTO carts (id_product,id_client,quantity) VALUES (5,96,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (17,33,2);
INSERT INTO carts (id_product,id_client,quantity) VALUES (6,7,5);
INSERT INTO carts (id_product,id_client,quantity) VALUES (7,19,4);

/* BILLING INFORMATION */
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (1,25,'Kareem Ayers','Ap #574-5262 Parturient Ave','Iquique','Tarapacá','4355 RD');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (2,10,'Hedy Cortez','7994 Non Ave','Papasidero','CAL','87967-045');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (3,20,'Reagan Pitts','P.O. Box 342, 4969 Egestas Street','Bremerhaven','HB','91816-268');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (4,40,'Abigail Smith','P.O. Box 238, 122 Mauris Street','Osasco','SP','67530');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (5,7,'Jerry Mosley','2892 Ut, Ave','Kalisz','Wielkopolskie','A1Z 8R6');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (6,33,'Marvin Rollins','8933 Consequat Road','Quilicura','Metropolitana de Santiago','P0B 3X5');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (7,17,'Dylan Miles','Ap #484-8450 Tellus Avenue','Rampur','Uttar Pradesh','8606');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (8,21,'Brittany Duncan','7514 Erat. Av.','Castelló','Comunitat Valenciana','63977');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (9,28,'Warren Phillips','P.O. Box 677, 7233 Leo, Rd.','Ashburton','South Island','CI2 3PO');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (10,37,'Haley Langley','P.O. Box 843, 7269 Elit. St.','Capena','Lazio','41971');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (11,17,'Charity Petty','9882 Lacinia Rd.','Pozantı','Adana','603039');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (12,25,'Len Jacobson','458-6713 Nec Avenue','Södertälje','Stockholms län','70601');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (13,9,'Orla Mills','425-4070 Ac Av.','Galway','C','50958');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (14,36,'Hasad Oneill','P.O. Box 264, 991 Luctus St.','Vienna','Vienna','928272');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (15,42,'Barrett Price','676-5089 Interdum. Rd.','Rochester','Minnesota','7170');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (16,31,'Rebecca Hutchinson','955-2756 Imperdiet Av.','Hamburg','HH','8844');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (17,5,'Rebecca Christensen','7526 Sem Rd.','Tresigallo','Emilia-Romagna','459914');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (18,2,'Hyacinth Sykes','3075 Consectetuer St.','Perpignan','Languedoc-Roussillon','342083');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (19,37,'Aretha Mcdaniel','Ap #160-4865 Sollicitudin Ave','Mesa','Arizona','9693');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (20,43,'Nash Sims','P.O. Box 715, 4600 Cursus Avenue','Tarnów','Małopolskie','31185');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (21,21,'Marcia Ross','P.O. Box 477, 2392 Metus. Rd.','Kirriemuir','Angus','856598');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (22,39,'Tiger Weeks','6546 In Road','Spijkenisse','Z.','27611');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (23,18,'Chaim Aguilar','391 Mauris Avenue','Uddevalla','Västra Götalands län','6314');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (24,2,'Kyle Schmidt','P.O. Box 357, 2460 Varius Road','Le Cannet','PR','78440');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (25,22,'Nathan Herrera','P.O. Box 172, 1407 Non Road','Auckland','North Island','919310');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (26,36,'Warren Oliver','286-9443 Mi Rd.','Galway','C','674334');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (27,38,'Graham Vega','883-6421 Mauris Av.','Hudiksvall','Gävleborgs län','903106');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (28,27,'Melissa Hewitt','Ap #255-7902 Eu, Ave','Waalwijk','Noord Brabant','WO1 8DI');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (29,46,'Mufutau Langley','849-5933 Ut St.','Norfolk','VA','54437');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (30,23,'Jaime Goodman','615-4489 Nec Road','Aberystwyth','CG','X49 8NK');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (31,15,'Teegan Sandoval','Ap #738-1379 Velit. Road','StrŽe','Henegouwen','50011');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (32,38,'Hillary Cortez','564-2674 Felis St.','Clermont-Ferrand','AU','327509');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (33,14,'Cally Obrien','Ap #243-471 Class Road','Weyburn','SK','5295');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (34,49,'Jaime Jenkins','Ap #805-3970 Cras Avenue','Invergordon','Ross-shire','89051');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (35,50,'Ora Brown','389-4498 Mus. St.','Oakham','Rutland','96318');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (36,45,'Keelie Branch','Ap #795-6302 Accumsan Ave','Galway','Connacht','99313');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (37,24,'Sybil Carter','P.O. Box 283, 9908 Egestas Rd.','Portland','Maine','4023');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (38,48,'Candice Chang','435-296 Tortor Road','Warri','Delta','63825-476');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (39,40,'Barry Barker','P.O. Box 704, 3767 Nisl. Ave','Pamplona','NA','2224');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (40,12,'Dorian Dorsey','8529 Nascetur Avenue','Lerwick','SH','754280');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (41,32,'Buffy Mendez','Ap #626-9295 Leo, Av.','Worcester','WO','89449');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (42,16,'Brynne Weaver','P.O. Box 156, 3087 Egestas Road','Sevilla','AN','668415');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (43,12,'Jeanette Holloway','P.O. Box 985, 6365 Vulputate, Rd.','Anchorage','AK','60327');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (44,36,'Teegan Drake','659-4383 Etiam Rd.','Cinco Esquinas','SJ','79970');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (45,25,'Ursula Lott','Ap #426-4845 Eget, Av.','Lexington','Kentucky','49069');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (46,29,'Kiona Lindsey','P.O. Box 206, 1538 Risus. Rd.','Butte','Montana','75127');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (47,15,'Jelani Goodwin','P.O. Box 234, 3016 Ac Avenue','Lerum','O','12499');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (48,3,'Marshall Hopper','P.O. Box 557, 2834 Elit, Street','Sacramento','California','L3S 2E5');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (49,47,'Gretchen Stone','Ap #846-8741 Ultrices Road','Warren','MI','320732');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (50,44,'Reese Murray','Ap #391-3564 Nam Rd.','Christchurch','South Island','9610');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (51,5,'Richard Wilkinson','P.O. Box 985, 9438 Augue St.','Belfast','U','1693');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (52,8,'Echo Williamson','Ap #864-7791 Cras Av.','Redruth','Cornwall','2948');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (53,42,'Troy Mercado','P.O. Box 144, 1437 Molestie Avenue','Nakusp','BC','9236');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (54,20,'Fredericka Grimes','P.O. Box 369, 4270 Nec St.','Vienna','Vienna','38654');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (55,19,'Ray Rosario','635-7872 Tellus. Street','Sagrada Familia','VII','75232');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (56,5,'Josiah Norris','2163 Parturient Av.','Falun','Dalarnas län','497701');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (57,34,'Chantale Montoya','P.O. Box 543, 6215 Lectus. St.','Meppel','Dr','3250');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (58,42,'Alana Good','Ap #485-4851 Aliquam Rd.','Upplands Väsby','AB','70116');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (59,29,'Fatima Chase','Ap #882-7512 Eleifend Street','Braunschweig','NI','C8K 1A5');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (60,5,'Orla Hubbard','8562 Scelerisque Avenue','Ponte nelle Alpi','VEN','28545');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (61,10,'Ariel Snow','9119 Nisl. St.','Bodmin','CO','58206');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (62,40,'Callum Snider','Ap #205-8002 Faucibus Avenue','Mora','Dalarnas län','5322');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (63,29,'MacKensie Campos','P.O. Box 579, 5707 Tempus Road','Paularo','Friuli-Venezia Giulia','23-919');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (64,14,'Fletcher Mccormick','Ap #241-3482 Nam Ave','Gravataí','RS','1121');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (65,36,'Griffin Bullock','842-6851 Sodales Av.','Pangnirtung','NU','68009');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (66,9,'Kareem Morse','Ap #295-6675 Lacus. Avenue','Lagos','Lagos','35720');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (67,5,'Savannah Terrell','Ap #562-627 Dignissim Street','Créteil','Île-de-France','75175');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (68,13,'Eaton Chan','463-4353 A, Street','Detroit','MI','70-761');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (69,17,'Carlos Rowland','P.O. Box 922, 7385 Sit Rd.','Trazegnies','HE','9382 BR');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (70,22,'Keely Martinez','234-8447 Ut St.','Konin','Wielkopolskie','3502 JO');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (71,28,'Rafael Stevenson','4260 Pretium Rd.','Vienna','Wie','71775');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (72,48,'Serina Salas','P.O. Box 750, 9038 Ac Street','Jundiaí','São Paulo','16455-792');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (73,4,'Leigh Garrett','4220 Feugiat Ave','Dublin','Leinster','6156');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (74,26,'Xenos Flynn','Ap #456-1306 Pede, Ave','Versailles','Île-de-France','1411');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (75,4,'Inez Atkins','P.O. Box 125, 1579 Parturient St.','Liverpool','New South Wales','4322');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (76,34,'Wylie Ramos','1703 Montes, St.','San Antonio','TX','8564 MM');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (77,18,'Chase Hodge','2692 Vivamus Ave','Kano','Kano','52290');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (78,28,'Blaze Mcclain','Ap #876-5150 Eget Rd.','Granada','AN','63148');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (79,13,'Julie Price','Ap #447-3544 Est Avenue','Pudukkottai','TN','9015');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (80,15,'Gannon Brown','138-1831 Congue, Street','Dublin','Leinster','61343');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (81,33,'Kirk Orr','3772 Luctus Avenue','Paris','Île-de-France','207844');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (82,4,'Anjolie Cameron','1141 Placerat, Rd.','Diadema','São Paulo','8195');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (83,23,'Medge Jensen','Ap #554-971 Nonummy Street','Bremerhaven','HB','5615');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (84,43,'Candice Jones','7919 Auctor Road','Hamme','Oost-Vlaanderen','317824');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (85,23,'Zorita Michael','493-2365 Auctor Road','Richmond','BC','3921');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (86,32,'Gloria Mathis','6499 Lobortis St.','Melville','WA','3981');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (87,37,'Sloane Pruitt','8889 Cursus Rd.','Vezin','NA','1513 KF');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (88,39,'Leigh Randolph','753-1882 Proin St.','San Antonio','TX','21-047');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (89,2,'Germaine Hahn','153-2757 Nec Rd.','Purral','San José','7695');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (90,17,'Ashton Munoz','Ap #211-622 Blandit Road','Hillsboro','OR','69545');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (91,16,'Jack Herrera','1367 Leo St.','Poole','DO','2859');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (92,45,'Cole Jarvis','P.O. Box 314, 7220 Orci Rd.','Toledo','Castilla - La Mancha','1652');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (93,45,'Hilel Ware','Ap #993-7158 Enim Street','Vienna','Wie','8583');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (94,12,'Mari Chambers','332-9299 Risus. Rd.','Vienna','Wie','2537');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (95,26,'Dennis Klein','373-5085 Ultricies Street','Talagante','RM','8978');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (96,41,'Daniel Mcmahon','203-2142 Eu Street','Kielce','SK','73293');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (97,49,'Drew Bentley','Ap #540-9096 Phasellus Road','Dublin','Leinster','9596');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (98,9,'Akeem Warren','P.O. Box 610, 9556 Interdum Rd.','Mount Gambier','SA','425495');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (99,24,'Davis Craig','P.O. Box 269, 2480 Non, Rd.','Vienna','Wie','9737');
INSERT INTO "billing_information" (id,id_client,full_name,address,city,state,zip_code) VALUES (100,50,'Slade Mullins','642-2802 Velit. Rd.','Tiltil','RM','878271');

/* PURCHASE STATE */
-- INSERT INTO "purchase_state" (id,state_p) VALUES (1,'Waiting for payment');
-- INSERT INTO "purchase_state" (id,state_p) VALUES (2,'Waiting for payment approval');
-- INSERT INTO "purchase_state" (id,state_p) VALUES (3,'Paid');
-- INSERT INTO "purchase_state" (id,state_p) VALUES (4,'Shipped');
-- INSERT INTO "purchase_state" (id,state_p) VALUES (5,'Completed');
-- INSERT INTO "purchase_state" (id,state_p) VALUES (6,'Returned');

/* PURCHASE */
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (1,97,16,'2019-04-12 06:22:37');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (2,19,36,'2019-04-05 05:28:53');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (3,43,32,'2019-03-26 06:31:05');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (4,60,34,'2019-04-13 16:58:02');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (5,98,12,'2019-03-25 22:30:32');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (6,62,9,'2019-04-18 07:30:56');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (7,67,46,'2019-03-19 07:08:30');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (8,56,11,'2019-04-02 08:10:02');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (9,63,23,'2019-04-11 08:14:34');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (10,99,43,'2019-04-10 14:25:50');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (11,19,37,'2019-03-20 11:56:00');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (12,82,38,'2019-03-17 16:53:13');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (13,100,27,'2019-03-19 18:32:39');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (14,38,26,'2019-04-04 07:19:32');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (15,82,11,'2019-03-22 12:00:13');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (16,94,36,'2019-03-23 01:24:25');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (17,82,2,'2019-04-01 09:59:05');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (18,32,19,'2019-03-23 01:35:42');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (19,7,10,'2019-04-04 13:39:10');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (20,43,47,'2019-04-08 09:13:42');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (21,2,50,'2019-03-25 14:00:48');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (22,61,44,'2019-03-26 16:44:50');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (23,54,32,'2019-03-19 23:01:42');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (24,36,18,'2019-03-26 11:35:48');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (25,49,41,'2019-04-17 14:41:08');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (26,80,15,'2019-04-18 23:00:39');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (27,83,21,'2019-04-10 11:29:03');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (28,41,44,'2019-04-03 00:42:10');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (29,69,32,'2019-04-13 14:56:56');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (30,36,42,'2019-03-30 08:15:38');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (31,90,21,'2019-04-09 23:50:09');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (32,18,26,'2019-04-03 12:37:53');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (33,80,43,'2019-03-19 20:25:00');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (34,55,5,'2019-04-14 17:44:59');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (35,22,30,'2019-04-08 17:43:07');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (36,95,14,'2019-04-04 19:42:34');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (37,67,40,'2019-04-12 04:35:47');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (38,74,50,'2019-04-18 08:51:44');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (39,4,43,'2019-04-15 20:10:30');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (40,71,33,'2019-03-31 10:42:21');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (41,43,32,'2019-03-20 21:32:46');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (42,94,28,'2019-03-16 11:12:25');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (43,17,20,'2019-04-01 09:58:44');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (44,17,6,'2019-03-30 01:33:07');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (45,75,20,'2019-03-30 11:54:06');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (46,36,30,'2019-04-05 02:58:51');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (47,77,23,'2019-04-13 21:32:07');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (48,94,10,'2019-04-14 23:08:10');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (49,90,24,'2019-03-27 21:04:40');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (50,95,38,'2019-03-25 19:34:12');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (51,27,20,'2019-03-23 06:27:31');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (52,76,46,'2019-04-01 15:14:05');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (53,22,44,'2019-04-03 16:42:50');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (54,37,36,'2019-04-12 15:12:16');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (55,12,1,'2019-04-01 18:42:25');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (56,61,15,'2019-04-17 19:58:37');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (57,57,48,'2019-03-28 14:58:22');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (58,36,12,'2019-04-04 13:04:51');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (59,56,44,'2019-04-14 14:26:12');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (60,90,3,'2019-04-05 07:19:37');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (61,16,48,'2019-03-23 20:55:44');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (62,8,15,'2019-03-27 00:58:45');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (63,4,7,'2019-04-11 03:07:15');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (64,17,35,'2019-03-26 06:07:26');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (65,38,36,'2019-03-19 10:41:24');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (66,18,46,'2019-04-04 22:41:44');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (67,75,36,'2019-04-18 23:03:16');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (68,63,46,'2019-03-20 09:38:25');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (69,62,41,'2019-03-20 02:09:05');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (70,75,41,'2019-03-27 18:48:04');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (71,48,13,'2019-03-27 09:09:52');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (72,58,6,'2019-04-11 23:40:20');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (73,12,29,'2019-04-16 00:34:31');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (74,28,8,'2019-04-02 16:23:54');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (75,63,48,'2019-03-23 01:56:16');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (76,39,12,'2019-04-03 09:40:30');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (77,38,20,'2019-03-22 15:34:30');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (78,98,15,'2019-04-15 07:42:46');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (79,78,47,'2019-04-15 13:37:25');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (80,89,26,'2019-04-16 20:22:07');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (81,61,13,'2019-04-14 16:22:10');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (82,27,20,'2019-03-02 20:59:16');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (83,77,43,'2019-03-28 14:51:40');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (84,34,44,'2019-03-25 13:45:02');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (85,80,10,'2019-04-15 18:40:45');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (86,73,15,'2019-04-01 09:03:01');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (87,49,47,'2019-04-12 05:53:00');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (88,51,30,'2019-04-17 14:00:54');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (89,66,45,'2019-03-21 07:21:34');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (90,90,47,'2019-03-19 21:30:12');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (91,34,41,'2019-03-29 11:23:26');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (92,4,27,'2019-03-18 01:41:41');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (93,69,10,'2019-04-10 17:35:48');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (94,41,15,'2019-04-16 06:44:10');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (95,85,12,'2019-03-30 08:24:43');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (96,78,1,'2019-04-15 12:04:35');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (97,67,40,'2019-03-18 10:15:42');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (98,38,23,'2019-04-15 10:56:59');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (99,76,24,'2019-04-16 14:24:51');
INSERT INTO "purchase" (id,id_billing_information,id_client,date_time) VALUES (100,42,4,'2019-04-09 06:11:33');

/* PURCHASE PRDUCT */
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (1,1,'Mens Armani Exchange Watch AX2104',134.07,'Subtle mens Armani Exchange watch, with stylish stealth black Ion-plated steel case and bracelet. This sleek design has a black dial with black baton hour markers and detailing for maximum style. Inside the watch is a Japanese Quartz movement, featuring a date function at 3 o''clock. The dial also features the Armani Exchange logo at 12 o''clock for an added effect. It fastens with a push-button deployment on the black metal bracelet.',0,21);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (2,2,'Casio G-Shock DW-5600HRGRZ-1ER',80.13,'From G-SHOCK, which strives for toughness, comes a Black & Red Series that makes the most of G-SHOCK colors. A bi-color molding process makes it possible to create a band that is black on the outside and red on the inside. The flashes of red from the inside of the band when putting on or taking off the watch are a bold statement of G-SHOCK identity. FREE Exclusive Gorillaz track ''Tranz (Sibot Remix)'' with purchase.',0,10);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (3,3,'HUGO Jump Watch',255.45,'HUGO Jump 1530028 is a functional and special Gents watch from JUMP collection. Material of the case is Black Ion-plated Steel and the Black dial gives the watch that unique look. 30 metres water resistancy will protect the watch and allows it to be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,38);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (4,4,'Tommy Hilfiger 1791121',262.31,'Stylish and unashamed, Luke by Tommy Hilfiger features a gold PVD plated case and bracelet with blue bezel, and is fitted with a quartz movement with day, date and 24 hour function shown on a silvery white dial, and is water resistant to 5atm.',0,10); 
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (5,5,'STORM 47363/GY',139.88,'STORM 47363/GY is a functional and handsome Gents watch. Case material is Stainless Steel while the dial colour is Grey. In regards to the water resistance, the watch has got a resistancy up to 50 metres. It means it can be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports.',0,37);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (6,6,'Jack Wills Acland',128.24,'Jack Wills Acland JW003SLBR is a practical and very impressive Gents watch from Acland collection. Material of the case is Alloy, which stands for a high quality of the item and the Silver dial gives the watch that unique look. 30 metres water resistancy will protect the watch and allows it to be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,15);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (7,7,'Accurist Chronograph',139.88,'Accurist London Vintage 7033 is a super special Gents watch. Case material is Stainless Steel while the dial colour is Cream. The features of the watch include (among others) a chronograph and date function. 50 metres water resistancy will protect the watch and allows it to be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports.',0,21);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (8,8,'Tissot Classic Dream',157.38,'This men''s Tissot Classic Dream watch has a stainless steel case with sapphire crystal and is powered by a quartz movement. It is fastened with a brown leather strap and has a white dial with crisp roman numerals. The watch has a date function.',0,42);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (9,9,'Guess W0668G4',177.82,'Polished gold case, sunray champagne dial, brushed and polished gold bracelet.',0,50);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (10,10,'Bulova 96A208',406.87,'Sophisticated design with full exhibition dial and case-back. Stainless steel screw-back case, skeletonized black three-hand dial revealing the intricate workings of the self-winding 21-jewel movement, domed mineral crystal, stainless steel bracelet with push-button deployant clasp, and water resistance to 30 meters. Diameter: 43mm Thickness: 12.15mm',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (101,11,'Smart Turnout Master',112.59,'Smart Turnout Master Watch Lime Embossed Leather Strap STL3/RW/56/LIM is a trendy Unisex watch. Material of the case is PVD rose plating while the dial colour is Off white. 30 metres water resistancy will protect the watch and allows it to be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (102,12,'HUGO Watch 1530069',186.59,'HUGO Focus 1530033 is an amazing and special Gents watch from FOCUS collection. Material of the case is Blue Ion-Plated Steel, which stands for a high quality of the item while the dial colour is Blue. In regards to the water resistance, the watch has got a resistancy up to 30 metres. It means it can be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (103,13,'LLARSEN 147GWG3-GCAMEL20',207.77,'LLARSEN Oliver 147GWG3-GCAMEL20 is a practical and handsome Gents watch from LW47 collection. Material of the case is Stainless Steel while the dial colour is White. The watch is shipped with an original box and a guarantee from the manufacturer.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (104,14,'G-Shock GA-100-1A1ER',66.16,'High-impact mens Casio X-Large G-Shock model in stealth black resin with black detailing, with white hour hand detailing. Features include chronograph, 5 daily alarms, countdown timer, world time from a Japanese Quartz movement with perpetual calendar and date function, LED backlight and digital tachymeter. The watch fastens with a sturdy rubber strap with double tang buckle for extra security. Impressively sized at 55mm and built as tough as possible, with 20 bar water resistance and the signature G-Shock design.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (105,15,'Bulova 98D149',672.06,'From the Precisionist Collection. New Champlain style in grey IP stainless steel with rose gold-tone accents on case and bracelet, 11 diamonds individually hand set on two-tone black/grey dial with calendar feature, curved mineral glass, screw-back case, fold-over buckle closure with safety lock and extender, and water resistance to 300 meters. Powered by Bulova’s proprietary three-prong quartz crystal Precisionist movement with a 262kHz vibrational frequency—eight times greater than standard watches—for unparalleled accuracy. Diameter: 46.5mm Thickness: 14.3mm',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (106,16,'Citizen CA0080-03E',265.81,'Citizen Red Arrows World Time model with a stainless steel round case. This intelligent time piece features 1/5 second chronograph, world time in 24 cities, 12.24 hour time, screw-back case and movement calibre: B612. It also has date function and is powered by Eco-Drive movement. The round black dial has high-visibility baton hour markers and hands which light up in the dark, red touches and date function. It''s 100 meter water resistant and fastens with a quality black with red stitching genuine leather strap.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (107,17,'Accurist MB921BX',104.47,'Mens Accurist Diamond watch in PVD gold plating, set around a black rectangular dial with gold baton hour markers.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (108,18,'Jean Paul Gaultier',104.47,'This iconic watch from the ENFANTS TERRIBLES collection by eponymous designer Jean Paul Gaultier features the unmistakeable stripes paired with a soft navy blue silicon strap for a sporty look and comfortable fit. Touches of warm rose gold make this a super trendy stand-out piece from the collection!',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (109,19,'Armani AX2103',113.56,'This stylish mens Armani Exchange watch in stainless steel features a 47mm case and centred on a black dial with silver baton hour markers and date function. The watch is fitted with quartz movement and fastens with a silver stainless steel bracelet.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (110,20,'Accurist 7033',77.77,'Accurist London Vintage 7033 is a super special Gents watch. Case material is Stainless Steel while the dial colour is Cream. The features of the watch include (among others) a chronograph and date function. 50 metres water resistancy will protect the watch and allows it to be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports. The watch is shipped with an original box and a guarantee from the manufacturer.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (111,21,'STORM 47210/B',126.52,'STORM New Remi Lazer Blue 47210/B is a functional and special Gents watch. Material of the case is Stainless Steel while the dial colour is Blue. 50 metres water resistancy will protect the watch and allows it to be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports. The watch is shipped with an original box and a guarantee from the manufacturer.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (112,22,'STORM 47388/B',95.62,'STORM Trionic 47388/B is a functional and special Gents watch. Material of the case is Stainless Steel while the dial colour is Blue. In regards to the water resistance, the watch has got a resistancy up to 50 metres. It means it can be submerged in water for periods, so can be used for swimming and fishing. It is not reccomended for high impact water sports. The watch is shipped with an original box and a guarantee from the manufacturer.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (113,23,'STORM 47075/B',104.85,'Mens Storm Sotec Lazer watch in stainless steel, centred on a bright blue dial with date function and high-contrast hands.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (114,24,'STORM 47155/B',104.45,'STORM Cyro XL 47155/B is a functional and handsome Gents watch. Case material is Stainless Steel and the Blue dial gives the watch that unique look. The features of the watch include (among others) a date function. This watch is market as water resistant. It means it can withstand slight splashes and rain, but is NOT to be immersed in water. We ship it with an original box and a guarantee from the manufacturer.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (115,25,'Guess W0799G4',275.40,'Polished silver and gold case with crystal detailing, sunray white glitz multi-functional dial, polished silver and gold bracelt with crystals.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (116,26,'Accurist MB933S',80.84,'This Men''s watch by Accurist is made from PVD gold plated steel and has a 42mm case with a silver dial. The dial features 3 mini dials, chronograph, date function and gold baton hour markers with gold hands. This sought after model is 50m water resistant and powered by a quality quartz movement. It fastens with a gold metal bracelet.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (117,27,'Swatch GB743',46.43,'The ever popular Swatch Once Again watch for men, proving that simple is always effective. This time piece in monochrome style comes with a water resistant resin case, with a reliable Swiss quartz movement with day / date function embedded into it, and an easy change battery cover. It has a minimal white dial with easy to read numeral hour markers in black, and a date magnification bubble in the acrylic glass. This model fastens with a black plastic strap.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (118,28,'Limit 5484.01',34.67,'Limit 5484.01 is an amazing and attractive Gents watch. Material of the case is PVD rose plating and the Black dial gives the watch that unique look. The features of the watch include (among others) a date function. In regards to the water resistance, the watch has got a resistancy up to 30 metres. It means it can be worn in scenarios where it is likely to be splashed but not immersed in water. It can be worn while washing your hands and will be fine in rain. The watch is shipped with an original box and a guarantee from the manufacturer.',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (119,29,'Bulova 97A138',405.10,'Sophisticated design with full exhibition dial and case-back. Gold-tone stainless steel screw-back case, skeletonized silver white three-hand dial revealing the intricate workings of the self-winding 21-jewel movement, domed mineral crystal, brown leather strap with push-button deployant buckle, and water resistance to 30 meters. Diameter: 43mm Thickness: 12.15mm',0,35);
INSERT INTO "purchased_product" (id_product,id_purchase,name,price,description,discount,quantity) VALUES (120,30,'G-Shock GWG-1000-1A1ER',766.08,'Casio G-Shock Premium Mudmaster Compass GWG-1000-1A1ER is an amazing and very impressive Gents watch. Case material is Stainless Steel and Resin, which stands for a high quality of the item while the dial colour is Black. The features of the watch include (among others) a chronograph and date function as well as an alarm. This model has got 200 metres water resistancy - it can be used for professional marine activity, skin diving and high impact water sports, but not deep sea or mixed gas diving. The watch is shipped with an original box and a guarantee from the manufacturer.',0,35);

/* PURCHASE LOG */
/* WAITING FOR PAYMENT */ 

INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (1,'Waiting for payment', '2019-02-01 03:36:19');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (2,'Waiting for payment','2019-02-01 09:48:45');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (3,'Waiting for payment','2019-02-01 13:59:24');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (4,'Waiting for payment','2019-02-01 04:44:35');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (5,'Waiting for payment','2019-02-01 09:32:42');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (6,'Waiting for payment','2019-02-01 19:23:38');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (7,'Waiting for payment','2019-02-01 06:31:57');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (8,'Waiting for payment','2019-02-01 10:40:50');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (9,'Waiting for payment','2019-02-01 21:57:46');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (10,'Waiting for payment','2019-02-01 04:09:39');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (11,'Waiting for payment','2019-02-01 04:54:13');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (12,'Waiting for payment','2019-02-01 12:29:43');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (13,'Waiting for payment','2019-02-01 00:29:01');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (14,'Waiting for payment','2019-02-01 05:28:15');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (15,'Waiting for payment','2019-02-01 12:59:15');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (16,'Waiting for payment','2019-02-01 15:20:41');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (17,'Waiting for payment','2019-02-01 02:22:35');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (18,'Waiting for payment','2019-02-01 22:29:00');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (19,'Waiting for payment','2019-02-01 06:30:29');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (20,'Waiting for payment','2019-02-01 04:45:21');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (21,'Waiting for payment','2019-02-01 15:16:12');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (22,'Waiting for payment','2019-02-01 21:17:12');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (23,'Waiting for payment','2019-02-01 11:49:29');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (24,'Waiting for payment','2019-02-01 17:16:32');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (25,'Waiting for payment','2019-02-01 04:25:11');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (26,'Waiting for payment','2019-02-01 07:30:28');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (27,'Waiting for payment','2019-02-01 00:34:15');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (28,'Waiting for payment','2019-02-01 10:06:22');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (29,'Waiting for payment','2019-02-01 00:54:41');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (30,'Waiting for payment','2019-02-01 16:57:25');


/* WAITING FOR PAYMENT APPROVAL */
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (1,'Waiting for payment approval','2019-02-02 05:01:51');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (2,'Waiting for payment approval', '2019-02-02 05:44:33');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (3,'Waiting for payment approval', '2019-02-02 22:13:32');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (4,'Waiting for payment approval', '2019-02-02 09:11:37');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (5,'Waiting for payment approval', '2019-02-02 18:55:18');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (6,'Waiting for payment approval', '2019-02-02 11:26:05');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (7,'Waiting for payment approval', '2019-02-02 15:23:42');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (8,'Waiting for payment approval', '2019-02-02 08:51:43');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (9,'Waiting for payment approval', '2019-02-02 19:23:05');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (10,'Waiting for payment approval', '2019-02-02 16:27:19');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (11,'Waiting for payment approval', '2019-02-02 16:49:58');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (12,'Waiting for payment approval', '2019-02-02 00:48:42');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (13,'Waiting for payment approval', '2019-02-02 20:12:29');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (14,'Waiting for payment approval', '2019-02-02 18:47:15');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (15,'Waiting for payment approval', '2019-02-02 12:00:23');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (16,'Waiting for payment approval', '2019-02-02 05:11:12');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (17,'Waiting for payment approval', '2019-02-02 05:23:18');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (18,'Waiting for payment approval', '2019-02-02 22:42:37');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (19,'Waiting for payment approval', '2019-02-02 15:48:12');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (20,'Waiting for payment approval', '2019-02-02 18:43:26');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (21,'Waiting for payment approval', '2019-02-02 19:49:44');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (22,'Waiting for payment approval', '2019-02-02 11:32:49');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (23,'Waiting for payment approval', '2019-02-02 00:03:21');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (24,'Waiting for payment approval', '2019-02-02 14:01:26');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (25,'Waiting for payment approval', '2019-02-02 10:18:23');

/* PAID */
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (1,'Paid', '2019-02-03 03:12:19');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (2,'Paid','2019-02-03 00:07:29');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (3,'Paid','2019-02-03 18:04:05');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (4,'Paid','2019-02-03 17:32:47');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (5,'Paid','2019-02-03 21:01:22');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (6,'Paid','2019-02-03 15:46:22');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (7,'Paid','2019-02-03 02:28:45');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (8,'Paid','2019-02-03 04:02:46');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (9,'Paid','2019-02-03 02:10:45');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (10,'Paid','2019-02-03 02:15:27');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (11,'Paid','2019-02-03 05:43:03');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (12,'Paid','2019-02-03 02:01:51');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (13,'Paid','2019-02-03 03:09:29');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (14,'Paid','2019-02-03 14:43:44');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (15,'Paid','2019-02-03 16:47:00');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (16,'Paid','2019-02-03 16:39:16');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (17,'Paid','2019-02-03 05:54:39');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (18,'Paid','2019-02-03 01:46:37');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (19,'Paid','2019-02-03 11:13:26');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (20,'Paid','2019-02-03 12:58:51');

/* SHIPPED */
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (1,'Shipped','2019-02-04 10:47:14');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (2,'Shipped','2019-02-04 19:48:59');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (3,'Shipped','2019-02-04 06:43:36');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (4,'Shipped','2019-02-04 09:49:06');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (5,'Shipped','2019-02-04 18:34:11');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (6,'Shipped','2019-02-04 10:01:29');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (7,'Shipped','2019-02-04 22:46:08');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (8,'Shipped','2019-02-04 01:17:00');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (9,'Shipped','2019-02-04 10:18:44');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (10,'Shipped','2019-02-04 18:08:25');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (11,'Shipped','2019-02-04 05:17:03');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (12,'Shipped','2019-02-04 19:38:03');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (13,'Shipped','2019-02-04 07:52:24');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (14,'Shipped','2019-02-04 00:19:55');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (15,'Shipped','2019-02-04 13:24:37');

/* COMPLETED */
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (1,'Completed','2019-02-05 01:51:50');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (2,'Completed','2019-02-05 11:09:02');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (3,'Completed','2019-02-05 19:42:36');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (4,'Completed','2019-02-05 03:38:55');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (5,'Completed','2019-02-05 18:52:40');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (6,'Completed','2019-02-05 21:50:05');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (7,'Completed','2019-02-05 10:21:54');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (8,'Completed','2019-02-05 02:06:59');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (9,'Completed','2019-02-05 16:30:22');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (10,'Completed','2019-02-05 06:59:57');

/* RETURNED */
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (1,'Returned','2019-02-06 12:59:04');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (2,'Returned','2019-02-06 16:42:13');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (3,'Returned','2019-02-06 07:28:27');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (4,'Returned','2019-02-06 16:13:30');
INSERT INTO "purchase_log" (id_purchase,purchase_state,"date_time") VALUES (5,'Returned','2019-02-06 08:06:28');


/* BAN */
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (1,1,32,'2019-12-24 06:26:17','2019-12-09 17:35:27','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (2,1,42,'2019-12-30 21:16:04','2019-12-18 04:17:21','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (3,1,37,'2019-12-21 07:16:53','2019-12-08 09:00:02','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (4,1,29,'2019-12-30 19:46:40','2019-12-14 21:41:35','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (5,1,36,'2019-12-08 08:19:36','2019-12-26 00:19:31','Inappropriate review.');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (6,1,22,'2019-12-15 08:03:58','2019-12-19 12:58:42','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (7,1,42,'2019-12-25 02:52:05','2019-12-12 20:25:37','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (8,1,21,'2019-12-14 01:52:30','2019-12-20 17:50:29','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (9,1,35,'2019-12-24 06:12:07','2019-12-31 00:00:10','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (10,1,45,'2019-12-17 06:24:24','2019-12-01 02:30:07','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (11,1,34,'2019-12-20 02:41:04','2019-12-20 09:58:28','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (12,1,39,'2019-12-01 20:15:30','2019-12-21 08:28:04','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (13,1,20,'2019-12-07 22:39:32','2019-12-26 08:53:32','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (14,1,33,'2019-12-02 09:15:44','2019-12-23 21:43:48','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (15,1,10,'2019-12-27 18:20:29','2019-12-28 18:57:43','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (16,1,34,'2019-12-28 02:30:12','2019-12-04 09:50:30','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (17,1,13,'2019-12-10 02:14:49','2019-12-11 13:37:31','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (18,1,28,'2019-12-27 12:31:01','2019-12-21 02:23:07','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (19,1,11,'2019-12-06 05:23:31','2019-12-19 01:05:15','Inappropriate review');
INSERT INTO "ban" (id,id_staff_member,id_client,start_t,end_t,reason) VALUES (20,1,49,'2019-12-25 23:35:38','2019-12-21 22:42:18','Inappropriate review');

/* DISCOUNT */
INSERT INTO "discount" (id,id_category,value,start_t,end_t) VALUES (1,1,20,'2019-12-01 17:59:35','2019-12-31 06:27:23');
INSERT INTO "discount" (id,id_category,value,start_t,end_t) VALUES (2,2,50,'2019-12-01 13:19:46','2019-12-31 03:30:23');
INSERT INTO "discount" (id,id_category,value,start_t,end_t) VALUES (3,3,75,'2019-12-01 09:19:55','2019-12-31 08:06:19');
INSERT INTO "discount" (id,id_category,value,start_t,end_t) VALUES (4,4,10,'2019-12-01 08:42:09','2019-12-31 12:45:04');
INSERT INTO "discount" (id,id_category,value,start_t,end_t) VALUES (5,5,60,'2019-12-01 19:19:18','2019-12-31 02:52:36');

/* REVIEWS */
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (1,1,16,'Fucking great product',5,'2019-04-19 14:48:40');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (2,2,36,'I loved everything about it',5,'2019-04-19 18:46:27');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (3,3,32,'Did not meet my expectations',1,'2019-04-19 10:54:14');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (4,4,34,'Was really wishing for something with a better quality',2,'2019-04-19 19:54:38');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (5,5,12,'Service and product are great, so happy I made this purchase',5,'2019-04-19 18:59:33');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (6,6,9,'Product is what I was expecting but the order came a bit late',3,'2019-04-19 23:55:52');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (7,7,46,'Did not get quite what was advertised in the description',3,'2019-04-19 22:27:04');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (8,8,11,'Good costumer service',3,'2019-04-19 02:47:55');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (9,9,23,'My father really liked the gift',4,'2019-04-19 03:35:19');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (10,10,43,'It got broken in the transportation and they said it was my problem I did not get insurance',1,'2019-04-19 20:31:04');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (11,101,37,'I regret making this purchase, its terrible',1,'2019-04-19 08:10:46');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (12,102,38,'Service could have been better',2,'2019-04-19 16:23:32');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (13,103,27,'Messed up my order and I ended up with the wrong order...',2,'2019-04-19 09:05:03');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (14,104,26,'Really fast service, thank you',5,'2019-04-19 19:46:36');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (15,105,11,'Great experience buying here, will definetly come back again',5,'2019-04-19 13:05:59');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (16,106,36,'Will never come back again, my order got delayed 2 days more than it was supposed to',1,'2019-04-19 13:28:50');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (17,107,2,'Costumer service are very friendly and helpfull, great buying experience.',4,'2019-04-19 16:40:13');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (18,108,19,'Cant wait to shop here again',4,'2019-04-19 13:48:57');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (19,109,10,'Great expereince overall',5,'2019-04-19 19:18:46');
INSERT INTO reviews (id,id_product,id_client,comment,rating,"date_time") VALUES (20,110,47,'My order got lost and I had to wait 3 more days',2,'2019-04-19 07:22:49');

/* REPORT */
INSERT INTO reports (id,reason,id_review,id_client,"date_time") VALUES (1,'It has a swear word in it',1,32,'2019-02-11 14:53:40');

/* REPORT LOG*/
INSERT INTO "report_log" (report_id,id_staff_member,has_deleted,"date_time") VALUES (1,1,True,'2019-02-13 14:53:40');