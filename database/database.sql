-- =============================================
-- Database: ujikom4
-- Sistem Perpustakaan Sekolah
-- =============================================

-- Hapus database jika sudah ada
DROP DATABASE IF EXISTS ujikom4;

-- Buat database baru
CREATE DATABASE ujikom4 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ujikom4;

-- =============================================
-- Tabel: anggota (Data Siswa)
-- =============================================
CREATE TABLE anggota (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nama VARCHAR(100) NOT NULL,
  kelas VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabel: user (Login Admin & Siswa)
-- =============================================
CREATE TABLE user (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'siswa', -- 'admin' atau 'siswa'
  id_anggota INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_anggota) REFERENCES anggota(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabel: buku (Data Buku Perpustakaan)
-- =============================================
CREATE TABLE buku (
  id INT PRIMARY KEY AUTO_INCREMENT,
  judul VARCHAR(255) NOT NULL,
  pengarang VARCHAR(100) NOT NULL,
  tahun_terbit INT,
  jenis VARCHAR(100),
  stok INT NOT NULL DEFAULT 0,
  harga INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabel: transaksi (Data Peminjaman Buku)
-- =============================================
CREATE TABLE transaksi (
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_anggota INT NOT NULL,
  id_buku INT NOT NULL,
  tanggal_pinjam DATE NOT NULL,
  batas_kembali DATE NOT NULL,
  tanggal_kembali DATE,
  status VARCHAR(20) NOT NULL DEFAULT 'pinjam', -- 'pinjam', 'pending', or 'kembali'
  kondisi_buku VARCHAR(50), -- 'aman', 'robek', or 'hilang'
  halaman_rusak INT DEFAULT 0, -- jumlah halaman yang rusak/replok
  catatan_kondisi TEXT, -- catatan detail kondisi buku
  denda INT DEFAULT 0, -- denda berdasarkan keterlambatan
  denda_kondisi INT DEFAULT 0, -- denda tambahan berdasarkan kondisi
  status_approval VARCHAR(20), -- 'pending', 'approved', 'rejected' (untuk persetujuan admin)
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_anggota) REFERENCES anggota(id) ON DELETE CASCADE,
  FOREIGN KEY (id_buku) REFERENCES buku(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Data Dummy
-- =============================================

-- Insert Admin
-- Username: admin, Password: admin123 (hashed dengan MD5)
INSERT INTO user (id, username, password, role, id_anggota) 
VALUES (1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin', NULL);

-- Insert Data Anggota (Siswa)
INSERT INTO anggota (nama, kelas) VALUES
('Ahmad Rizki', 'XII RPL 1'),
('Budi Santoso', 'XII RPL 1'),
('Citra Dewi', 'XII RPL 2'),
('Doni Pratama', 'XII RPL 2'),
('Eka Putri', 'XII IPA 1');

-- Insert User Siswa
-- Password: pass123 (hashed dengan MD5) = 32250170a0dca92d53ec9624f336ca24
INSERT INTO user (username, password, role, id_anggota) VALUES
('Ahmad_Rizki', '32250170a0dca92d53ec9624f336ca24', 'siswa', 1),
('Budi_Santoso', '32250170a0dca92d53ec9624f336ca24', 'siswa', 2),
('Citra_Dewi', '32250170a0dca92d53ec9624f336ca24', 'siswa', 3),
('Doni_Pratama', '32250170a0dca92d53ec9624f336ca24', 'siswa', 4),
('Eka_Putri', '32250170a0dca92d53ec9624f336ca24', 'siswa', 5);

-- Insert Data Buku
INSERT INTO buku (judul, pengarang, tahun_terbit, jenis, stok, harga) VALUES
('Laskar Pelangi', 'Andrea Hirata', 2005, 'Novel', 5, 45000),
('Sang Pemimpi', 'Andrea Hirata', 2006, 'Novel', 3, 48000),
('Ketika Cinta Bertasbih', 'Tere Liye', 2009, 'Novel Religi', 2, 40000),
('Buku Pintar HTML & CSS', 'Aryadevi Wicaksono', 2015, 'Teknis', 4, 85000),
('Dasar-Dasar PHP', 'Budi Raharjo', 2010, 'Teknis', 3, 89000),
('Matahari', 'Reuben Jonathan Miller', 2017, 'Fiksi', 2, 42000),
('Filosofi Teras', 'Henry Manampiring', 2018, 'Self-Help', 6, 75000),
('Atomic Habits', 'James Clear', 2018, 'Self-Help', 4, 72000),
('Gadis Kretek', 'Ratih Kumala', 2012, 'Novel', 2, 50000),
('Ayat-Ayat Cinta', 'Habiburrahman El-Shirazy', 2004, 'Novel Religi', 5, 45000),
('Negeri 5 Menara', 'Ahmad Fuadi', 2009, 'Novel', 4, 48000),
('Anak Semua Bangsa', 'Pramoedya Ananta Toer', 1980, 'Novel Sejarah', 3, 55000),
('Saman', 'Ayu Utami', 1998, 'Novel', 2, 42000),
('Supernova', 'Dewi Lestari', 2001, 'Novel Fiksi', 3, 50000),
('Perahu Kertas', 'Dewi Lestari', 2003, 'Novel', 4, 48000),
('Sang Api Kinanti', 'Dewi Lestari', 2005, 'Novel', 2, 45000),
('Ketika Cinta Bertasbih 2', 'Tere Liye', 2010, 'Novel Religi', 3, 40000),
('Remaja Mekkah', 'Tere Liye', 2016, 'Novel', 2, 45000),
('Garis Waktu', 'Tere Liye', 2018, 'Novel', 3, 50000),
('Tetralogi Pulau Buru', 'Pramoedya Ananta Toer', 1999, 'Novel Sejarah', 2, 60000),
('Bumi', 'Tere Liye', 2014, 'Fiksi Ilmiah', 5, 55000),
('Bulan', 'Tere Liye', 2015, 'Fiksi Ilmiah', 4, 55000),
('Matahari Tetap Terbit Juga', 'Tere Liye', 2016, 'Novel Religi', 3, 50000),
('Kisah Dua Kota', 'Charles Dickens', 1859, 'Novel Klasik', 2, 65000),
('Jane Eyre', 'Charlotte Bronte', 1847, 'Novel Klasik', 3, 62000),
('Wuthering Heights', 'Emily Bronte', 1847, 'Novel Klasik', 2, 58000),
('Pride and Prejudice', 'Jane Austen', 1813, 'Novel Klasik', 4, 60000),
('Sense and Sensibility', 'Jane Austen', 1811, 'Novel Klasik', 2, 55000),
('The Great Gatsby', 'F. Scott Fitzgerald', 1925, 'Novel Klasik', 3, 50000),
('To Kill a Mockingbird', 'Harper Lee', 1960, 'Novel Klasik', 2, 52000),
('1984', 'George Orwell', 1949, 'Dystopia', 3, 68000),
('Animal Farm', 'George Orwell', 1945, 'Satire', 2, 45000),
('Brave New World', 'Aldous Huxley', 1932, 'Dystopia', 3, 65000),
('Fahrenheit 451', 'Ray Bradbury', 1953, 'Dystopia', 2, 48000),
('The Catcher in the Rye', 'J.D. Salinger', 1951, 'Novel', 3, 55000),
('The Hobbit', 'J.R.R. Tolkien', 1937, 'Fantasi', 4, 75000),
('The Lord of the Rings', 'J.R.R. Tolkien', 1954, 'Fantasi', 5, 150000),
('Harry Potter and the Philosopher Stone', 'J.K. Rowling', 1997, 'Fantasi', 6, 95000),
('Harry Potter and the Chamber of Secrets', 'J.K. Rowling', 1998, 'Fantasi', 5, 95000),
('Harry Potter and the Prisoner of Azkaban', 'J.K. Rowling', 1999, 'Fantasi', 4, 98000),
('Harry Potter and the Goblet of Fire', 'J.K. Rowling', 2000, 'Fantasi', 4, 125000),
('Harry Potter and the Order of the Phoenix', 'J.K. Rowling', 2003, 'Fantasi', 3, 145000),
('Harry Potter and the Half-Blood Prince', 'J.K. Rowling', 2005, 'Fantasi', 3, 140000),
('Harry Potter and the Deathly Hallows', 'J.K. Rowling', 2007, 'Fantasi', 4, 150000),
('The Name of the Wind', 'Patrick Rothfuss', 2007, 'Fantasi', 2, 85000),
('The Wise Mans Fear', 'Patrick Rothfuss', 2011, 'Fantasi', 2, 95000),
('A Game of Thrones', 'George R.R. Martin', 1996, 'Fantasi', 3, 120000),
('A Clash of Kings', 'George R.R. Martin', 1998, 'Fantasi', 2, 125000),
('A Storm of Swords', 'George R.R. Martin', 2000, 'Fantasi', 2, 130000),
('A Feast for Crows', 'George R.R. Martin', 2005, 'Fantasi', 2, 128000),
('A Dance with Dragons', 'George R.R. Martin', 2011, 'Fantasi', 2, 135000),
('The Alchemist', 'Paulo Coelho', 1988, 'Self-Help', 5, 50000),
('Mastery', 'Robert Greene', 2012, 'Self-Help', 3, 85000),
('The 48 Laws of Power', 'Robert Greene', 1998, 'Self-Help', 3, 80000),
('The Art of War', 'Sun Tzu', 500, 'Strategi', 4, 35000),
('Sapiens', 'Yuval Noah Harari', 2014, 'Sains', 3, 120000),
('Homo Deus', 'Yuval Noah Harari', 2015, 'Sains', 2, 125000),
('21 Lessons for the 21st Century', 'Yuval Noah Harari', 2018, 'Sains', 2, 115000),
('A Brief History of Time', 'Stephen Hawking', 1988, 'Sains', 2, 75000),
('The Universe in a Nutshell', 'Stephen Hawking', 2001, 'Sains', 2, 85000),
('Cosmos', 'Carl Sagan', 1980, 'Sains', 3, 95000),
('The Selfish Gene', 'Richard Dawkins', 1976, 'Sains', 2, 70000),
('The God Delusion', 'Richard Dawkins', 2006, 'Sains', 2, 78000),
('Thinking Fast and Slow', 'Daniel Kahneman', 2011, 'Psikologi', 3, 120000),
('Predictably Irrational', 'Dan Ariely', 2008, 'Psikologi', 2, 85000),
('The Tipping Point', 'Malcolm Gladwell', 2000, 'Sosiologi', 2, 75000),
('Blink', 'Malcolm Gladwell', 2005, 'Psikologi', 2, 70000),
('Outliers', 'Malcolm Gladwell', 2008, 'Sosiologi', 3, 75000),
('Mindset', 'Carol S. Dweck', 2006, 'Self-Help', 4, 68000),
('The Lean Startup', 'Eric Ries', 2011, 'Bisnis', 3, 85000),
('Good to Great', 'Jim Collins', 2001, 'Bisnis', 2, 95000),
('Start with Why', 'Simon Sinek', 2009, 'Bisnis', 3, 78000),
('The 7 Habits of Highly Effective People', 'Stephen Covey', 1989, 'Self-Help', 4, 85000),
('How to Win Friends and Influence People', 'Dale Carnegie', 1936, 'Self-Help', 5, 55000),
('The Power of Now', 'Eckhart Tolle', 1997, 'Self-Help', 3, 65000),
('Man\'s Search for Meaning', 'Viktor E. Frankl', 1946, 'Self-Help', 2, 48000),
('When Breath Becomes Air', 'Paul Kalanithi', 2014, 'Biografi', 2, 85000),
('Educated', 'Tara Westover', 2018, 'Biografi', 3, 95000),
('Never Finished', 'David Goggins', 2023, 'Biografi', 2, 125000),
('Becoming', 'Michelle Obama', 2018, 'Biografi', 3, 105000),
('Born to Run', 'Christopher McDougal', 2009, 'Olahraga', 2, 72000),
('The Kite Runner', 'Khaled Hosseini', 2003, 'Novel Drama', 3, 65000),
('A Thousand Splendid Suns', 'Khaled Hosseini', 2007, 'Novel Drama', 2, 68000),
('The Nightingale', 'Kristin Hannah', 2015, 'Novel Sejarah', 2, 75000),
('All the Light We Cannot See', 'Anthony Doerr', 2014, 'Novel Perang', 3, 85000),
('The Nightingale Song', 'Christina Baker Kline', 2013, 'Novel Sejarah', 2, 72000),
('The Book Thief', 'Markus Zusak', 2005, 'Novel Perang', 3, 78000),
('The Woman in Cabin 10', 'Ruth Ware', 2016, 'Misteri', 2, 95000),
('In the Woods', 'Tana French', 2007, 'Detektif', 2, 85000),
('The Girl with the Dragon Tattoo', 'Stieg Larsson', 2005, 'Thriller', 3, 98000),
('The Hunger Games', 'Suzanne Collins', 2008, 'Dystopia', 4, 85000),
('Catching Fire', 'Suzanne Collins', 2009, 'Dystopia', 3, 88000),
('Mockingjay', 'Suzanne Collins', 2010, 'Dystopia', 3, 92000),
('Divergent', 'Veronica Roth', 2011, 'Dystopia', 3, 82000),
('Insurgent', 'Veronica Roth', 2012, 'Dystopia', 2, 85000),
('Allegiant', 'Veronica Roth', 2013, 'Dystopia', 2, 88000),
('The 5th Wave', 'Rick Yancey', 2013, 'Sci-Fi', 2, 95000),
('Ender\'s Game', 'Orson Scott Card', 1985, 'Sci-Fi', 2, 72000),
('Dune', 'Frank Herbert', 1965, 'Sci-Fi', 3, 98000),
('Foundation', 'Isaac Asimov', 1951, 'Sci-Fi', 2, 85000),
('Neuromancer', 'William Gibson', 1984, 'Sci-Fi', 2, 75000),
('Snow Crash', 'Neal Stephenson', 1992, 'Sci-Fi', 2, 88000);


-- =============================================
-- Index untuk performa query
-- =============================================
CREATE INDEX idx_user_username ON user(username);
CREATE INDEX idx_transaksi_anggota ON transaksi(id_anggota);
CREATE INDEX idx_transaksi_buku ON transaksi(id_buku);
CREATE INDEX idx_transaksi_status ON transaksi(status);
CREATE INDEX idx_buku_judul ON buku(judul);
