-- SQL file just so i can easily see typos. I'm blind. 
-- Creating database. 
CREATE DATABASE taxibooking;

-- Create customer table
CREATE TABLE IF NOT EXISTS customers(
    CUSTOMER_ID INT AUTO_INCREMENT PRIMARY KEY,
    CUSTOMER_NAME VARCHAR(255) NOT NULL,
    CUSTOMER_PH VARCHAR(10) NOT NULL
);

-- suburb table
CREATE TABLE IF NOT EXISTS suburbs(
    SUBURB_ID INT AUTO_INCREMENT PRIMARY KEY,
    SUBURB_NAME VARCHAR(30) NOT NULL
);

-- booking info
CREATE TABLE IF NOT EXISTS booking_information(
    BOOKING_REF VARCHAR(20) PRIMARY KEY,
    UNIT_NUMBER VARCHAR(5),
    STREET_NUMBER VARCHAR(5) NOT NULL,
    STREET_NAME VARCHAR(30) NOT NULL,
    ORIGIN_SUBURB_ID INT,
    DESTINATION_SUBURB_ID INT,
    PICKUP_DATE DATE,
    PICKUP_TIME TIME,
    FOREIGN KEY (ORIGIN_SUBURB_ID) REFERENCES suburbs(SUBURB_ID),
    FOREIGN KEY (DESTINATION_SUBURB_ID) REFERENCES suburbs(SUBURB_ID)
);

-- Create booking table
CREATE TABLE IF NOT EXISTS bookings(
    BOOKING_ID INT AUTO_INCREMENT PRIMARY KEY,
    CUSTOMER_ID INT,
    BOOKING_REF VARCHAR(20),
    BOOKING_DATE DATETIME NOT NULL,
    BOOKING_STATUS VARCHAR(10) DEFAULT 'unassigned',
    FOREIGN KEY (CUSTOMER_ID) REFERENCES customers(CUSTOMER_ID),
    FOREIGN KEY (BOOKING_REF) REFERENCES booking_information(BOOKING_REF)
);

-- Create admin table
CREATE TABLE IF NOT EXISTS admins(
    ADMIN_ID INT AUTO_INCREMENT PRIMARY KEY,
    ADMIN_NAME VARCHAR(255) NOT NULL,
    ADMIN_PASS VARCHAR(2000) NOT NULL
);
