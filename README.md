# the-selkies-rest
# The Selkie's Rest - Booking Application

Welcome to **The Selkie's Rest** repository, a custom-built booking system tailored for a boutique hotel experience. This PHP-based application provides users with intuitive booking management, feature exploration, and an integrated calendar system to ensure a seamless reservation process.

---

## 🚀 Features

- **Dynamic Booking Management**: 
  - Book rooms effortlessly with a selection of unique features.
  - Manage room availability and customer preferences in real time.

- **Integrated Calendar**: 
  - Visualize room availability using a custom-built calendar, ensuring easy navigation through dates and bookings.

- **Exclusive Hotel Features**:
  - Selkie Sauna
  - Blackthorn Bicycles
  - The Scotch Whiskey Experience
  - Loch Monster Hunt
  - Highland Cow Cuddles

- **API Integration**:
  - External services integration using Guzzle for enhanced functionality.

- **Environment Management**:
  - Securely manage environment variables with `vlucas/phpdotenv`.

---

## 🛠️ Installation

### Prerequisites
- PHP 7.4 or higher
- Composer
- SQLite database support

### Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/anna-dahlberg/the-selkies-rest
   cd selkies-rest

2. **Install Dependencies**
	Run the following command to install required libraries via Composer:
	```bash
	composer install

3. **Set Up Environment Variables**
	Copy the .env.example file to .env and update the configuration to match your setup:
	```bash
	cp .env.example .env

4. **Database Setup**
	Create a database "bookings.db" with the table creation queries further down and make the insertions as well. Ensure the bookings.db SQLite database file is located in the appropriate directory. 

5. **Run the Application**
	Use a local PHP server or deploy the application to your server.
 
## 📚 Technologies Used
- PHP: Core language for development.
- Composer: Dependency manager.
- Guzzle: HTTP client for API requests.
- vlucas/phpdotenv: Secure environment variable management.
- Custom Calendar Package: Provides dynamic calendar functionality. Source: [Build a Simple Calendar in Website using PHP](https://youthsforum.com/2020/08/build-a-simple-calendar-in-website-using-php-with-source-code/)

---

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/anna-dahlberg/the-selkies-rest/blob/main/LICENSE) file for details.

---

## Table creation queries for the database

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
	min_days INTEGER,
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
	FOREIGN KEY (feature_id) REFERENCES features(id)
);
```

### Initial Insertion Queries 

```sql
INSERT INTO rooms (type, price)
VALUES ('Budget', 3),
	('Standard', 6),
	('Luxury', 9);

INSERT INTO features ("name", price)
VALUES ('sauna',3),
		('bicycle', 3),
		('radio', 3),
		('whiskeyExperience', 3),
		('lochMonsterHunt', 3),
		('highlandCowCuddles', 3);

INSERT INTO discounts (name, min_days, discount_rate)
VALUES ('Selkie Stay Discount', 3, 2);
```