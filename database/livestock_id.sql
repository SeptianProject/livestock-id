CREATE DATABASE IF NOT EXISTS livestock_id CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE livestock_id;

CREATE TABLE IF NOT EXISTS kandang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_kandang VARCHAR(30) NOT NULL UNIQUE,
    nama_kandang VARCHAR(120) NOT NULL,
    lokasi VARCHAR(120) NOT NULL,
    kapasitas INT NOT NULL,
    terisi INT NOT NULL DEFAULT 0,
    jenis_ternak VARCHAR(50) NULL,
    status ENUM('aktif', 'nonaktif', 'perbaikan') NOT NULL DEFAULT 'aktif',
    luas INT NULL,
    fasilitas VARCHAR(255) NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO kandang (kode_kandang, nama_kandang, lokasi, kapasitas, terisi, jenis_ternak, status, luas, fasilitas, catatan)
VALUES
('KDG-01', 'Kandang Sapi Perah A', 'Blok Utara', 100, 92, 'Sapi Perah', 'aktif', 280, 'Ventilasi, tempat minum otomatis', 'Kondisi baik'),
('KDG-02', 'Kandang Sapi Potong B', 'Blok Selatan', 80, 68, 'Sapi Potong', 'aktif', 220, 'Drainase, lampu pemanas', 'Perlu inspeksi bulanan'),
('KDG-03', 'Kandang Kambing Domba', 'Blok Barat', 120, 68, 'Campuran', 'perbaikan', 250, 'Pagar stainless, CCTV', 'Renovasi sebagian area timur');
