-- ONLY RUN IN LOCAL DATABASE
-- DROP DATABASE IF EXISTS "final-project-db2026";
-- CREATE DATABASE "final-project-db2026";

DROP TABLE IF EXISTS budget, category, contact, debt_loan, goal, pocket, sub_category, transactions, transfer;

-- 1. POCKET
CREATE TABLE Pocket (
    Pocket_ID INT NOT NULL,
    Pocket_Name VARCHAR(200) NOT NULL,
    Balance FLOAT NOT NULL,
    Max_Budget FLOAT NOT NULL,
    Created_Date TIMESTAMP NOT NULL,
    CONSTRAINT pk_Pocket PRIMARY KEY (Pocket_ID)
);

-- 2. CATEGORY
CREATE TABLE Category (
    Category_ID INT NOT NULL ,
    Category_Name VARCHAR(200) NOT NULL,
    Category_Type VARCHAR(200) NOT NULL,
    Icon_Code VARCHAR(50) NOT NULL,
    CONSTRAINT pk_Category PRIMARY KEY (Category_ID)
);

-- 3. TRANSACTIONS
CREATE TABLE Transactions (
    Transaction_ID INT NOT NULL ,
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
    Goal_ID INT NOT NULL ,
    Pocket_ID INT NOT NULL,
    Goal_Name VARCHAR(200) NOT NULL,
    Target_Amount FLOAT NOT NULL,
    Deadline_Date DATE NOT NULL,
    CONSTRAINT pk_Goal PRIMARY KEY (Goal_ID)
);

-- 5. CONTACT
CREATE TABLE Contact (
    Contact_ID INT NOT NULL ,
    Contact_Name VARCHAR(200) NOT NULL,
    Phone_Number VARCHAR(200) NOT NULL,
    Relation_Type VARCHAR(200) NOT NULL,
    CONSTRAINT pk_Contact PRIMARY KEY (Contact_ID)
);

-- 6. DEBT_LOAN
CREATE TABLE Debt_Loan (
    Debt_ID INT NOT NULL ,
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
    Budget_ID INT NOT NULL ,
    Category_ID INT NOT NULL,
    Monthly_Limit FLOAT NOT NULL,
    Start_Date DATE NOT NULL,
    End_Date DATE NOT NULL,
    CONSTRAINT pk_Budget PRIMARY KEY (Budget_ID)
);

-- 8. TRANSFER
CREATE TABLE Transfer (
    Transfer_ID INT NOT NULL ,
    Source_Pocket_ID INT NOT NULL,
    Target_Pocket_ID INT NOT NULL,
    Transfer_Amount FLOAT NOT NULL,
    Transfer_Date TIMESTAMP NOT NULL,
    CONSTRAINT pk_Transfer PRIMARY KEY (Transfer_ID)
);

-- 9. SUB_CATEGORY
CREATE TABLE Sub_Category (
    Sub_Category_ID INT NOT NULL ,
    Category_ID INT NOT NULL,
    Sub_Name VARCHAR(200) NOT NULL,
    Notes TEXT NOT NULL,
    CONSTRAINT pk_Sub_Category PRIMARY KEY (Sub_Category_ID)
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

-- ERASE THE OLD DATA
DELETE FROM Transfer;
DELETE FROM Debt_Loan;
DELETE FROM Budget;
DELETE FROM Goal;
DELETE FROM Transactions;
DELETE FROM Contact;
DELETE FROM Sub_Category;
DELETE FROM Category;
DELETE FROM Pocket;

-- 1. POCKET
INSERT INTO Pocket (Pocket_ID, Pocket_Name, Balance, Max_Budget, Created_Date) VALUES
(1, 'Uang Bulanan Orang Tua', 3500000, 0, '2026-01-01 08:00:00'),
(2, 'Uang Jajan Harian', 750000, 1500000, '2026-01-01 09:00:00'),
(3, 'Biaya Kost & Apartemen', 2500000, 2500000, '2026-01-01 10:00:00'),
(4, 'Tabungan Laptop / Skripsi', 5000000, 0, '2026-01-02 11:30:00'),
(5, 'Dana Darurat Mahasiswa', 3000000, 0, '2026-01-05 14:00:00'),
(6, 'Tabungan Liburan', 2000000, 0, '2026-01-06 08:00:00'),
(7, 'Dana Organisasi Kampus', 1000000, 2000000, '2026-01-07 09:00:00'),
(8, 'Tabungan Investasi', 4000000, 0, '2026-01-08 10:00:00'),
(9, 'Biaya Sertifikasi', 1500000, 3000000, '2026-01-09 11:00:00'),
(10, 'Dana Freelance', 2500000, 0, '2026-01-10 12:00:00');

-- 2. CATEGORY
INSERT INTO Category (Category_ID, Category_Name, Category_Type, Icon_Code) VALUES
(10, 'Uang Masuk', 'Income', 'ic_wallet'),
(11, 'Makanan & Minuman', 'Expense', 'ic_food'),
(12, 'Transportasi Kampus', 'Expense', 'ic_transport'),
(13, 'Beasiswa / Freelance', 'Income', 'ic_income'),
(14, 'Akademik & Digital', 'Expense', 'ic_study'),
(15, 'Hiburan', 'Expense', 'ic_entertainment'),
(16, 'Kesehatan', 'Expense', 'ic_health'),
(17, 'Organisasi', 'Expense', 'ic_organization'),
(18, 'Tabungan', 'Income', 'ic_savings'),
(19, 'Belanja Pribadi', 'Expense', 'ic_shopping');

-- 3. SUB_CATEGORY
INSERT INTO Sub_Category (Sub_Category_ID, Category_ID, Sub_Name, Notes) VALUES
(101, 10, 'Hutang Teman', 'Pinjaman uang dari teman kampus'),
(102, 10, 'Piutang Teman', 'Uang yang dipinjamkan ke teman'),
(103, 14, 'UKT / Semester Fee', 'Biaya pendidikan President University'),
(104, 12, 'Grab / Gojek Kampus', 'Transportasi ke kampus atau mall sekitar'),
(105, 14, 'Internet & Software', 'Langganan internet, AI tools, software'),
(106, 11, 'Makan Kantin / Food Court', 'Pengeluaran makan harian'),
(107, 13, 'Freelance Project', 'Pemasukan dari jasa freelance'),
(108, 15, 'Streaming & Gaming', 'Netflix, Spotify, Steam'),
(109, 16, 'Obat & Klinik', 'Biaya kesehatan mahasiswa'),
(110, 17, 'Kas Organisasi', 'Kebutuhan event organisasi'),
(111, 18, 'Deposito Mahasiswa', 'Tabungan masa depan'),
(112, 19, 'Skincare & Fashion', 'Belanja kebutuhan pribadi');

-- 4. CONTACT
INSERT INTO Contact (Contact_ID, Contact_Name, Phone_Number, Relation_Type) VALUES
(301, 'Keefi Telkomsel', '08122026001', 'Teman Kampus'),
(302, 'Fairaz Samsung', '08772026002', 'Partner Projek'),
(303, 'Mark WhatsApp', '08192026003', 'Yang Punya WhatsApp'),
(304, 'Bank Jago', '15002026', 'Bank Digital'),
(305, 'President University Finance Office', '02155778899', 'Institusi Kampus'),
(306, 'Dosen Pembimbing', '08122026006', 'Akademik'),
(307, 'Teman Kelas SI', '08122026007', 'Teman Kampus'),
(308, 'Bapak Kost', '08122026008', 'Tempat Tinggal'),
(309, 'Shopee PayLater', '15007000', 'Finansial'),
(310, 'Senior Wibu', '08122026010', 'Mentor');

-- 5. TRANSACTIONS
INSERT INTO Transactions (Transaction_ID, Pocket_ID, Category_ID, Amount, System_Log, Description, Warning_Status) VALUES
(501, 1, 10, 3500000, '2026-02-01 00:00:01', 'Transfer bulanan dari orang tua', FALSE),
(502, 2, 11, 45000, '2026-03-01 12:30:00', 'Makan siang food court PU', FALSE),
(503, 2, 11, 250000, '2026-03-02 19:00:00', 'Nongkrong di Point', TRUE),
(504, 3, 14, 1500000, '2026-03-05 08:00:00', 'Bayar kost bulanan', FALSE),
(505, 2, 11, 25000, '2026-03-06 10:00:00', 'Ngopi di kampus', FALSE),
(506, 2, 12, 30000, '2026-03-07 08:00:00', 'Naik ojek ke kampus', FALSE),
(507, 4, 14, 500000, '2026-03-08 13:00:00', 'Bayar sertifikasi online', FALSE),
(508, 10, 13, 1200000, '2026-03-09 21:00:00', 'Pembayaran freelance website', FALSE),
(509, 2, 15, 150000, '2026-03-10 20:00:00', 'Langganan Spotify', FALSE),
(510, 2, 19, 350000, '2026-03-11 18:00:00', 'Belanja kebutuhan pribadi', TRUE);

-- 6. GOAL
INSERT INTO Goal (Goal_ID, Pocket_ID, Goal_Name, Target_Amount, Deadline_Date) VALUES
(201, 4, 'Beli Laptop Baru', 12000000, '2026-12-25'),
(202, 5, 'Dana Darurat Semester Akhir', 10000000, '2027-06-01'),
(203, 4, 'Sertifikasi Cybersecurity', 5000000, '2026-08-17'),
(204, 4, 'Upgrade Setup Belajar', 7000000, '2026-11-11'),
(205, 5, 'Tabungan Wisuda', 15000000, '2028-01-01'),
(206, 6, 'Liburan Semester', 5000000, '2026-12-20'),
(207, 8, 'Investasi Awal', 10000000, '2027-01-01'),
(208, 9, 'Exam Certification', 3500000, '2026-10-01'),
(209, 4, 'Bangun Portfolio Device', 8000000, '2027-03-01'),
(210, 5, 'Dana Setelah Lulus', 25000000, '2028-06-01');

-- 7. BUDGET
INSERT INTO Budget (Budget_ID, Category_ID, Monthly_Limit, Start_Date, End_Date) VALUES
(601, 11, 1500000, '2026-03-01', '2026-03-31'),
(602, 12, 500000, '2026-03-01', '2026-03-31'),
(603, 14, 1000000, '2026-03-01', '2026-03-31'),
(604, 11, 1500000, '2026-04-01', '2026-04-30'),
(605, 12, 500000, '2026-04-01', '2026-04-30'),
(606, 15, 500000, '2026-03-01', '2026-03-31'),
(607, 16, 300000, '2026-03-01', '2026-03-31'),
(608, 17, 400000, '2026-03-01', '2026-03-31'),
(609, 19, 750000, '2026-03-01', '2026-03-31'),
(610, 14, 1200000, '2026-04-01', '2026-04-30');

-- 8. DEBT_LOAN
INSERT INTO Debt_Loan (Debt_ID, Contact_ID, Pocket_ID, Debt_Category, Amount, Due_Date, Status) VALUES
(401, 301, 2, 101, 500000, '2026-05-01', 'Belum Lunas'),
(402, 302, 2, 102, 300000, '2026-03-15', 'Lunas'),
(403, 304, 3, 101, 2500000, '2026-04-01', 'Cicilan Aktif'),
(404, 303, 2, 102, 200000, '2026-06-10', 'Belum Dibayar'),
(405, 301, 2, 101, 750000, '2026-07-20', 'Belum Lunas'),
(406, 307, 2, 101, 250000, '2026-08-01', 'Belum Lunas'),
(407, 309, 2, 101, 1200000, '2026-09-15', 'Cicilan Aktif'),
(408, 310, 4, 102, 500000, '2026-04-20', 'Belum Dibayar'),
(409, 306, 1, 102, 300000, '2026-05-10', 'Lunas'),
(410, 308, 3, 101, 1000000, '2026-06-30', 'Belum Lunas');

-- 9. TRANSFER
INSERT INTO Transfer (Transfer_ID, Source_Pocket_ID, Target_Pocket_ID, Transfer_Amount, Transfer_Date) VALUES
(701, 1, 2, 1000000, '2026-03-01 08:00:00'),
(702, 1, 3, 2500000, '2026-03-01 08:05:00'),
(703, 1, 4, 500000, '2026-03-01 08:10:00'),
(704, 2, 1, 100000, '2026-03-15 15:00:00'),
(705, 1, 5, 500000, '2026-03-16 10:00:00'),
(706, 1, 6, 300000, '2026-03-17 09:00:00'),
(707, 10, 8, 700000, '2026-03-18 10:00:00'),
(708, 1, 9, 500000, '2026-03-19 11:00:00'),
(709, 8, 5, 400000, '2026-03-20 12:00:00'),
(710, 2, 7, 150000, '2026-03-21 13:00:00');