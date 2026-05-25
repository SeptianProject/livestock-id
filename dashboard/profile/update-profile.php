<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    if (!headers_sent()) {
        header('Location: ../../auth/login.php');
        exit;
    }

    echo 'Silakan masuk terlebih dahulu.';
    return;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

function profileUserColumns(PDO $pdo): array
{
    static $columns = null;

    if (is_array($columns)) {
        return $columns;
    }

    try {
        $statement = $pdo->query('SHOW COLUMNS FROM tb_user');
        $columns = array_map(
            static fn(array $column): string => (string) $column['Field'],
            $statement->fetchAll()
        );
    } catch (Throwable $exception) {
        error_log('Gagal memuat struktur tb_user: ' . $exception->getMessage());
        $columns = [];
    }

    return $columns;
}

function profileHasColumn(array $lookup, string $column): bool
{
    return isset($lookup[$column]);
}

function profileResolveValue(array $row, array $columns, string $default = ''): string
{
    foreach ($columns as $column) {
        if (array_key_exists($column, $row) && $row[$column] !== null) {
            return trim((string) $row[$column]);
        }
    }

    return $default;
}

$profileErrors = [];
$profileSuccess = '';
$profileUserId = (int) $_SESSION['user_id'];

$profileColumnNames = profileUserColumns($pdo);
$profileColumnLookup = array_fill_keys($profileColumnNames, true);

$profileFieldDefinitions = [
    'full_name' => [
        'label' => 'Nama Lengkap',
        'columns' => ['nama_lengkap', 'full_name', 'nama_user', 'nama'],
        'type' => 'text',
        'placeholder' => 'Masukkan nama lengkap',
        'maxlength' => 100,
        'fullWidth' => false,
    ],
    'email' => [
        'label' => 'Alamat Email',
        'columns' => ['email', 'email_user'],
        'type' => 'email',
        'placeholder' => 'Masukkan alamat email',
        'maxlength' => 100,
        'fullWidth' => false,
    ],
    'phone' => [
        'label' => 'No. Telepon',
        'columns' => ['no_telp', 'telepon', 'phone', 'phone_number'],
        'type' => 'tel',
        'placeholder' => 'Masukkan nomor telepon',
        'maxlength' => 20,
        'fullWidth' => false,
    ],
    'address' => [
        'label' => 'Alamat',
        'columns' => ['alamat', 'address'],
        'type' => 'textarea',
        'placeholder' => 'Masukkan alamat',
        'maxlength' => 255,
        'fullWidth' => true,
        'rows' => 3,
    ],
    'bio' => [
        'label' => 'Bio',
        'columns' => ['bio', 'biografi', 'description'],
        'type' => 'textarea',
        'placeholder' => 'Ceritakan sedikit tentang Anda',
        'maxlength' => 500,
        'fullWidth' => true,
        'rows' => 3,
    ],
];

$profileEditableColumns = [];
foreach ($profileFieldDefinitions as $fieldKey => $definition) {
    foreach ($definition['columns'] as $columnName) {
        if (profileHasColumn($profileColumnLookup, $columnName)) {
            $profileEditableColumns[$fieldKey] = $columnName;
            break;
        }
    }
}

$profileUserStmt = $pdo->prepare('SELECT * FROM tb_user WHERE id_user = :id_user LIMIT 1');
$profileUserStmt->execute(['id_user' => $profileUserId]);
$profileUser = $profileUserStmt->fetch();

if (!$profileUser) {
    if (!headers_sent()) {
        http_response_code(404);
    }

    echo 'Data akun tidak ditemukan.';
    return;
}

$profileFormData = [
    'username' => (string) ($profileUser['username'] ?? ''),
];

foreach ($profileFieldDefinitions as $fieldKey => $definition) {
    $profileFormData[$fieldKey] = profileResolveValue($profileUser, $definition['columns']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['profile_action'] ?? '') === 'update_profile') {
    $profileFormData['username'] = trim((string) ($_POST['username'] ?? $profileFormData['username']));

    foreach ($profileFieldDefinitions as $fieldKey => $definition) {
        if (!isset($profileEditableColumns[$fieldKey])) {
            continue;
        }

        $profileFormData[$fieldKey] = trim((string) ($_POST[$fieldKey] ?? $profileFormData[$fieldKey]));
    }

    if ($profileFormData['username'] === '') {
        $profileErrors[] = 'Username wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($profileFormData['username'], 'UTF-8') : strlen($profileFormData['username'])) < 4) {
        $profileErrors[] = 'Username minimal 4 karakter.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($profileFormData['username'], 'UTF-8') : strlen($profileFormData['username'])) > 100) {
        $profileErrors[] = 'Username maksimal 100 karakter.';
    }

    $usernameCheckStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_user WHERE username = :username AND id_user <> :id_user');
    $usernameCheckStmt->execute([
        'username' => $profileFormData['username'],
        'id_user' => $profileUserId,
    ]);

    if ((int) $usernameCheckStmt->fetchColumn() > 0) {
        $profileErrors[] = 'Username sudah digunakan.';
    }

    foreach ($profileFieldDefinitions as $fieldKey => $definition) {
        if (!isset($profileEditableColumns[$fieldKey])) {
            continue;
        }

        $fieldValue = $profileFormData[$fieldKey];
        $maxLength = (int) ($definition['maxlength'] ?? 0);

        if ($fieldValue !== '' && $maxLength > 0) {
            $fieldLength = function_exists('mb_strlen') ? mb_strlen($fieldValue, 'UTF-8') : strlen($fieldValue);

            if ($fieldLength > $maxLength) {
                $profileErrors[] = sprintf('%s maksimal %d karakter.', $definition['label'], $maxLength);
            }
        }

        if (($definition['type'] ?? '') === 'email' && $fieldValue !== '' && filter_var($fieldValue, FILTER_VALIDATE_EMAIL) === false) {
            $profileErrors[] = sprintf('%s tidak valid.', $definition['label']);
        }
    }

    if ($profileErrors === []) {
        try {
            $updateClauses = ['username = :username'];
            $updateParams = [
                'username' => $profileFormData['username'],
                'id_user' => $profileUserId,
            ];

            foreach ($profileFieldDefinitions as $fieldKey => $definition) {
                if (!isset($profileEditableColumns[$fieldKey])) {
                    continue;
                }

                $columnName = $profileEditableColumns[$fieldKey];
                $updateClauses[] = $columnName . ' = :' . $columnName;
                $updateParams[$columnName] = $profileFormData[$fieldKey] !== '' ? $profileFormData[$fieldKey] : null;
            }

            $updateSql = 'UPDATE tb_user SET ' . implode(', ', $updateClauses) . ' WHERE id_user = :id_user';
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute($updateParams);

            $_SESSION['username'] = $profileFormData['username'];
            $profileUser = array_merge($profileUser, $profileFormData, ['id_user' => $profileUserId]);
            $profileSuccess = 'Profil berhasil diperbarui.';
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui profil user: ' . $exception->getMessage());
            $profileErrors[] = 'Gagal menyimpan perubahan profil. Silakan coba lagi.';
        }
    }
}

$profileVisibleFieldDefinitions = [];
foreach ($profileFieldDefinitions as $fieldKey => $definition) {
    if (isset($profileEditableColumns[$fieldKey])) {
        $profileVisibleFieldDefinitions[$fieldKey] = $definition;
    }
}

$profileRoleValue = (string) ($profileUser['role'] ?? ($_SESSION['role'] ?? 'admin'));
$profileRoleLabelMap = [
    'admin' => 'Administrator',
    'dokter' => 'Dokter',
    'petugas_lapang' => 'Petugas Lapang',
    'petugas_produksi' => 'Petugas Produksi',
];
$profileRoleLabel = $profileRoleLabelMap[$profileRoleValue] ?? ucfirst(str_replace('_', ' ', $profileRoleValue));
$profileAvatarInitials = strtoupper(substr((string) ($profileUser['username'] ?? $_SESSION['username'] ?? 'A'), 0, 2));
?>

<div class="card-panel">
    <div class="card-panel-header" style="margin-bottom: 20px">
        <div>
            <h3>Edit Informasi Profil</h3>
            <p class="subtitle">Perbarui username dan informasi akun Anda.</p>
        </div>
    </div>

    <?php if ($profileErrors !== []): ?>
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; font-size: 13px; line-height: 1.5;">
            <strong style="display: block; margin-bottom: 4px;">Terjadi kesalahan:</strong>
            <ul style="margin: 0; padding-left: 18px;">
                <?php foreach ($profileErrors as $profileError): ?>
                    <li><?php echo e($profileError); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($profileSuccess !== ''): ?>
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 12px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; font-size: 13px; line-height: 1.5;">
            <?php echo e($profileSuccess); ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="profile_action" value="update_profile" />

        <p class="form-section-title">Data Akun</p>
        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px;">
            <div>
                <label class="form-label" for="profileUsername">Username <span style="color: #e05252">*</span></label>
                <input
                    type="text"
                    id="profileUsername"
                    name="username"
                    class="form-control-custom"
                    maxlength="100"
                    required
                    autocomplete="username"
                    value="<?php echo e($profileFormData['username']); ?>" />
            </div>

            <div>
                <label class="form-label" for="profileRole">Role</label>
                <input
                    type="text"
                    id="profileRole"
                    class="form-control-custom"
                    value="<?php echo e($profileRoleLabel); ?>"
                    readonly
                    style="background: #f5f7f9; cursor: not-allowed" />
            </div>

            <?php if ($profileVisibleFieldDefinitions === []): ?>
                <div style="grid-column: 1 / -1; padding: 12px 14px; border-radius: 12px; background: #f5f7f9; color: #7c8493; font-size: 13px;">
                    Kolom profil tambahan belum tersedia di tabel users. Saat ini yang bisa diperbarui adalah username.
                </div>
            <?php else: ?>
                <?php foreach ($profileVisibleFieldDefinitions as $fieldKey => $definition): ?>
                    <div style="<?php echo !empty($definition['fullWidth']) ? 'grid-column: 1 / -1;' : ''; ?>">
                        <label class="form-label" for="profile<?php echo e(ucfirst($fieldKey)); ?>"><?php echo e($definition['label']); ?></label>
                        <?php if (($definition['type'] ?? 'text') === 'textarea'): ?>
                            <textarea
                                id="profile<?php echo e(ucfirst($fieldKey)); ?>"
                                name="<?php echo e($fieldKey); ?>"
                                class="form-control-custom"
                                rows="<?php echo (int) ($definition['rows'] ?? 3); ?>"
                                maxlength="<?php echo (int) ($definition['maxlength'] ?? 255); ?>"
                                placeholder="<?php echo e($definition['placeholder'] ?? ''); ?>"
                                style="resize: vertical"><?php echo e($profileFormData[$fieldKey]); ?></textarea>
                        <?php else: ?>
                            <input
                                type="<?php echo e($definition['type'] ?? 'text'); ?>"
                                id="profile<?php echo e(ucfirst($fieldKey)); ?>"
                                name="<?php echo e($fieldKey); ?>"
                                class="form-control-custom"
                                maxlength="<?php echo (int) ($definition['maxlength'] ?? 255); ?>"
                                placeholder="<?php echo e($definition['placeholder'] ?? ''); ?>"
                                value="<?php echo e($profileFormData[$fieldKey]); ?>" />
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

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