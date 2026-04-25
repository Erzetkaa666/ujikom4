-- =============================================
-- UPDATE DATABASE: Pisahkan Kelas dan Jurusan
-- Struktur baru: tingkat (10/11/12) + jurusan (RPL/DKV/TKJ/ANI/TKRO/TITL/AKL)
-- =============================================
-- CARA MENJALANKAN:
-- 1. Buka MySQL/phpMyAdmin
-- 2. Pilih database: ujikom4
-- 3. Buka tab SQL
-- 4. Copy-paste semua perintah di file ini
-- 5. Klik tombol Execute/Run
-- =============================================

-- =============================================
-- STEP 1: Tambahkan kolom tingkat dan jurusan
-- =============================================
ALTER TABLE anggota ADD COLUMN tingkat INT DEFAULT 10 AFTER nama;
ALTER TABLE anggota ADD COLUMN jurusan VARCHAR(50) DEFAULT 'RPL' AFTER tingkat;

-- =============================================
-- STEP 2: Migrasi data kelas lama ke ekstraksi tingkat dan jurusan
-- (Hanya jika ada data anggota yang sudah ada sebelumnya)
-- Format lama: "XII RPL 1" → tingkat=12, jurusan=RPL
-- =============================================
UPDATE anggota SET 
  tingkat = CASE 
    WHEN kelas LIKE 'X %' THEN 10
    WHEN kelas LIKE 'XI %' THEN 11
    WHEN kelas LIKE 'XII %' THEN 12
    ELSE 10
  END,
  jurusan = CASE 
    WHEN kelas LIKE '%RPL%' THEN 'RPL'
    WHEN kelas LIKE '%DKV%' THEN 'DKV'
    WHEN kelas LIKE '%TKJ%' THEN 'TKJ'
    WHEN kelas LIKE '%ANI%' THEN 'ANI'
    WHEN kelas LIKE '%TKRO%' THEN 'TKRO'
    WHEN kelas LIKE '%TITL%' THEN 'TITL'
    WHEN kelas LIKE '%AKL%' THEN 'AKL'
    ELSE 'RPL'
  END
WHERE kelas IS NOT NULL AND kelas != '';

-- =============================================
-- STEP 3: (OPSIONAL) Hapus kolom kelas lama jika sudah tidak digunakan
-- Uncomment baris di bawah jika ingin menghapus kolom kelas
-- =============================================
-- ALTER TABLE anggota DROP COLUMN kelas;

-- =============================================
-- VERIFIKASI
-- =============================================
-- Jalankan query di bawah untuk verifikasi data berhasil dimigrasikan:
-- SELECT id, nama, tingkat, jurusan FROM anggota;

-- =============================================
-- CATATAN PENTING
-- =============================================
-- Setiap anggota baru akan menggunakan struktur:
-- - Column: id, nama, tingkat (INT: 10/11/12), jurusan (VARCHAR: RPL/DKV/TKJ/ANI/TKRO/TITL/AKL)
-- Warna badge untuk setiap jurusan:
--   - RPL: Biru (primary)
--   - DKV: Merah (danger)
--   - TKJ: Hijau (success)
--   - ANI: Kuning/Orange (warning)
--   - TKRO: Cyan (info)
--   - TITL: Abu-abu (secondary)
--   - AKL: Hitam (dark)
