# Dokumentasi Function Store Cuti

## Overview
Function `storeCuti` adalah method untuk menyimpan pengajuan cuti ke dalam tabel `perizinans`. Function ini menangani validasi input, upload file lampiran, perhitungan hari kerja, dan penyimpanan data dengan transaction untuk memastikan data integrity.

## Struktur Database

### Tabel `perizinans`
```sql
- id (Primary Key)
- user_id (Foreign Key ke users - pengaju)
- jenis (Foreign Key ke jenis_perizinans)
- tanggal_mulai (Date)
- tanggal_selesai (Date)
- jumlah_hari (Decimal 5,2)
- durasi (Decimal - dalam menit)
- alasan (Text)
- lampiran (String - JSON array file paths)
- status (Enum: pending, disetujui, ditolak, dibatalkan)
- level_persetujuan_saat_ini (Integer)
- riwayat_persetujuan (JSON)
- ditolak_oleh_user_id (Foreign Key ke users)
- komentar_penolakan (Text)
- tanggal_ditolak (Timestamp)
- tanggal_disetujui_hr (Timestamp)
- created_at, updated_at
```

### Tabel `jenis_perizinans`
```sql
- id (Primary Key)
- nama (String)
- deskripsi (Text)
- memotong_kuota (Boolean)
- level_persetujuan_dibutuhkan (Integer)
- created_at, updated_at
```

## Validasi Input

### Rules Validasi
```php
'employee_id' => 'required|integer|exists:hrd_employees,id'
'jenis_cuti' => 'required|string|in:tahunan,sakit,melahirkan,menikah,khitan,baptis,keluarga_meninggal,ibadah_haji,penting,besar'
'tanggal_mulai' => 'required|date|after_or_equal:today'
'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai'
'keterangan' => 'required|string|min:10|max:1000'
'alamat_cuti' => 'nullable|string|max:500'
'lampiran.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'
```

### Custom Error Messages
- Pesan error dalam bahasa Indonesia
- Validasi khusus untuk tanggal (tidak boleh mundur)
- Validasi file upload (format dan ukuran)

## Proses Penyimpanan

### 1. Validasi Akses Karyawan
```php
$managedEmployees = $this->getManagedEmployees();
$employee = $managedEmployees->where('id', $request->employee_id)->first();
```
- Memastikan user hanya bisa mengajukan cuti untuk karyawan yang dikelola
- Menggunakan data dari atribut `employees` di tabel users

### 2. Mapping Jenis Cuti
```php
$jenisMapping = [
    'tahunan' => ['nama' => 'Cuti Tahunan', 'memotong_kuota' => true, 'level' => 2],
    'sakit' => ['nama' => 'Cuti Sakit', 'memotong_kuota' => false, 'level' => 1],
    // ... dst
];
```
- Auto-create jenis perizinan jika belum ada
- Mapping level persetujuan berdasarkan jenis cuti

### 3. Perhitungan Hari Kerja
```php
private function calculateWorkingDays(Carbon $startDate, Carbon $endDate)
{
    $workingDays = 0;
    $current = $startDate->copy();

    while ($current->lte($endDate)) {
        if ($current->dayOfWeek !== Carbon::SATURDAY && $current->dayOfWeek !== Carbon::SUNDAY) {
            $workingDays++;
        }
        $current->addDay();
    }

    return $workingDays;
}
```
- Menghitung hari kerja (exclude weekend)
- Bisa diperluas untuk exclude hari libur nasional

### 4. Upload File Lampiran
```php
$lampiranPaths = [];
if ($request->hasFile('lampiran')) {
    foreach ($request->file('lampiran') as $file) {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('perizinan/cuti', $filename, 'public');
        $lampiranPaths[] = $path;
    }
}
```
- Multiple file upload
- Unique filename dengan timestamp dan uniqid
- Disimpan di storage/app/public/perizinan/cuti/
- Path disimpan sebagai JSON array

### 5. Riwayat Persetujuan
```php
'riwayat_persetujuan' => [
    [
        'level' => 0,
        'action' => 'submitted',
        'user_id' => Auth::id(),
        'user_name' => Auth::user()->nama,
        'employee_id' => $request->employee_id,
        'employee_name' => $employee->nama,
        'timestamp' => now(),
        'keterangan' => 'Pengajuan cuti disubmit'
    ]
]
```
- Tracking lengkap proses approval
- Siap untuk multi-level approval
- Menyimpan informasi user dan karyawan

## Error Handling

### Database Transaction
```php
try {
    DB::beginTransaction();
    // ... proses penyimpanan
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    // Cleanup uploaded files
    if (!empty($lampiranPaths)) {
        foreach ($lampiranPaths as $path) {
            Storage::disk('public')->delete($path);
        }
    }
    return redirect()->back()->with('error', $e->getMessage());
}
```

### Cleanup Strategy
- Rollback database jika ada error
- Hapus file yang sudah diupload jika transaksi gagal
- Return error message yang informatif

## Response

### Success Response
```php
return redirect()->route('perizinan.index')
    ->with('success', 'Pengajuan cuti berhasil disubmit. Nomor pengajuan: #000001');
```
- Redirect ke halaman index perizinan
- Flash message dengan nomor pengajuan
- Nomor pengajuan format 6 digit dengan leading zero

### Error Response
```php
return redirect()->back()
    ->withErrors($validator)
    ->withInput();
```
- Kembali ke form dengan error messages
- Preserve input data untuk user experience

## Jenis Cuti yang Didukung

| Jenis Cuti | Memotong Kuota | Level Approval | Keterangan |
|------------|----------------|----------------|------------|
| Tahunan | ✅ | 2 | Cuti tahunan reguler |
| Sakit | ❌ | 1 | Perlu surat dokter |
| Melahirkan | ❌ | 2 | Khusus karyawan wanita |
| Menikah | ❌ | 2 | Cuti pernikahan |
| Khitan Anak | ❌ | 1 | Cuti khitan anak |
| Baptis Anak | ❌ | 1 | Cuti baptis anak |
| Keluarga Meninggal | ❌ | 1 | Cuti duka |
| Ibadah Haji | ❌ | 2 | Cuti haji |
| Penting | ✅ | 2 | Keperluan penting |
| Besar | ✅ | 2 | Cuti besar |

## File Upload Requirements

### Supported Formats
- PDF (.pdf)
- Images (.jpg, .jpeg, .png)
- Documents (.doc, .docx)

### File Size Limit
- Maximum 5MB per file
- Multiple files allowed

### Storage Location
```
storage/app/public/perizinan/cuti/
├── timestamp_uniqid.pdf
├── timestamp_uniqid.jpg
└── ...
```

## Security Features

### Access Control
- User hanya bisa mengajukan cuti untuk karyawan yang dikelola
- Validasi employee_id terhadap managed employees

### File Security
- Unique filename untuk prevent collision
- File type validation
- Size limitation

### Data Integrity
- Database transaction
- Foreign key constraints
- Input validation

## Usage Example

### Form Submission
```html
<form action="{{ route('perizinan.cuti.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="employee_id" value="1">
    <select name="jenis_cuti">
        <option value="tahunan">Cuti Tahunan</option>
        <!-- ... -->
    </select>
    <input type="date" name="tanggal_mulai">
    <input type="date" name="tanggal_selesai">
    <textarea name="keterangan"></textarea>
    <textarea name="alamat_cuti"></textarea>
    <input type="file" name="lampiran[]" multiple>
    <button type="submit">Submit</button>
</form>
```

### Testing Data
```sql
-- Set user mengelola karyawan
UPDATE users SET employees = '[1,2,3]' WHERE id = 1;

-- Run seeder untuk jenis perizinan
php artisan db:seed --class=JenisPerizinanSeeder
```

## Future Enhancements

### Possible Improvements
1. **Holiday Integration**: Exclude public holidays from working days calculation
2. **Quota Management**: Real-time quota checking for leave types that cut quota
3. **Notification System**: Email/SMS notifications for approval workflow
4. **Approval Workflow**: Multi-level approval based on employee hierarchy
5. **Calendar Integration**: Integration with calendar systems
6. **Reporting**: Advanced reporting and analytics
7. **Mobile App**: Mobile application support
8. **API**: RESTful API for external integrations

### Performance Optimizations
1. **File Compression**: Automatic file compression for uploads
2. **Caching**: Cache frequently accessed data
3. **Queue Jobs**: Background processing for heavy operations
4. **Database Indexing**: Optimize database queries

## Troubleshooting

### Common Issues
1. **File Upload Fails**: Check storage permissions and disk space
2. **Validation Errors**: Verify form field names match validation rules
3. **Access Denied**: Ensure user has proper employee assignments
4. **Database Errors**: Check foreign key constraints and table structure

### Debug Tips
1. Enable Laravel debug mode for detailed error messages
2. Check storage/logs/laravel.log for error details
3. Verify database migrations are up to date
4. Test with sample data using seeders