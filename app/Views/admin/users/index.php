<?= $this->include('admin/layouts/header') ?>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; gap:10px;">
    <div class="page-title">User Management</div>
    <button class="btn btn-primary" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> <span class="hide-xs">Tambah User</span></button>
</div>

<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th style="text-align:right;">Aksi</th></tr></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
    <div class="row-list" id="cardList" style="padding:10px;"></div>
</div>

<!-- ===================== MODAL: Tambah / Edit ===================== -->
<div class="overlay" id="userModal_overlay" onclick="closeModal('userModal')"></div>
<div class="modal" id="userModal">
    <div class="modal-drag"></div>
    <div class="modal-header"><h3 id="userModalTitle">Tambah User</h3><button type="button" class="modal-close" onclick="closeModal('userModal')"><i class="fa-solid fa-xmark"></i></button></div>
    <form id="userForm" action="<?= base_url('admin/users/store') ?>" method="POST">
        <input type="hidden" name="editing_id" id="f_editing_id" value="<?= esc(old('editing_id', '')) ?>">
        <div class="modal-body">
            <div class="field">
                <label class="required">Nama Lengkap</label>
                <input class="input" name="nama_lengkap" id="f_nama" value="<?= esc(old('nama_lengkap', '')) ?>" required>
            </div>
            <div class="field-row">
                <div class="field">
                    <label class="required">Username</label>
                    <input class="input <?= isset($errors['username']) ? 'is-invalid' : '' ?>" name="username" id="f_username" value="<?= esc(old('username', '')) ?>" required>
                    <?php if (isset($errors['username'])): ?><div class="field-error"><?= $errors['username'] ?></div><?php endif; ?>
                </div>
                <div class="field">
                    <label class="required">Email</label>
                    <input type="email" class="input <?= isset($errors['email']) ? 'is-invalid' : '' ?>" name="email" id="f_email" value="<?= esc(old('email', '')) ?>" required>
                    <?php if (isset($errors['email'])): ?><div class="field-error"><?= $errors['email'] ?></div><?php endif; ?>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label id="f_password_label" class="required">Password</label>
                    <input type="password" class="input" name="password" id="f_password" placeholder="Minimal 6 karakter" autocomplete="new-password">
                </div>
                <div class="field">
                    <label id="f_password_confirm_label" class="required">Ulangi Password</label>
                    <input type="password" class="input" name="password_confirm" id="f_password_confirm" autocomplete="new-password">
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label class="required">Role</label>
                    <select class="input" name="role" id="f_role" required>
                        <option value="tu_bendahara">TU / Bendahara</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div class="field" id="f_status_wrap" style="display:none;">
                    <label class="required">Status</label>
                    <select class="input" name="status" id="f_status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('userModal')">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Simpan</button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
const CURRENT_USER_ID = <?= (int) session()->get('id_user') ?>;
const HAS_ERRORS = <?= !empty($errors) ? 'true' : 'false' ?>;
const OLD_EDITING_ID = <?= json_encode(old('editing_id', ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const DATA = <?= json_encode($users, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

function esc(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }
function roleBadge(r) { return r === 'super_admin' ? '<span class="badge badge-brand"><i class="fa-solid fa-crown"></i> Super Admin</span>' : '<span class="badge badge-info"><i class="fa-solid fa-user-shield"></i> TU / Bendahara</span>'; }
function statusBadge(s) { return s === 'aktif' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>'; }
function canDelete(u) { return u.role !== 'super_admin' && u.id_user != CURRENT_USER_ID; }

function render() {
    document.getElementById('tableBody').innerHTML = DATA.map(u => `
        <tr>
            <td style="font-weight:700; color:var(--ink);">${esc(u.nama_lengkap)}</td>
            <td class="mono">${esc(u.username)}</td>
            <td>${esc(u.email)}</td>
            <td>${roleBadge(u.role)}</td>
            <td>${statusBadge(u.status)}</td>
            <td style="text-align:right;">
                <button class="icon-action" title="Edit" onclick="openEditModal(${u.id_user})"><i class="fa-solid fa-pencil"></i></button>
                ${canDelete(u) ? `<form method="POST" action="${BASE_URL}/admin/users/delete/${u.id_user}" style="display:inline;" onsubmit="return confirm('Hapus user ' + ${JSON.stringify(u.nama_lengkap)} + '?')"><button type="submit" class="icon-action danger" title="Hapus"><i class="fa-solid fa-trash"></i></button></form>` : ''}
            </td>
        </tr>
    `).join('');

    document.getElementById('cardList').innerHTML = DATA.map(u => `
        <div class="card" style="padding:14px; display:flex; align-items:center; gap:12px;">
            <div style="flex:1; min-width:0;" onclick="openEditModal(${u.id_user})">
                <div style="font-weight:700; color:var(--ink); font-size:13.5px;">${esc(u.nama_lengkap)}</div>
                <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">${esc(u.username)} · ${esc(u.email)}</div>
                <div style="margin-top:6px; display:flex; gap:6px;">${roleBadge(u.role)}${statusBadge(u.status)}</div>
            </div>
            <button class="icon-action" title="Edit" onclick="openEditModal(${u.id_user})"><i class="fa-solid fa-pencil"></i></button>
        </div>
    `).join('');
}

function resetForm() {
    document.getElementById('userForm').reset();
    document.getElementById('f_editing_id').value = '';
}

function openCreateModal() {
    resetForm();
    document.getElementById('userModalTitle').textContent = 'Tambah User';
    document.getElementById('userForm').action = BASE_URL + '/admin/users/store';
    document.getElementById('f_password').setAttribute('required', 'required');
    document.getElementById('f_password_confirm').setAttribute('required', 'required');
    document.getElementById('f_password_label').textContent = 'Password';
    document.getElementById('f_status_wrap').style.display = 'none';
    document.getElementById('f_status').removeAttribute('required');
    openModal('userModal');
}

function openEditModal(id) {
    const u = DATA.find(x => x.id_user == id);
    if (!u) return;
    resetForm();
    document.getElementById('f_editing_id').value = id;
    document.getElementById('userModalTitle').textContent = 'Edit — ' + u.nama_lengkap;
    document.getElementById('userForm').action = BASE_URL + '/admin/users/update/' + id;
    document.getElementById('f_nama').value = u.nama_lengkap;
    document.getElementById('f_username').value = u.username;
    document.getElementById('f_email').value = u.email;
    document.getElementById('f_role').value = u.role;

    document.getElementById('f_password').removeAttribute('required');
    document.getElementById('f_password_confirm').removeAttribute('required');
    document.getElementById('f_password_label').textContent = 'Password Baru (opsional)';
    document.getElementById('f_password').placeholder = 'Kosongkan kalau tidak diganti';

    document.getElementById('f_status_wrap').style.display = 'block';
    document.getElementById('f_status').setAttribute('required', 'required');
    document.getElementById('f_status').value = u.status;

    openModal('userModal');
}

function handleHash() {
    const h = location.hash;
    if (h === '#tambah') openCreateModal();
    else if (h.startsWith('#edit-')) openEditModal(parseInt(h.replace('#edit-', ''), 10));
}

render();
if (HAS_ERRORS) {
    if (OLD_EDITING_ID) {
        document.getElementById('userModalTitle').textContent = 'Edit User';
        document.getElementById('userForm').action = BASE_URL + '/admin/users/update/' + OLD_EDITING_ID;
        document.getElementById('f_editing_id').value = OLD_EDITING_ID;
        document.getElementById('f_password').removeAttribute('required');
        document.getElementById('f_password_confirm').removeAttribute('required');
        document.getElementById('f_password_label').textContent = 'Password Baru (opsional)';
        document.getElementById('f_status_wrap').style.display = 'block';
        document.getElementById('f_status').setAttribute('required', 'required');
    } else {
        document.getElementById('userForm').action = BASE_URL + '/admin/users/store';
        document.getElementById('f_password').setAttribute('required', 'required');
        document.getElementById('f_password_confirm').setAttribute('required', 'required');
    }
    openModal('userModal');
} else if (location.hash) {
    handleHash();
}
</script>

<?= $this->include('admin/layouts/footer') ?>
