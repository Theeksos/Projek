<<<<<<< HEAD

>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4

CREATE DATABASE IF NOT EXISTS doughco_hub;
USE doughco_hub;

<<<<<<< HEAD
-- Tabel user, menyimpan Owner, Mitra, dan Staf (RBAC)
CREATE TABLE IF NOT EXISTS tb_user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  
CREATE TABLE IF NOT EXISTS tb_user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  
>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('owner', 'mitra', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

<<<<<<< HEAD

>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
INSERT INTO tb_user (username, password, nama_lengkap, role) VALUES
('owner1', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Jova (Owner)', 'owner'),
('mitra1', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Ryan (Mitra)', 'mitra'),
('staff1', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Staf Kios A', 'staff');
