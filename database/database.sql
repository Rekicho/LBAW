-- Types
 
CREATE TYPE purchase_state AS ENUM ('Waiting for payment', 'Waiting for payment approval', 'Paid', 'Shipped', 'Completed');

