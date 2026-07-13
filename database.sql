
CREATE DATABASE IF NOT EXISTS doughco_hub;
USE doughco_hub;

CREATE TABLE IF NOT EXISTS tb_user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,   
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('owner', 'mitra', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tb_user (username, password, nama_lengkap, role) VALUES
('owner1', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Jova (Owner)', 'owner'),
('mitra1', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Ryan (Mitra)', 'mitra'),
('staff1', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Staf Kios A', 'staff');
