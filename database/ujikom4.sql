-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Apr 2026 pada 16.18
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ujikom4`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tingkat` int(11) DEFAULT 10,
  `jurusan` varchar(50) DEFAULT 'RPL',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id`, `nama`, `tingkat`, `jurusan`, `created_at`) VALUES
(6, 'Muhammad Dzikril Hakim', 10, 'TKJ', '2026-04-26 11:39:39'),
(7, 'Pandji Ismunandar', 10, 'RPL', '2026-04-25 13:02:47'),
(8, 'Iqbal Fauzan Maulana', 10, 'RPL', '2026-04-20 13:16:24'),
(9, 'Agis Saputra', 10, 'RPL', '2026-04-20 13:16:48'),
(10, 'Alief Nur Shabri Aqila', 10, 'DKV', '2026-04-20 13:17:07'),
(11, 'Billie Rizky Muzibu', 10, 'RPL', '2026-04-20 13:17:28'),
(12, 'Muhammad Khodi Rabbani', 10, 'DKV', '2026-04-20 13:17:51'),
(13, 'Fikri Khairul Umam', 10, 'RPL', '2026-04-20 13:18:13'),
(14, 'Fadhil Muhammad', 10, 'RPL', '2026-04-20 13:18:29'),
(17, 'Rapli Putra Pamungkas', 12, 'TKRO', '2026-04-20 14:04:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pengarang` varchar(100) NOT NULL,
  `tahun_terbit` int(11) DEFAULT NULL,
  `jenis` varchar(100) DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `harga` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`id`, `judul`, `pengarang`, `tahun_terbit`, `jenis`, `stok`, `harga`, `created_at`, `updated_at`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 2005, 'Novel', 5, 45000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(2, 'Sang Pemimpi', 'Andrea Hirata', 2006, 'Novel', 3, 48000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(3, 'Ketika Cinta Bertasbih', 'Tere Liye', 2009, 'Novel Religi', 2, 40000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(4, 'Buku Pintar HTML & CSS', 'Aryadevi Wicaksono', 2015, 'Teknis', 4, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(5, 'Dasar-Dasar PHP', 'Budi Raharjo', 2010, 'Teknis', 3, 89000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(6, 'Matahari', 'Reuben Jonathan Miller', 2017, 'Fiksi', 2, 42000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(7, 'Filosofi Teras', 'Henry Manampiring', 2018, 'Self-Help', 6, 75000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(8, 'Atomic Habits', 'James Clear', 2018, 'Self-Help', 4, 72000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(9, 'Gadis Kretek', 'Ratih Kumala', 2012, 'Novel', 2, 50000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(10, 'Ayat-Ayat Cinta', 'Habiburrahman El-Shirazy', 2004, 'Novel Religi', 5, 45000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(11, 'Negeri 5 Menara', 'Ahmad Fuadi', 2009, 'Novel', 4, 48000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(12, 'Anak Semua Bangsa', 'Pramoedya Ananta Toer', 1980, 'Novel Sejarah', 3, 55000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(13, 'Saman', 'Ayu Utami', 1998, 'Novel', 2, 42000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(14, 'Supernova', 'Dewi Lestari', 2001, 'Novel Fiksi', 3, 50000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(15, 'Perahu Kertas', 'Dewi Lestari', 2003, 'Novel', 4, 48000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(16, 'Sang Api Kinanti', 'Dewi Lestari', 2005, 'Novel', 2, 45000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(17, 'Ketika Cinta Bertasbih 2', 'Tere Liye', 2010, 'Novel Religi', 3, 40000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(18, 'Remaja Mekkah', 'Tere Liye', 2016, 'Novel', 2, 45000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(19, 'Garis Waktu', 'Tere Liye', 2018, 'Novel', 3, 50000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(20, 'Tetralogi Pulau Buru', 'Pramoedya Ananta Toer', 1999, 'Novel Sejarah', 2, 60000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(21, 'Bumi', 'Tere Liye', 2014, 'Fiksi Ilmiah', 5, 55000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(22, 'Bulan', 'Tere Liye', 2015, 'Fiksi Ilmiah', 4, 55000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(23, 'Matahari Tetap Terbit Juga', 'Tere Liye', 2016, 'Novel Religi', 3, 50000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(24, 'Kisah Dua Kota', 'Charles Dickens', 1859, 'Novel Klasik', 2, 65000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(25, 'Jane Eyre', 'Charlotte Bronte', 1847, 'Novel Klasik', 3, 62000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(26, 'Wuthering Heights', 'Emily Bronte', 1847, 'Novel Klasik', 2, 58000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(27, 'Pride and Prejudice', 'Jane Austen', 1813, 'Novel Klasik', 4, 60000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(28, 'Sense and Sensibility', 'Jane Austen', 1811, 'Novel Klasik', 2, 55000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(29, 'The Great Gatsby', 'F. Scott Fitzgerald', 1925, 'Novel Klasik', 3, 50000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(30, 'To Kill a Mockingbird', 'Harper Lee', 1960, 'Novel Klasik', 2, 52000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(31, '1984', 'George Orwell', 1949, 'Dystopia', 3, 68000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(32, 'Animal Farm', 'George Orwell', 1945, 'Satire', 2, 45000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(33, 'Brave New World', 'Aldous Huxley', 1932, 'Dystopia', 3, 65000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(34, 'Fahrenheit 451', 'Ray Bradbury', 1953, 'Dystopia', 2, 48000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(35, 'The Catcher in the Rye', 'J.D. Salinger', 1951, 'Novel', 3, 55000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(36, 'The Hobbit', 'J.R.R. Tolkien', 1937, 'Fantasi', 4, 75000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(37, 'The Lord of the Rings', 'J.R.R. Tolkien', 1954, 'Fantasi', 5, 150000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(38, 'Harry Potter and the Philosopher Stone', 'J.K. Rowling', 1997, 'Fantasi', 6, 95000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(39, 'Harry Potter and the Chamber of Secrets', 'J.K. Rowling', 1998, 'Fantasi', 5, 95000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(40, 'Harry Potter and the Prisoner of Azkaban', 'J.K. Rowling', 1999, 'Fantasi', 4, 98000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(41, 'Harry Potter and the Goblet of Fire', 'J.K. Rowling', 2000, 'Fantasi', 4, 125000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(42, 'Harry Potter and the Order of the Phoenix', 'J.K. Rowling', 2003, 'Fantasi', 3, 145000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(43, 'Harry Potter and the Half-Blood Prince', 'J.K. Rowling', 2005, 'Fantasi', 3, 140000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(44, 'Harry Potter and the Deathly Hallows', 'J.K. Rowling', 2007, 'Fantasi', 4, 150000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(45, 'The Name of the Wind', 'Patrick Rothfuss', 2007, 'Fantasi', 2, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(46, 'The Wise Mans Fear', 'Patrick Rothfuss', 2011, 'Fantasi', 2, 95000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(47, 'A Game of Thrones', 'George R.R. Martin', 1996, 'Fantasi', 3, 120000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(48, 'A Clash of Kings', 'George R.R. Martin', 1998, 'Fantasi', 2, 125000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(49, 'A Storm of Swords', 'George R.R. Martin', 2000, 'Fantasi', 2, 130000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(50, 'A Feast for Crows', 'George R.R. Martin', 2005, 'Fantasi', 2, 128000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(51, 'A Dance with Dragons', 'George R.R. Martin', 2011, 'Fantasi', 2, 135000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(52, 'The Alchemist', 'Paulo Coelho', 1988, 'Self-Help', 5, 50000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(53, 'Mastery', 'Robert Greene', 2012, 'Self-Help', 3, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(54, 'The 48 Laws of Power', 'Robert Greene', 1998, 'Self-Help', 3, 80000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(55, 'The Art of War', 'Sun Tzu', 500, 'Strategi', 4, 35000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(56, 'Sapiens', 'Yuval Noah Harari', 2014, 'Sains', 3, 120000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(57, 'Homo Deus', 'Yuval Noah Harari', 2015, 'Sains', 2, 125000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(58, '21 Lessons for the 21st Century', 'Yuval Noah Harari', 2018, 'Sains', 2, 115000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(59, 'A Brief History of Time', 'Stephen Hawking', 1988, 'Sains', 2, 75000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(60, 'The Universe in a Nutshell', 'Stephen Hawking', 2001, 'Sains', 2, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(61, 'Cosmos', 'Carl Sagan', 1980, 'Sains', 3, 95000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(62, 'The Selfish Gene', 'Richard Dawkins', 1976, 'Sains', 2, 70000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(63, 'The God Delusion', 'Richard Dawkins', 2006, 'Sains', 2, 78000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(64, 'Thinking Fast and Slow', 'Daniel Kahneman', 2011, 'Psikologi', 3, 120000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(65, 'Predictably Irrational', 'Dan Ariely', 2008, 'Psikologi', 2, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(66, 'The Tipping Point', 'Malcolm Gladwell', 2000, 'Sosiologi', 2, 75000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(67, 'Blink', 'Malcolm Gladwell', 2005, 'Psikologi', 2, 70000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(68, 'Outliers', 'Malcolm Gladwell', 2008, 'Sosiologi', 3, 75000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(69, 'Mindset', 'Carol S. Dweck', 2006, 'Self-Help', 4, 68000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(70, 'The Lean Startup', 'Eric Ries', 2011, 'Bisnis', 3, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(71, 'Good to Great', 'Jim Collins', 2001, 'Bisnis', 2, 95000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(72, 'Start with Why', 'Simon Sinek', 2009, 'Bisnis', 3, 78000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(73, 'The 7 Habits of Highly Effective People', 'Stephen Covey', 1989, 'Self-Help', 4, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(74, 'How to Win Friends and Influence People', 'Dale Carnegie', 1936, 'Self-Help', 5, 55000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(75, 'The Power of Now', 'Eckhart Tolle', 1997, 'Self-Help', 3, 65000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(76, 'Man\'s Search for Meaning', 'Viktor E. Frankl', 1946, 'Self-Help', 2, 48000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(77, 'When Breath Becomes Air', 'Paul Kalanithi', 2014, 'Biografi', 2, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(78, 'Educated', 'Tara Westover', 2018, 'Biografi', 3, 95000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(79, 'Never Finished', 'David Goggins', 2023, 'Biografi', 2, 125000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(80, 'Becoming', 'Michelle Obama', 2018, 'Biografi', 3, 105000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(81, 'Born to Run', 'Christopher McDougal', 2009, 'Olahraga', 2, 72000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(82, 'The Kite Runner', 'Khaled Hosseini', 2003, 'Novel Drama', 3, 65000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(83, 'A Thousand Splendid Suns', 'Khaled Hosseini', 2007, 'Novel Drama', 2, 68000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(84, 'The Nightingale', 'Kristin Hannah', 2015, 'Novel Sejarah', 2, 75000, '2026-04-26 11:36:24', '2026-04-20 13:13:18'),
(85, 'All the Light We Cannot See', 'Anthony Doerr', 2014, 'Novel Perang', 3, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(86, 'The Nightingale Song', 'Christina Baker Kline', 2013, 'Novel Sejarah', 2, 72000, '2026-04-26 11:36:24', '2026-04-25 14:15:54'),
(87, 'The Book Thief', 'Markus Zusak', 2005, 'Novel Perang', 2, 78000, '2026-04-26 11:36:24', '2026-04-20 11:40:13'),
(88, 'The Woman in Cabin 10', 'Ruth Ware', 2016, 'Misteri', 2, 95000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(89, 'In the Woods', 'Tana French', 2007, 'Detektif', 2, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(90, 'The Girl with the Dragon Tattoo', 'Stieg Larsson', 2005, 'Thriller', 3, 98000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(91, 'The Hunger Games', 'Suzanne Collins', 2008, 'Dystopia', 4, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(92, 'Catching Fire', 'Suzanne Collins', 2009, 'Dystopia', 3, 88000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(93, 'Mockingjay', 'Suzanne Collins', 2010, 'Dystopia', 3, 92000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(94, 'Divergent', 'Veronica Roth', 2011, 'Dystopia', 3, 82000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(95, 'Insurgent', 'Veronica Roth', 2012, 'Dystopia', 2, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(96, 'Allegiant', 'Veronica Roth', 2013, 'Dystopia', 2, 88000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(97, 'The 5th Wave', 'Rick Yancey', 2013, 'Sci-Fi', 2, 95000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(98, 'Ender\'s Game', 'Orson Scott Card', 1985, 'Sci-Fi', 2, 72000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(99, 'Dune', 'Frank Herbert', 1965, 'Sci-Fi', 3, 98000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(100, 'Foundation', 'Isaac Asimov', 1951, 'Sci-Fi', 2, 85000, '2026-04-26 11:36:24', '2026-04-26 11:36:24'),
(101, 'Neuromancer', 'William Gibson', 1984, 'Sci-Fi', 1, 75000, '2026-04-26 11:36:24', '2026-04-20 11:40:18'),
(102, 'Snow Crash', 'Neal Stephenson', 1992, 'Sci-Fi', 2, 88000, '2026-04-26 11:36:24', '2026-04-20 13:12:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `batas_kembali` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pinjam',
  `kondisi_buku` varchar(50) DEFAULT NULL,
  `halaman_rusak` int(11) DEFAULT 0,
  `catatan_kondisi` text DEFAULT NULL,
  `denda` int(11) DEFAULT 0,
  `denda_kondisi` int(11) DEFAULT 0,
  `status_approval` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_anggota`, `id_buku`, `tanggal_pinjam`, `batas_kembali`, `tanggal_kembali`, `status`, `kondisi_buku`, `halaman_rusak`, `catatan_kondisi`, `denda`, `denda_kondisi`, `status_approval`, `created_at`) VALUES
(1, 6, 87, '2026-04-20', '2026-04-22', '2026-04-25', 'kembali', 'hilang', 0, 'hilang bg maaf', 6000, 78000, 'approved', '2026-04-20 11:40:13'),
(2, 6, 101, '2026-04-20', '2026-04-21', '2026-04-26', 'kembali', 'hilang', 0, 'Maaf bg', 10000, 75000, 'approved', '2026-04-20 11:40:18'),
(3, 6, 84, '2026-04-20', '2026-04-23', '2026-04-20', 'kembali', 'aman', 0, '', 0, 0, 'approved', '2026-04-20 12:47:56'),
(4, 7, 102, '2026-04-20', '2026-04-21', '2026-04-20', 'kembali', 'aman', 0, '', 0, 0, 'approved', '2026-04-20 13:12:25'),
(5, 6, 86, '2026-04-20', '2026-04-22', '2026-04-25', 'kembali', 'aman', 0, 'aman bg\r\n', 6000, 0, 'approved', '2026-04-20 14:09:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'siswa',
  `id_anggota` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `role`, `id_anggota`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin', NULL, '2026-04-26 11:36:24'),
(7, 'dzikril', '827ccb0eea8a706c4c34a16891f84e7b', 'siswa', 6, '2026-04-26 11:39:39'),
(8, 'pandji', '827ccb0eea8a706c4c34a16891f84e7b', 'siswa', 7, '2026-04-25 13:02:47'),
(9, 'iqbal', '827ccb0eea8a706c4c34a16891f84e7b', 'siswa', 8, '2026-04-20 13:16:24'),
(10, 'agis', '827ccb0eea8a706c4c34a16891f84e7b', 'siswa', 9, '2026-04-20 13:16:48'),
(11, 'alief', '827ccb0eea8a706c4c34a16891f84e7b', 'siswa', 10, '2026-04-20 13:17:07'),
(12, 'billie', '827ccb0eea8a706c4c34a16891f84e7b', 'siswa', 11, '2026-04-20 13:17:28'),
(13, 'khodi', '827ccb0eea8a706c4c34a16891f84e7b', 'siswa', 12, '2026-04-20 13:17:51'),
(14, 'umam', '827ccb0eea8a706c4c34a16891f84e7b', 'siswa', 13, '2026-04-20 13:18:13'),
(15, 'fadel', '827ccb0eea8a706c4c34a16891f84e7b', 'siswa', 14, '2026-04-20 13:18:29'),
(16, 'mpiw', 'e10adc3949ba59abbe56e057f20f883e', 'siswa', 17, '2026-04-20 14:04:45');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_buku_judul` (`judul`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_transaksi_anggota` (`id_anggota`),
  ADD KEY `idx_transaksi_buku` (`id_buku`),
  ADD KEY `idx_transaksi_status` (`status`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `idx_user_username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
