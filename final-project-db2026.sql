-- ONLY RUN IN LOCAL DATABASE
-- DROP DATABASE IF EXISTS "final-project-db2026";
-- CREATE DATABASE "final-project-db2026";

DROP TABLE IF EXISTS Budget, Category, Contact, Debt_Loan, Goal, Pocket, Sub_category, Transactions, Transfer, Counters;

-- 1. POCKET
CREATE TABLE Pocket (
    Pocket_ID INT NOT NULL AUTO_INCREMENT,
    Pocket_Name VARCHAR(200) NOT NULL,
    Balance FLOAT NOT NULL,
    Max_Budget FLOAT NOT NULL,
    Created_Date TIMESTAMP NOT NULL,
    CONSTRAINT pk_Pocket PRIMARY KEY (Pocket_ID)
);

-- 2. CATEGORY
CREATE TABLE Category (
    Category_ID INT NOT NULL AUTO_INCREMENT,
    Category_Name VARCHAR(200) NOT NULL,
    Category_Type VARCHAR(200) NOT NULL,
    Icon_Code VARCHAR(50) NOT NULL,
    CONSTRAINT pk_Category PRIMARY KEY (Category_ID)
);

-- 3. TRANSACTIONS
CREATE TABLE Transactions (
    Transaction_ID INT NOT NULL AUTO_INCREMENT,
    Pocket_ID INT NOT NULL,
    Category_ID INT NOT NULL,
    Amount FLOAT NOT NULL,
    System_Log TIMESTAMP NOT NULL,
    Description TEXT NOT NULL,
    Warning_Status BOOLEAN NOT NULL,
    CONSTRAINT pk_Transaction PRIMARY KEY (Transaction_ID)
);

-- 4. GOAL
CREATE TABLE Goal (
    Goal_ID INT NOT NULL AUTO_INCREMENT,
    Pocket_ID INT NOT NULL,
    Goal_Name VARCHAR(200) NOT NULL,
    Target_Amount FLOAT NOT NULL,
    Deadline_Date DATE NOT NULL,
    CONSTRAINT pk_Goal PRIMARY KEY (Goal_ID)
);

-- 5. CONTACT
CREATE TABLE Contact (
    Contact_ID INT NOT NULL AUTO_INCREMENT,
    Contact_Name VARCHAR(200) NOT NULL,
    Phone_Number VARCHAR(200) NOT NULL,
    Relation_Type VARCHAR(200) NOT NULL,
    CONSTRAINT pk_Contact PRIMARY KEY (Contact_ID)
);

-- 6. DEBT_LOAN
CREATE TABLE Debt_Loan (
    Debt_ID INT NOT NULL AUTO_INCREMENT,
    Contact_ID INT NOT NULL,
    Pocket_ID INT NOT NULL,
    Debt_Category INT NOT NULL,
    Amount FLOAT NOT NULL,
    Due_Date DATE NOT NULL,
    Status VARCHAR(200) NOT NULL,
    CONSTRAINT pk_Debt_Loan PRIMARY KEY (Debt_ID)
);

-- 7. BUDGET
CREATE TABLE Budget (
    Budget_ID INT NOT NULL AUTO_INCREMENT,
    Category_ID INT NOT NULL,
    Monthly_Limit FLOAT NOT NULL,
    Start_Date DATE NOT NULL,
    End_Date DATE NOT NULL,
    CONSTRAINT pk_Budget PRIMARY KEY (Budget_ID)
);

-- 8. TRANSFER
CREATE TABLE Transfer (
    Transfer_ID INT NOT NULL AUTO_INCREMENT,
    Source_Pocket_ID INT NOT NULL,
    Target_Pocket_ID INT NOT NULL,
    Transfer_Amount FLOAT NOT NULL,
    Transfer_Date TIMESTAMP NOT NULL,
    CONSTRAINT pk_Transfer PRIMARY KEY (Transfer_ID)
);

-- 9. SUB_CATEGORY
CREATE TABLE Sub_Category (
    Sub_Category_ID INT NOT NULL AUTO_INCREMENT,
    Category_ID INT NOT NULL,
    Sub_Name VARCHAR(200) NOT NULL,
    Notes TEXT NOT NULL,
    CONSTRAINT pk_Sub_Category PRIMARY KEY (Sub_Category_ID)
);

-- 10. COUNTERS (application-managed sequential ID table)
-- Provides gapless sequential IDs via SELECT ... FOR UPDATE (InnoDB & PostgreSQL).
-- current_value is initialised to MAX(id) of the corresponding entity table so that
-- the first application-inserted row gets MAX(id)+1 and never collides with seed data.
CREATE TABLE Counters (
    name          VARCHAR(100) NOT NULL,
    current_value BIGINT       NOT NULL,
    CONSTRAINT pk_counters PRIMARY KEY (name)
);

-- ADD FOREIGN KEYS
ALTER TABLE Transactions
ADD CONSTRAINT fk_Transaction_Pocket_ID
FOREIGN KEY (Pocket_ID) REFERENCES Pocket (Pocket_ID);

ALTER TABLE Transactions
ADD CONSTRAINT fk_Transaction_Category_ID
FOREIGN KEY (Category_ID) REFERENCES Category (Category_ID);

ALTER TABLE Goal
ADD CONSTRAINT fk_Goal_Pocket_ID
FOREIGN KEY (Pocket_ID) REFERENCES Pocket (Pocket_ID);

ALTER TABLE Debt_Loan
ADD CONSTRAINT fk_Debt_Loan_Contact_ID
FOREIGN KEY (Contact_ID) REFERENCES Contact (Contact_ID);

ALTER TABLE Debt_Loan
ADD CONSTRAINT fk_Debt_Loan_Pocket_ID
FOREIGN KEY (Pocket_ID) REFERENCES Pocket (Pocket_ID);

ALTER TABLE Debt_Loan
ADD CONSTRAINT fk_Debt_Loan_Category_ID
FOREIGN KEY (Debt_Category) REFERENCES Sub_Category (Sub_Category_ID);

ALTER TABLE Budget
ADD CONSTRAINT fk_Budget_Category_ID
FOREIGN KEY (Category_ID) REFERENCES Category (Category_ID);

ALTER TABLE Transfer
ADD CONSTRAINT fk_Transfer_Source_Pocket_ID
FOREIGN KEY (Source_Pocket_ID) REFERENCES Pocket (Pocket_ID);

ALTER TABLE Transfer
ADD CONSTRAINT fk_Transfer_Target_Pocket_ID
FOREIGN KEY (Target_Pocket_ID) REFERENCES Pocket (Pocket_ID);

ALTER TABLE Sub_Category
ADD CONSTRAINT fk_Sub_Category_ID
FOREIGN KEY (Category_ID) REFERENCES Category (Category_ID);
