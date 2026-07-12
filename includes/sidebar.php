<?php

if (!isset($halaman_aktif)) {
    $halaman_aktif = "";
}

$menu = [
    "dashboard" => ["Dashboard", "dashboard_owner.php", "bi-grid-1x2-fill"],
    "kios"      => ["Kios",      "kios.php", "bi-shop"],
    "produk"    => ["Produk",    "produk.php", "bi-box-seam"],
    "stok"      => ["Stok",      "stok.php", "bi-clipboard-data"],
    "staf"      => ["Staf",      "staff.php", "bi-people-fill"],
    "laporan"   => ["Laporan",   "laporan.php", "bi-bar-chart-fill"],
    "sop"       => ["SOP",       "sop.php", "bi-journal-text"],
    "mitra"     => ["Mitra",     "mitra.php", "bi-diagram-3-fill"],
];

$nama_user = $_SESSION['nama'] ?? 'User';
$kata = explode(" ", $nama_user);
$inisial = strtoupper(substr($kata[0], 0, 1) . (isset($kata[1]) ? substr($kata[1], 0, 1) : ""));
$role_user = ucfirst($_SESSION['role'] ?? 'owner');
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
    </div>

    <div class="sidebar-user">
        <div class="avatar"><?= htmlspecialchars($inisial) ?></div>
        <div class="user-info">
            <div><?= htmlspecialchars($nama_user) ?></div>
            <div class="role"><?= htmlspecialchars($role_user) ?> <i class="bi bi-chevron-down"></i></div>
        </div>
    </div>
</aside>
