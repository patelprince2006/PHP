-- Create Buses Table
CREATE TABLE IF NOT EXISTS buses (
    id SERIAL PRIMARY KEY,
    operator_name VARCHAR(100) NOT NULL,
    bus_number VARCHAR(50),
    from_location VARCHAR(100) NOT NULL,
    to_location VARCHAR(100) NOT NULL,
    departure_time TIMESTAMP NOT NULL,
    arrival_time TIMESTAMP NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    bus_type VARCHAR(50)
);

-- Create Trains Table
CREATE TABLE IF NOT EXISTS trains (
    id SERIAL PRIMARY KEY,
    train_name VARCHAR(100) NOT NULL,
    train_number VARCHAR(50) NOT NULL,
    from_station VARCHAR(100) NOT NULL,
    to_station VARCHAR(100) NOT NULL,
    departure_time TIMESTAMP NOT NULL,
    arrival_time TIMESTAMP NOT NULL,
    price DECIMAL(10, 2) NOT NULL
);

-- Create Hotels Table
CREATE TABLE IF NOT EXISTS hotels (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address TEXT,
    price_per_night DECIMAL(10, 2) NOT NULL,
    rating DECIMAL(2, 1) DEFAULT 0,
    amenities TEXT
);

-- Insert Dummy Data for Buses
INSERT INTO buses (operator_name, bus_number, from_location, to_location, departure_time, arrival_time, price, bus_type) VALUES 
('VRL Travels', 'KA-01-AB-1234', 'Bangalore', 'Hyderabad', NOW() + INTERVAL '1 day', NOW() + INTERVAL '1 day 8 hours', 1200.00, 'AC Sleeper'),
('Orange Tours', 'TS-09-CD-5678', 'Hyderabad', 'Bangalore', NOW() + INTERVAL '1 day', NOW() + INTERVAL '1 day 9 hours', 1100.00, 'AC Semi-Sleeper');

-- Insert Dummy Data for Trains
INSERT INTO trains (train_name, train_number, from_station, to_station, departure_time, arrival_time, price) VALUES 
('Rajdhani Express', '12433', 'Chennai', 'Delhi', NOW() + INTERVAL '2 days', NOW() + INTERVAL '3 days', 3500.00),
('Shatabdi Express', '12007', 'Chennai', 'Mysore', NOW() + INTERVAL '2 days', NOW() + INTERVAL '2 days 7 hours', 800.00);

-- Insert Dummy Data for Hotels
INSERT INTO hotels (name, city, address, price_per_night, rating, amenities) VALUES 
('Taj Mahal Palace', 'Mumbai', 'Apollo Bunder, Mumbai', 15000.00, 5.0, 'Pool, Spa, Wifi'),
('Hyatt Regency', 'Delhi', 'Bhikaji Cama Place, New Delhi', 12000.00, 4.8, 'Pool, Gym, Wifi'),
('Goa Beach Resort', 'Goa', 'Calangute Beach, Goa', 5000.00, 4.2, 'Beach Access, Bar, Wifi');
