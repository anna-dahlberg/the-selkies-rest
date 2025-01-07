# the-selkies-rest
Repo for my hotel The Selkie's Rest at Blackthorn Isle, for Yrgopelago. 


## Table creation queries

### Rooms table

```sql
CREATE TABLE IF NOT EXISTS rooms (
	id INTEGER PRIMARY KEY AUTOINCREMENT, 
	type VARCHAR(30),
	price INTEGER
);
```

### Guests table

```sql
CREATE TABLE IF NOT EXISTS guests (
	id INTEGER PRIMARY KEY AUTOINCREMENT, 
	name VARCHAR(50),
	email VARCHAR(50)
);
```

### Bookings table

```sql
CREATE TABLE IF NOT EXISTS bookings (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	arrival_date DATE,
	departure_date DATE,
	guest_id INTEGER,
	room_id INTEGER,
	discount_id INTEGER,
	total_cost INTEGER,
	FOREIGN KEY (guest_id) REFERENCES guests(id),
	FOREIGN KEY (room_id) REFERENCES rooms(id),
	FOREIGN KEY (discount_id) REFERENCES discounts(id)
);
```

### Features table

```sql
CREATE TABLE IF NOT EXISTS features (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name VARCHAR(30),
	price INTEGER
);
```

### Discounts table

```sql
CREATE TABLE IF NOT EXISTS discounts (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name VARCHAR(30),
	min_nights INTEGER,
	discount_rate INTEGER
);
```

### Rooms_bookings_features junction table

```sql
CREATE TABLE IF NOT EXISTS rooms_bookings_features (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	booking_id INTEGER,
	feature_id INTEGER,
	FOREIGN KEY (booking_id) REFERENCES bookings(id),
	FOREIGN KEY (feature_id) REFERENCES features(id),
);
```

### Initial Insertion Queries 

```sql
INSERT INTO rooms (type, price)
VALUES ('Budget', 2),
	('Standard', 4),
	('Luxury', 6);

INSERT INTO features ("name", price)
VALUES ('sauna',2),
		('bicycle', 2),
		('radio', 2),
		('whiskeyExperience', 2),
		('lochMonsterHunt', 2),
		('highlandCowCuddles', 2);

INSERT INTO discounts (name, min_nights, discount_rate)
VALUES ('Selkie Stay Discount', 3, 2);
```