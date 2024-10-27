CREATE TABLE IF NOT EXISTS Users (
    User_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(50) NOT NULL,
    Surname VARCHAR(50) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    IBAN VARCHAR(36) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Credit_cards (
    Card_ID INT AUTO_INCREMENT PRIMARY KEY,
    User_ID INT NOT NULL,
    Card_number VARCHAR(16) NOT NULL UNIQUE,
    Expiration VARCHAR(5) NOT NULL,
    CVV VARCHAR(3) NOT NULL,
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Transactions (
    ID_transaction INT AUTO_INCREMENT PRIMARY KEY,
    IBAN_sender VARCHAR(36) NOT NULL,
    IBAN_receiver VARCHAR(36) NOT NULL,
    Date DATETIME NOT NULL,
    Reason VARCHAR(255),
    Amount DECIMAL(10, 2) NOT NULL
);