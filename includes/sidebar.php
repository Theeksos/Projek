<?php

if (!isset($halaman_aktif)) {
    $halaman_aktif = "";
}

// 1. Ambil Role User dari Session (Default ke 'owner' jika kosong)
$role_user_session = strtolower($_SESSION['role'] ?? 'owner');

// 2. Definisi Seluruh Menu Sistem
$menu_master = [
    "dashboard" => ["Dashboard", "dashboard_owner.php", "bi-grid-1x2-fill"],
    "kios"      => ["Kios",      "kios.php", "bi-shop"],
    "produk"    => ["Produk",    "produk.php", "bi-box-seam"],
    "stok"      => ["Stok",      "stok.php", "bi-clipboard-data"],
    "staf"      => ["Staf",      "staff.php", "bi-people-fill"],
    "laporan"   => ["Laporan",   "laporan.php", "bi-bar-chart-fill"],
    "sop"       => ["SOP",       "sop.php", "bi-journal-text"],
    "mitra"     => ["Mitra",     "mitra.php", "bi-diagram-3-fill"],
];

// 3. LOGIKA FILTER MENU BERDASARKAN ROLE
$menu = [];

if ($role_user_session === 'owner' || $role_user_session === 'admin') {
    // Owner / Admin dapat melihat semua menu
    $menu = $menu_master;
} elseif ($role_user_session === 'mitra') {
    // Menu khusus untuk Mitra
    $menu = [
        "dashboard" => ["Dashboard", "dashboard_mitra.php", "bi-grid-1x2-fill"], // Mengarah ke dashboard khusus mitra
        "kios"      => ["Kios",      "kios.php", "bi-shop"],
        "produk"      => ["Produk",      "produk.php", "bi-clipboard-data"],
        "staf"      => ["Staf",      "staff.php", "bi-people-fill"],
        "laporan"   => ["Laporan",   "laporan.php", "bi-bar-chart-fill"],
        "sop"       => ["SOP",       "sop.php", "bi-journal-text"],
    ];
} elseif ($role_user_session === 'staff') {
    // Menu khusus untuk Staff / Karyawan Kios
    $menu = [
        "dashboard" => ["Dashboard", "dashboard_staff.php", "bi-grid-1x2-fill"], // Mengarah ke dashboard khusus staff
        "stok"      => ["Stok",      "stok.php", "bi-clipboard-data"],
        "sop"       => ["SOP",       "sop.php", "bi-journal-text"],
    ];
} else {
    // Fallback jika role tidak dikenali (hanya tampil dashboard dasar)
    $menu = [
        "dashboard" => ["Dashboard", "dashboard.php", "bi-grid-1x2-fill"],
    ];
}

// 4. Proses Inisial Nama & Role Teks
$nama_user = $_SESSION['nama'] ?? 'User';
$kata = explode(" ", $nama_user);
$inisial = strtoupper(substr($kata[0], 0, 1) . (isset($kata[1]) && !empty($kata[1]) ? substr($kata[1], 0, 1) : ""));
$role_display = ucfirst($role_user_session);
?>

<aside class="sidebar">
    <div>
        <div class="sidebar-brand">
            <span class="brand-icon"><i class="bi bi-egg-fried"></i></span>
            Dough & Co
        </div>
        <ul class="sidebar-nav">
            <?php foreach ($menu as $key => $item): ?>
                <li>
                    <a href="<?= $item[1] ?>" class="<?= $halaman_aktif === $key ? 'active' : '' ?>">
                        <i class="bi <?= $item[2] ?>"></i> <?= $item[0] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="sidebar-logout-container">
            <a href="logout.php" class="btn-sidebar-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="avatar"><?= htmlspecialchars($inisial) ?></div>
        <div class="user-info">
            <div><?= htmlspecialchars($nama_user) ?></div>
            <div class="role"><?= htmlspecialchars($role_display) ?> <i class="bi bi-chevron-down"></i></div>
        </div>
    </div>
</aside>