-- Script untuk testing sistem pemilihan karyawan
-- Pastikan untuk menyesuaikan ID sesuai dengan data yang ada di database

-- 1. Update user untuk mengelola beberapa karyawan
-- Ganti user_id dan employee_ids sesuai dengan data yang ada
UPDATE users 
SET employees = '[1,2,3]' 
WHERE id = 1;

-- 2. Update user untuk mengelola satu karyawan saja (self-service)
UPDATE users 
SET employees = '[1]' 
WHERE id = 2;

-- 3. Update user tanpa karyawan yang dikelola (akan fallback ke employee_id)
UPDATE users 
SET employees = NULL, employee_id = 1 
WHERE id = 3;

-- 4. Contoh data karyawan untuk testing
-- Pastikan ada data di tabel hrd_employees
INSERT INTO hrd_employees (nama, nip, email, is_deleted) VALUES
('John Doe', '123456', 'john@example.com', 0),
('Jane Smith', '123457', 'jane@example.com', 0),
('Bob Johnson', '123458', 'bob@example.com', 0)
ON DUPLICATE KEY UPDATE nama = VALUES(nama);

-- 5. Query untuk melihat data user dan karyawan yang dikelola
SELECT 
    u.id as user_id,
    u.nama as user_name,
    u.employees,
    u.employee_id,
    e.nama as employee_name,
    e.nip
FROM users u
LEFT JOIN hrd_employees e ON u.employee_id = e.id
WHERE u.employees IS NOT NULL OR u.employee_id IS NOT NULL;

-- 6. Query untuk testing akses karyawan
SELECT 
    e.id,
    e.nama,
    e.nip,
    e.email,
    p.nama as posisi_nama,
    un.nama as unit_nama,
    d.nama as divisi_nama
FROM hrd_employees e
LEFT JOIN hrd_posisis p ON e.id = p.employee_id AND p.status = 1
LEFT JOIN hrd_units un ON p.unit_id = un.id
LEFT JOIN hrd_divisis d ON p.divisi_id = d.id
WHERE e.is_deleted = 0
AND e.id IN (1,2,3);

-- 7. Query untuk melihat struktur tabel users (untuk memastikan kolom employees ada)
DESCRIBE users;

-- 8. Query untuk melihat struktur tabel hrd_employees
DESCRIBE hrd_employees;