<?= $this->include('admin/layouts/header') ?>

<style>
    .page-header {
        margin-bottom: 24px;
    }
    
    .page-title {
        font-size: 24px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .breadcrumb {
        font-size: 14px;
        color: #6b7280;
    }
    
    .breadcrumb a {
        color: #14b8a6;
        text-decoration: none;
    }
    
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    
    .card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        border: 1px solid #e5e7eb;
        max-width: 600px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .form-row.single {
        grid-template-columns: 1fr;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-weight: 500;
        font-size: 14px;
    }
    
    .form-group label.required::after {
        content: " *";
        color: #ef4444;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #14b8a6;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
    }
    
    .form-control.is-invalid {
        border-color: #ef4444;
    }
    
    .invalid-feedback {
        color: #ef4444;
        font-size: 13px;
        margin-top: 4px;
    }
    
    .form-hint {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }
    
    .btn {
        padding: 10px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        display: inline-block;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: #14b8a6;
        color: #ffffff;
    }
    
    .btn-primary:hover {
        background: #0d9488;
    }
    
    .btn-secondary {
        background: #6b7280;
        color: #ffffff;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #e5e7eb;
    }
</style>

<div class="page-header">
    <h1 class="page-title"><?= isset($user) ? 'Edit' : 'Tambah' ?> User</h1>
    <div class="breadcrumb">
        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a> / 
        <a href="<?= base_url('admin/users') ?>">User Management</a> / 
        <?= isset($user) ? 'Edit' : 'Tambah' ?>
    </div>
</div>

<div class="card">
    <form action="<?= isset($user) ? base_url('admin/users/update/' . $user['id_user']) : base_url('admin/users/store') ?>" method="POST">
        
        <div class="form-row">
            <div class="form-group">
                <label for="username" class="required">Username</label>
                <input 
                    type="text" 
                    class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                    id="username" 
                    name="username" 
                    placeholder="Username" 
                    value="<?= old('username', $user['username'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['username'])): ?>
                    <div class="invalid-feedback"><?= $errors['username'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email" class="required">Email</label>
                <input 
                    type="email" 
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                    id="email" 
                    name="email" 
                    placeholder="email@example.com" 
                    value="<?= old('email', $user['email'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-row single">
            <div class="form-group">
                <label for="nama_lengkap" class="required">Nama Lengkap</label>
                <input 
                    type="text" 
                    class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>" 
                    id="nama_lengkap" 
                    name="nama_lengkap" 
                    placeholder="Nama Lengkap" 
                    value="<?= old('nama_lengkap', $user['nama_lengkap'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['nama_lengkap'])): ?>
                    <div class="invalid-feedback"><?= $errors['nama_lengkap'] ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="password" class="<?= !isset($user) ? 'required' : '' ?>">Password</label>
                <input 
                    type="password" 
                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                    id="password" 
                    name="password" 
                    placeholder="<?= isset($user) ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 6 karakter' ?>"
                    <?= !isset($user) ? 'required' : '' ?>
                >
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?= $errors['password'] ?></div>
                <?php endif; ?>
                <?php if (isset($user)): ?>
                    <div class="form-hint">Kosongkan jika tidak ingin mengubah password</div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password_confirm" class="<?= !isset($user) ? 'required' : '' ?>">Konfirmasi Password</label>
                <input 
                    type="password" 
                    class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" 
                    id="password_confirm" 
                    name="password_confirm" 
                    placeholder="Ulangi password"
                    <?= !isset($user) ? 'required' : '' ?>
                >
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="invalid-feedback"><?= $errors['password_confirm'] ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="role" class="required">Role</label>
                <select 
                    class="form-control <?= isset($errors['role']) ? 'is-invalid' : '' ?>" 
                    id="role" 
                    name="role"
                    required
                >
                    <option value="">-- Pilih Role --</option>
                    <option value="super_admin" <?= (old('role', $user['role'] ?? '') === 'super_admin') ? 'selected' : '' ?>>
                        Super Admin
                    </option>
                    <option value="admin" <?= (old('role', $user['role'] ?? '') === 'admin') ? 'selected' : '' ?>>
                        Admin
                    </option>
                </select>
                <?php if (isset($errors['role'])): ?>
                    <div class="invalid-feedback"><?= $errors['role'] ?></div>
                <?php endif; ?>
            </div>
            
            <?php if (isset($user)): ?>
            <div class="form-group">
                <label for="status" class="required">Status</label>
                <select 
                    class="form-control" 
                    id="status" 
                    name="status"
                    required
                >
                    <option value="aktif" <?= (old('status', $user['status'] ?? '') === 'aktif') ? 'selected' : '' ?>>
                        Aktif
                    </option>
                    <option value="nonaktif" <?= (old('status', $user['status'] ?? '') === 'nonaktif') ? 'selected' : '' ?>>
                        Nonaktif
                    </option>
                </select>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= isset($user) ? 'Update' : 'Simpan' ?>
            </button>
            <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
        
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>