<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$successMessage = '';
$petugas = null;
$jabatans = [];

// Get ID dari URL
$petugas_id = (int) ($_GET['id'] ?? 0);

if ($petugas_id <= 0) {
    http_response_code(404);
    error_log('ID petugas tidak valid');
    echo 'ID petugas tidak ditemukan.';
    exit;
}

// Load data jabatan
try {
    $jabatanStmt = $pdo->query('SELECT * FROM tb_jabatan ORDER BY nama_jabatan ASC');
    $jabatans = $jabatanStmt->fetchAll();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data jabatan: ' . $exception->getMessage());
    echo 'Gagal memuat data jabatan. Silakan coba lagi nanti.';
    exit;
}

// Load data petugas
try {
    $petugasStmt = $pdo->prepare('SELECT * FROM tb_petugas WHERE id_petugas = :id_petugas');
    $petugasStmt->execute(['id_petugas' => $petugas_id]);
    $petugas = $petugasStmt->fetch();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data petugas: ' . $exception->getMessage());
    echo 'Gagal memuat data petugas. Silakan coba lagi nanti.';
    exit;
}

if (!$petugas) {
    http_response_code(404);
    error_log('Petugas dengan ID ' . $petugas_id . ' tidak ditemukan');
    echo 'Petugas tidak ditemukan.';
    exit;
}

$formData = [
    'nama_petugas' => $petugas['nama_petugas'] ?? '',
    'id_jabatan' => (string) ($petugas['id_jabatan'] ?? ''),
    'id_user' => (string) ($petugas['id_user'] ?? ''),
    'no_telp' => $petugas['no_telp'] ?? '',
];

$users = [];
try {
    $currentUserId = $formData['id_user'] !== '' ? (int) $formData['id_user'] : 0;
    $userStmt = $pdo->prepare(
        "SELECT u.id_user, u.username, u.role
         FROM tb_user u
         LEFT JOIN tb_petugas p ON p.id_user = u.id_user AND p.id_petugas <> :id_petugas
         WHERE u.role <> 'admin' AND (p.id_user IS NULL OR u.id_user = :current_user_id)
         ORDER BY u.username ASC"
    );
    $userStmt->execute([
        'id_petugas' => $petugas_id,
        'current_user_id' => $currentUserId,
    ]);
    $users = $userStmt->fetchAll();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data user: ' . $exception->getMessage());
    echo 'Gagal memuat data user. Silakan coba lagi nanti.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string) ($_POST['action'] ?? 'save'));

    foreach ($formData as $key => $defaultValue) {
        $formData[$key] = trim((string) ($_POST[$key] ?? $defaultValue));
    }

    if ($formData['nama_petugas'] === '') {
        $errors[] = 'Nama petugas wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['nama_petugas'], 'UTF-8') : strlen($formData['nama_petugas'])) > 100) {
        $errors[] = 'Nama petugas maksimal 100 karakter.';
    }

    if ($formData['id_jabatan'] === '') {
        $errors[] = 'Jabatan wajib diisi.';
    }

    if ($formData['id_user'] !== '' && !ctype_digit($formData['id_user'])) {
        $errors[] = 'Akun user tidak valid.';
    }

    if ($formData['no_telp'] === '') {
        $errors[] = 'Nomor telepon wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['no_telp'], 'UTF-8') : strlen($formData['no_telp'])) > 20) {
        $errors[] = 'Nomor telepon maksimal 20 karakter.';
    }

    if ($errors === []) {
        try {
            $updateStmt = $pdo->prepare(
                'UPDATE tb_petugas 
                 SET nama_petugas = :nama_petugas, id_jabatan = :id_jabatan, id_user = :id_user, no_telp = :no_telp 
                 WHERE id_petugas = :id_petugas'
            );

            $updateStmt->execute([
                'nama_petugas' => $formData['nama_petugas'],
                'id_jabatan' => $formData['id_jabatan'],
                'id_user' => $formData['id_user'] !== '' ? (int) $formData['id_user'] : null,
                'no_telp' => $formData['no_telp'],
                'id_petugas' => $petugas_id,
            ]);

            header('Location: index.php?success=Data petugas berhasil diubah');
            exit;
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui data petugas: ' . $exception->getMessage());
            $errors[] = 'Gagal menyimpan data petugas. Pastikan nama petugas maksimal 100 karakter, nomor telepon maksimal 20 karakter, dan jabatan dipilih dengan benar.';
        }
    }
}

?>

<div class="card-panel">
     <div class="card-panel-header" style="margin-bottom: 20px">
         <div>
             <h3>Edit Informasi Profil</h3>
             <p class="subtitle">
                 Perbarui data diri dan informasi akun Anda.
             </p>
         </div>
     </div>

     <form action="" method="POST">
         <p class="form-section-title">Data Pribadi</p>
         <div
             style="
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 14px;
                  ">
             <div>
                 <label class="form-label" for="namaDepan">Nama Depan</label>
                 <input
                     type="text"
                     id="namaDepan"
                     name="nama_depan"
                     class="form-control-custom"
                     value="Admin" />
             </div>
             <div>
                 <label class="form-label" for="namaBelakang">Nama Belakang</label>
                 <input
                     type="text"
                     id="namaBelakang"
                     name="nama_belakang"
                     class="form-control-custom"
                     value="Sistem" />
             </div>
             <div>
                 <label class="form-label" for="noHP">No. Telepon</label>
                 <input
                     type="tel"
                     id="noHP"
                     name="telepon"
                     class="form-control-custom"
                     value="+62 812-0000-0000" />
             </div>
             <div>
                 <label class="form-label" for="jabatanProfil">Jabatan</label>
                 <input
                     type="text"
                     id="jabatanProfil"
                     name="jabatan"
                     class="form-control-custom"
                     value="Administrator"
                     readonly
                     style="background: #f5f7f9; cursor: not-allowed" />
             </div>
             <div style="grid-column: 1/-1">
                 <label class="form-label" for="emailProfil">Alamat Email</label>
                 <input
                     type="email"
                     id="emailProfil"
                     name="email"
                     class="form-control-custom"
                     value="admin@livestock.id" />
             </div>
             <div style="grid-column: 1/-1">
                 <label class="form-label" for="alamat">Alamat</label>
                 <textarea
                     id="alamat"
                     name="alamat"
                     class="form-control-custom"
                     rows="2"
                     style="resize: vertical">
Jl. Peternakan No. 1, Boyolali, Jawa Tengah</textarea>
             </div>
         </div>

         <p class="form-section-title" style="margin-top: 24px">
             Biografi
         </p>
         <textarea
             name="bio"
             class="form-control-custom"
             rows="3"
             style="resize: vertical"
             placeholder="Ceritakan sedikit tentang Anda...">
Administrator sistem LivestockID yang bertanggung jawab atas pengelolaan data ternak, kandang, dan petugas.</textarea>

         <div class="form-actions" style="margin-top: 20px">
             <button type="submit" class="btn-primary-custom">
                 <i class="bi bi-check-lg"></i> Simpan Perubahan
             </button>
             <button type="reset" class="btn-secondary-custom">
                 <i class="bi bi-arrow-counterclockwise"></i> Reset
             </button>
         </div>
     </form>
 </div>