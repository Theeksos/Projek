# Dough & Co Hub — Modul Login

Struktur:
```
doughco_hub/
├── login.php              # Halaman login (Presentation Layer)
├── proses_login.php       # Proses autentikasi & RBAC (Logic Layer)
├── logout.php             # Logout
├── database.sql           # Struktur tabel + data contoh
├── dashboard_owner.php    # Placeholder dashboard Owner
├── dashboard_mitra.php    # Placeholder dashboard Mitra
├── dashboard_staff.php    # Placeholder dashboard Staff
├── config/
│   └── database.php       # Koneksi ke MySQL (Data Layer)
└── assets/
    ├── css/style.css      # Styling tampilan login
    └── js/validasi.js     # Validasi form (JavaScript Native)
```

Akun contoh (setelah import database.sql):
- owner1 / admin123
- mitra1 / admin123
- staff1 / admin123
