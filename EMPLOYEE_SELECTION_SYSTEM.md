# Sistem Pemilihan Karyawan untuk Form Cuti

## Deskripsi
Sistem ini memungkinkan user untuk memilih karyawan yang akan diajukan cutinya berdasarkan data karyawan yang dikelola oleh user yang sedang login. Data karyawan yang dikelola disimpan dalam atribut `employees` di tabel `users` dalam format JSON array berisi ID karyawan.

## Struktur Database

### Tabel Users
- `employees` (JSON): Array berisi ID karyawan yang dikelola oleh user
  Contoh: `[1, 2, 3]` atau `["1", "2", "3"]`

### Tabel Employees (hrd_employees)
- `id`: Primary key
- `nama`: Nama karyawan
- `nip`: Nomor Induk Pegawai
- `email`: Email karyawan
- Relasi dengan `posisi`, `unit`, dan `divisi`

## Fitur yang Diimplementasikan

### 1. Controller (PerizinanController)

#### Method `getManagedEmployees()`
- Mengambil data karyawan yang dikelola berdasarkan atribut `employees` user yang login
- Jika tidak ada karyawan yang dikelola, akan mengembalikan data karyawan user itu sendiri (self-service)
- Menggunakan eager loading untuk relasi `unit`, `divisi`, dan `posisi`

#### Method `getEmployeeInfo(Request $request)`
- Endpoint AJAX untuk mendapatkan informasi detail karyawan
- URL: `/perizinan/ajax/employee-info?employee_id={id}`
- Method: GET
- Response: JSON dengan data karyawan

#### Method `cuti()`
- Mengirim data `managedEmployees` ke view untuk ditampilkan di dropdown

### 2. View (cuti.blade.php)

#### Dropdown Pemilihan Karyawan
- Ditampilkan jika user mengelola lebih dari 1 karyawan
- Jika hanya 1 karyawan, menggunakan hidden input

#### Form Fields yang Diupdate Otomatis
- Nama Karyawan
- NIP
- Jabatan
- Unit Kerja
- Sisa Cuti

#### JavaScript AJAX
- Event listener untuk perubahan dropdown
- Fungsi `loadEmployeeInfo()` untuk memuat data via AJAX
- Fungsi `clearEmployeeInfo()` untuk membersihkan form

### 3. Routes
```php
Route::get('/perizinan/cuti', [PerizinanController::class, 'cuti']);
Route::post('/perizinan/cuti/store', [PerizinanController::class, 'storeCuti']);
Route::get('/perizinan/ajax/employee-info', [PerizinanController::class, 'getEmployeeInfo']);
```

### 4. Model Updates

#### User Model
- Menambahkan cast `'employees' => 'array'` untuk otomatis parsing JSON

## Cara Penggunaan

### 1. Setup Data User
Pastikan user memiliki data di kolom `employees`:
```sql
UPDATE users SET employees = '[1,2,3]' WHERE id = 1;
```

### 2. Akses Form Cuti
- User login dan akses `/perizinan/cuti`
- Jika user mengelola multiple karyawan, dropdown akan muncul
- Pilih karyawan dari dropdown
- Form akan otomatis terisi dengan data karyawan yang dipilih

### 3. Validasi Akses
- System memvalidasi bahwa user hanya bisa mengakses karyawan yang ada di list `employees`
- Jika user tidak memiliki akses, akan muncul error 404

## Contoh Response AJAX

```json
{
    "id": 1,
    "nama": "John Doe",
    "nip": "123456",
    "email": "john@example.com",
    "jabatan": "Staff IT",
    "unit_kerja": "IT Department",
    "divisi": "Technology",
    "sisa_cuti": "12 hari"
}
```

## Error Handling

### Client Side
- Loading state saat memuat data
- Alert untuk error response
- Clear form jika terjadi error

### Server Side
- Validasi employee_id required
- Validasi akses user ke karyawan
- Return JSON error response

## Keamanan
- CSRF token validation
- User access validation
- Input sanitization
- XSS protection melalui Blade templating

## Extensibility
Sistem ini dapat diperluas untuk:
- Form izin, lembur, dan verifikasi absen
- Multiple approval levels
- Notification system
- Audit trail