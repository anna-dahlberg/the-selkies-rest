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
	first_name VARCHAR(30),
	last_name VARCHAR(40),
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
	FOREIGN KEY (guest_id) REFERENCES guests(id)
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
	discount_rate FLOAT
);
```

### Rooms_bookings_features junction table

```sql
CREATE TABLE IF NOT EXISTS rooms_bookings_features (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	booking_id INTEGER,
	feature_id INTEGER,
	discount_id INTEGER,
	FOREIGN KEY (booking_id) REFERENCES bookings(id),
	FOREIGN KEY (feature_id) REFERENCES features(id),
	FOREIGN KEY (discount_id) REFERENCES discounts(id)
);
```