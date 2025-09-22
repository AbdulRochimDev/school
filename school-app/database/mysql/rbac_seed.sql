-- Seed core roles (idempotent)
INSERT IGNORE INTO roles (name, slug) VALUES
 ('Super Admin','super_admin'),
 ('Admin','admin'),
 ('Admin Akademik','admin_akademik'),
 ('Admin Keuangan','admin_keuangan'),
 ('Operator PPDB','operator_ppdb'),
 ('Guru','guru'),
 ('Wali Kelas','wali_kelas'),
 ('Siswa','siswa');

