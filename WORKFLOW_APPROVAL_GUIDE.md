# Panduan Workflow Approval System

## 📋 Overview

Sistem Workflow Approval telah berhasil diimplementasikan untuk mengelola pengajuan izin, cuti, verifikasi absen, dan lembur dengan jenjang persetujuan yang terstruktur.

## 🔄 Alur Workflow

### Jenjang Persetujuan:
1. **Pengajuan** → PIC mengajukan untuk karyawan
2. **Approval Atasan** → Atasan langsung (berdasarkan hierarki unit/divisi)  
3. **Approval HRD** → Final approval oleh HRD

### Status Workflow:
- `pending` - Menunggu approval atasan
- `approved_supervisor` - Disetujui atasan, menunggu HRD
- `approved_hrd` - Disetujui HRD (final)
- `rejected` - Ditolak
- `cancelled` - Dibatalkan

## 🚀 Setup dan Instalasi

### 1. Jalankan Migration dan Seeder

```bash
# Jalankan migration
php artisan migrate

# Atau gunakan command setup lengkap
php artisan workflow:setup --seed
```

### 2. Konfigurasi Environment

Tambahkan ke file `.env`:

```env
# WhatsApp API Configuration (opsional)
WHATSAPP_API_URL=your_whatsapp_api_url
WHATSAPP_API_KEY=your_whatsapp_api_key

# Email Configuration (pastikan sudah dikonfigurasi)
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Setup Approval Levels

1. Login sebagai admin
2. Buka **Settings > Approval Levels**
3. Tambah approval level untuk setiap unit/divisi
4. Assign supervisor untuk setiap level

### 4. Setup User Roles

Update user dengan role yang sesuai:

```php
// Set user sebagai HRD
$user = User::find($userId);
$user->is_hrd = true;
$user->save();

// Set user sebagai Supervisor
$user = User::find($userId);
$user->is_supervisor = true;
$user->supervised_units = [1, 2, 3]; // Array unit IDs
$user->supervised_divisis = [1, 2]; // Array divisi IDs
$user->save();
```

## 📱 Fitur Utama

### 1. Dashboard Workflow Approval

- **URL**: `/workflow/dashboard`
- **Akses**: Supervisor dan HRD
- **Fitur**:
  - Summary pending approvals
  - List pengajuan berdasarkan kategori
  - Approve/Reject langsung dari dashboard
  - Real-time notification counter

### 2. Pengaturan Approval Levels

- **URL**: `/settings/approval-levels`
- **Akses**: Admin
- **Fitur**:
  - Manage supervisor untuk unit/divisi
  - Activate/deactivate approval levels
  - Bulk assignment

### 3. Notifikasi Otomatis

- **Email**: Dikirim ke semua stakeholder
- **WhatsApp**: Dikirim jika nomor terverifikasi
- **Trigger**:
  - Pengajuan baru → Notifikasi ke supervisor
  - Approved supervisor → Notifikasi ke HRD
  - Final approval/rejection → Notifikasi ke pengaju

## 🔧 Penggunaan API

### Approve by Supervisor

```javascript
POST /workflow/approve-supervisor
{
    "type": "cuti|izin|lembur|verifikasi",
    "id": 123,
    "notes": "Catatan opsional"
}
```

### Approve by HRD

```javascript
POST /workflow/approve-hrd
{
    "type": "cuti|izin|lembur|verifikasi", 
    "id": 123,
    "notes": "Catatan opsional"
}
```

### Reject Request

```javascript
POST /workflow/reject
{
    "type": "cuti|izin|lembur|verifikasi",
    "id": 123,
    "reason": "Alasan penolakan (wajib)"
}
```

### Get Pending Approvals

```javascript
GET /workflow/pending-approvals?type=all|cuti|izin|lembur|verifikasi
```

## 🎯 Integrasi dengan Model Existing

Semua model existing (Cuti, Izin, Lembur, VerifikasiAbsen) telah diupdate dengan:

### Field Baru:
- `status` - Status workflow
- `submitted_by` - User yang mengajukan
- `approved_by_supervisor` - Supervisor yang approve
- `approved_by_hrd` - HRD yang approve
- `submitted_at` - Waktu pengajuan
- `approved_supervisor_at` - Waktu approve supervisor
- `approved_hrd_at` - Waktu approve HRD
- `rejection_reason` - Alasan penolakan
- `supervisor_notes` - Catatan supervisor
- `hrd_notes` - Catatan HRD

### Method Baru:
- `approveBySupervisor($notes)` - Approve oleh supervisor
- `approveByHrd($notes)` - Approve oleh HRD
- `reject($reason)` - Tolak pengajuan
- `cancel($reason)` - Batalkan pengajuan
- `canBeApprovedBySupervisor($userId)` - Check permission
- `canBeApprovedByHrd($userId)` - Check permission

### Relationship Baru:
- `submittedBy()` - User yang mengajukan
- `approvedBySupervisor()` - Supervisor yang approve
- `approvedByHrd()` - HRD yang approve
- `workflowHistories()` - History workflow
- `employee()` - Employee terkait

## 📊 Monitoring dan Tracking

### Workflow History

Setiap perubahan status tercatat dalam tabel `workflow_histories`:

```php
// Get workflow history
$cuti = Cuti::find(1);
$histories = $cuti->workflowHistories;

foreach ($histories as $history) {
    echo $history->action_label; // "Diajukan", "Disetujui Atasan", etc.
    echo $history->user->nama; // User yang melakukan action
    echo $history->notes; // Catatan
    echo $history->created_at; // Waktu action
}
```

### Scope Queries

```php
// Get pending approvals
$pendingCuti = Cuti::pendingApproval()->get();

// Get approved items
$approvedCuti = Cuti::approved()->get();

// Get by status
$rejectedCuti = Cuti::byStatus('rejected')->get();
```

## 🔒 Security dan Permission

### Middleware Protection

- Route workflow dilindungi `WorkflowApprovalAccess` middleware
- Hanya supervisor dan HRD yang bisa akses
- Permission check di level method

### Permission Logic

```php
// Check if user can approve for specific employee
$user = Auth::user();
$canApprove = $user->canApproveForEmployee($employeeId);

// Check workflow permission
$cuti = Cuti::find(1);
$canApproveBySupervisor = $cuti->canBeApprovedBySupervisor();
$canApproveByHrd = $cuti->canBeApprovedByHrd();
```

## 🐛 Troubleshooting

### Common Issues:

1. **Notifikasi tidak terkirim**
   - Check email configuration
   - Check WhatsApp API settings
   - Check user email/phone verification

2. **Permission denied**
   - Pastikan user memiliki role supervisor/HRD
   - Check approval level configuration
   - Verify employee-supervisor mapping

3. **Approval tidak muncul**
   - Check employee unit/divisi assignment
   - Verify approval level is active
   - Check supervisor assignment

### Debug Commands:

```bash
# Check queue jobs (jika menggunakan queue)
php artisan queue:work

# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## 📈 Future Enhancements

Fitur yang bisa ditambahkan di masa depan:

1. **Multi-level approval** - Lebih dari 2 level
2. **Conditional approval** - Approval berdasarkan kondisi tertentu
3. **Bulk approval** - Approve multiple items sekaligus
4. **Approval delegation** - Delegate approval ke user lain
5. **Advanced reporting** - Analytics workflow approval
6. **Mobile app integration** - Push notification ke mobile
7. **Integration with external systems** - LDAP, Active Directory, etc.

## 📞 Support

Jika ada pertanyaan atau issue, silakan hubungi tim development atau buat issue di repository project.
