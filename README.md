# Futsal 71 ⚽

Sistem **booking lapangan futsal online** berbasis web untuk venue Futsal 71 di Tangerang. Pelanggan bisa lihat ketersediaan lapangan, booking, dan bayar online tanpa perlu telepon atau datang langsung.

## Fitur

- **Booking online** — Pilih lapangan, tanggal, dan jam (08:00–22:00 WIB), langsung checkout
- **Pembayaran online** — Terintegrasi iPaymu (sandbox)
- **Riwayat booking** — Lihat semua booking yang sudah pernah dibuat
- **Admin panel** — Dashboard, kelola booking/lapangan/user, ekspor CSV
- **Auth** — Login/register via email, session-based, CSRF protection
- **Dark theme** — Tampilan gelap khas F1, responsive (Bootstrap 5)

## Tech Stack

| Layer | Tech |
|---|---|
| Backend | PHP 8 (no framework) |
| Database | MySQL (PDO) |
| Frontend | Bootstrap 5.3, FontAwesome 6.4 |
| Payment | iPaymu (HMAC-SHA256) |
| Auth | Session + CSRF token |

## Cara Install

1. Clone ke `C:\xampp\htdocs\futsal71\` (atau docroot web server)
2. Import `database.sql` ke MySQL — bikin database `futsal71_db` + seed data
3. Edit `config.php` — sesuaikan `BASE_URL` (default: `/futsal71/`)
4. Edit `backend/config/database.php` — sesuaikan kredensial MySQL
5. Akses `http://localhost/futsal71/`

### Login Default

| Role | Email | Password |
|---|---|---|
| Admin | `admin@futsal71.com` | `admin123` |
| User | `john@example.com` | `user123` |

## Struktur

```
futsal71/
├── backend/          # PHP logic (config, functions, payment callback)
├── frontend/         # Halaman user (booking, checkout, history, dll)
│   ├── pages/        # Landing, booking, checkout, payment, history
│   └── includes/     # header.php (navbar), footer.php
├── admin/            # Panel admin (dashboard, bookings, fields, users)
├── assets/           # style.css, main.js
├── photo/            # Gambar (logo, foto lapangan)
├── config.php        # BASE_URL
└── database.sql      # Schema + seed
```

## Catatan

- iPaymu mode **sandbox** — tidak akan memproses pembayaran sungguhan
- Google OAuth **dinonaktifkan** untuk localhost
- Belum ada cron job — booking expired tidak otomatis dicancel
- Semua teks UI dalam **Bahasa Indonesia**
