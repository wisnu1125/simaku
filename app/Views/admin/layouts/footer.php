        </div><!-- /.content -->
    </div><!-- /.main-content -->

    <!-- ================= BOTTOM NAV (mobile) ================= -->
    <nav class="bottom-nav">
        <a href="<?= base_url('admin/dashboard') ?>" class="bottom-nav-item <?= simaku_active('admin/dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i> Beranda
        </a>
        <a href="<?= base_url('admin/siswa') ?>" class="bottom-nav-item <?= simaku_active('admin/siswa') ? 'active' : '' ?>">
            <i class="fa-solid fa-user-graduate"></i> Siswa
        </a>
        <a href="<?= base_url('admin/tagihan') ?>" class="bottom-nav-item <?= simaku_active('admin/tagihan') ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i> Tagihan
        </a>
        <a href="<?= base_url('admin/pembayaran') ?>" class="bottom-nav-item <?= simaku_active('admin/pembayaran') ? 'active' : '' ?>">
            <i class="fa-solid fa-wallet"></i> Bayar
        </a>
        <button class="bottom-nav-item" onclick="toggleMobileNav(true)">
            <i class="fa-solid fa-ellipsis"></i> Menu
        </button>
    </nav>
</div><!-- /.app-shell -->

<script>
/* =====================================================================
   SIMAKU — helper JS bersama (dipakai di semua halaman admin)
   ===================================================================== */

// ---------- Mobile nav sheet ----------
function toggleMobileNav(show) {
    document.getElementById('mobileNavSheet').classList.toggle('open', show);
    document.getElementById('mobileNavOverlay').classList.toggle('open', show);
    document.body.style.overflow = show ? 'hidden' : '';
}
// Sidebar (dipakai kalau ada tombol hamburger di layout lama / custom)
function toggleSidebar(show) {
    document.getElementById('sidebar')?.classList.toggle('open', show);
}

// ---------- Modal generik (Tambah/Edit) ----------
// Setiap modal butuh markup: <div class="overlay" id="X_overlay"></div><div class="modal" id="X">...</div>
function openModal(id) {
    document.getElementById(id)?.classList.add('open');
    document.getElementById(id + '_overlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id)?.classList.remove('open');
    document.getElementById(id + '_overlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

// ---------- Drawer generik (Detail) ----------
function openDrawer(id) {
    document.getElementById(id)?.classList.add('open');
    document.getElementById(id + '_overlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeDrawer(id) {
    document.getElementById(id)?.classList.remove('open');
    document.getElementById(id + '_overlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

// Tutup pakai tombol back/escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.open').forEach(function (m) { closeModal(m.id); });
        document.querySelectorAll('.drawer.open').forEach(function (d) { closeDrawer(d.id); });
        toggleMobileNav(false);
    }
});

// ---------- Toast: hilang otomatis ----------
setTimeout(function () {
    document.querySelectorAll('.toast').forEach(function (t) {
        t.style.transition = 'opacity .4s, transform .4s';
        t.style.opacity = '0';
        t.style.transform = 'translateY(-6px)';
        setTimeout(function () { t.remove(); }, 400);
    });
}, 5000);

// ---------- Konfirmasi hapus ----------
function confirmDelete(message) {
    return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
}

// ---------- Dropdown pencarian (cari siswa, dsb) ----------
// Sekarang cukup toggle display saja -- posisinya sudah ditangani CSS (position:absolute
// relatif ke .search-box), karena formnya tidak lagi di dalam modal yang overflow:auto.
function openSearchDropdown(inputEl, dropdownEl, html) {
    dropdownEl.innerHTML = html;
    dropdownEl.style.display = 'block';
}
function closeSearchDropdown(dropdownEl) {
    dropdownEl.style.display = 'none';
}

// ---------- Scroll halus ke bagian yang baru muncul (mis. setelah pilih siswa) ----------
function scrollIntoModal(el) {
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ---------- Panel inline (pengganti modal) ----------
function openPanel(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('open');
    setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
}
function closePanel(id) {
    document.getElementById(id)?.classList.remove('open');
}

// ---------- Toast notifikasi (dipanggil dari JS, memakai toast-stack yang sudah ada di layout) ----------
function showToast(message, type = 'success') {
    const stack = document.getElementById('toastStack');
    if (!stack) return;
    const validType = ['success', 'error', 'warning', 'info'].includes(type) ? type : (type === 'danger' ? 'error' : 'success');
    const iconMap = { success: 'circle-check', error: 'circle-exclamation', warning: 'triangle-exclamation', info: 'circle-info' };
    const el = document.createElement('div');
    el.className = 'toast ' + validType;
    el.innerHTML = `<i class="fa-solid fa-${iconMap[validType]} icon"></i><span>${message}</span><i class="fa-solid fa-xmark close" onclick="this.parentElement.remove()"></i>`;
    stack.appendChild(el);
    setTimeout(() => el.remove(), 5000);
}

// ---------- Format Rupiah ----------
function formatRupiah(angka, prefix = 'Rp ') {
    const number_string = angka.toString().replace(/[^,\d]/g, '');
    const split = number_string.split(',');
    const sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix + rupiah;
}

// ---------- Terbilang (dipakai di form pembayaran) ----------
function terbilang(angka) {
    angka = Math.floor(Math.abs(angka));
    var baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    var hasil = '';
    if (angka < 12) { hasil = ' ' + baca[angka]; }
    else if (angka < 20) { hasil = terbilang(angka - 10) + ' Belas'; }
    else if (angka < 100) { hasil = terbilang(Math.floor(angka / 10)) + ' Puluh' + terbilang(angka % 10); }
    else if (angka < 200) { hasil = ' Seratus' + terbilang(angka - 100); }
    else if (angka < 1000) { hasil = terbilang(Math.floor(angka / 100)) + ' Ratus' + terbilang(angka % 100); }
    else if (angka < 2000) { hasil = ' Seribu' + terbilang(angka - 1000); }
    else if (angka < 1000000) { hasil = terbilang(Math.floor(angka / 1000)) + ' Ribu' + terbilang(angka % 1000); }
    else if (angka < 1000000000) { hasil = terbilang(Math.floor(angka / 1000000)) + ' Juta' + terbilang(angka % 1000000); }
    return hasil;
}
</script>
</body>
</html>
