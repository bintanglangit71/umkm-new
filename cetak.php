<?php
session_start();
include 'koneksi.php';

$id_user = $_SESSION['id'] ?? ($_SESSION['id_user'] ?? null);

if (empty($id_user)) {
    header("Location: login/login.php?pesan=harus_login");
    exit;
}

$id_transaksi = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT t.*, u.nama AS nama_pelanggan, u.alamat, u.hp, u.email
                                   FROM tb_transaksi t
                                   JOIN tb_user u ON u.id = t.id_pelanggan
                                   WHERE t.id_transaksi = ?");
mysqli_stmt_bind_param($stmt, "i", $id_transaksi);
mysqli_stmt_execute($stmt);
$transaksi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if (!$transaksi || (!$is_admin && (int) $transaksi['id_pelanggan'] !== (int) $id_user)) {
    header("Location: carousell.php");
    exit;
}

$stmt2 = mysqli_prepare($koneksi, "SELECT d.jumlah, p.nama, p.harga, p.poto
                                    FROM tb_detail d
                                    JOIN tb_produk p ON p.id = d.id_produk
                                    WHERE d.id_transaksi = ?");
mysqli_stmt_bind_param($stmt2, "i", $id_transaksi);
mysqli_stmt_execute($stmt2);
$items = mysqli_stmt_get_result($stmt2);

$total = 0;
$rows = [];
while ($row = mysqli_fetch_assoc($items)) {
    $subtotal = $row['jumlah'] * $row['harga'];
    $total += $subtotal;
    $rows[] = $row;
}

$no_struk = 'TRX-' . str_pad($transaksi['id_transaksi'], 5, '0', STR_PAD_LEFT);
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#0f3d2a">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk <?= htmlspecialchars($no_struk) ?> - Teh Idaman</title>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="theme-hijau.css" rel="stylesheet" />
    <style>
        .invoice-card {
            max-width: 720px;
            margin: 2rem auto;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background-color: #fff;
            overflow: hidden;
        }
        .invoice-head {
            background: linear-gradient(135deg, #0f3d2a 0%, #198754 100%);
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        .product-thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 12px;
        }
        @media print {
            .no-print, header, footer { display: none !important; }
            html, body { background: #fff !important; }
            .invoice-card { box-shadow: none !important; margin: 0 !important; max-width: 100% !important; }
        }
      :root {
        --hijau-tua: #0f3d2a;
        --hijau-utama: #198754;
        --hijau-hover: #146c43;
        --hijau-aktif: #115c3a;
      }
      .bg-hijau-tua {
        background-color: var(--hijau-tua) !important;
      }

      .bi {
        vertical-align: -0.125em;
        fill: currentColor;
      }
      .btn-bd-primary {
        --bd-violet-bg: var(--hijau-utama);
        --bd-violet-rgb: 25, 135, 84;
        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--hijau-utama);
        --bs-btn-border-color: var(--hijau-utama);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: var(--hijau-hover);
        --bs-btn-hover-border-color: var(--hijau-hover);
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: var(--hijau-aktif);
        --bs-btn-active-border-color: var(--hijau-aktif);
      }
      .bd-mode-toggle {
        z-index: 1500;
      }
      .bd-mode-toggle .bi {
        width: 1em;
        height: 1em;
      }
      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }

      /* ===== Layout: footer selalu menempel di bawah, tanpa area putih ===== */
      html {
        background-color: var(--hijau-tua);
      }
      body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: var(--bs-body-bg);
      }
      body > main {
        flex: 1 0 auto;
      }
      body > footer {
        margin-top: auto;
      }

      /* ===== Footer: teks & ikon terbaca di latar hijau tua ===== */
      .footer-hijau {
        color: #ffffff;
      }
      .footer-hijau h5 {
        color: #ffffff;
      }
      .footer-hijau .text-body-secondary {
        color: rgba(255, 255, 255, 0.78) !important;
      }
      .footer-hijau a.text-body-secondary:hover {
        color: #75d9a3 !important;
      }
      .footer-icon-wa {
        color: #75d9a3;
      }
      .footer-icon-pusat {
        color: #ff9b9b;
      }

      /* ===== Navbar ===== */
      .navbar-hijau {
        background: linear-gradient(90deg, #0a2a1c 0%, #0f3d2a 55%, #146c43 100%);
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding-top: 0.6rem;
        padding-bottom: 0.6rem;
      }
      .navbar-hijau .navbar-brand {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 700;
        font-size: 1.3rem;
        letter-spacing: 0.5px;
        color: #ffffff;
      }
      .navbar-hijau .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.82);
        font-weight: 500;
        border-radius: 999px;
        padding: 0.45rem 1.1rem;
        transition: background-color 0.2s ease, color 0.2s ease;
      }
      .navbar-hijau .navbar-nav .nav-link:hover,
      .navbar-hijau .navbar-nav .nav-link:focus {
        color: #ffffff;
        background-color: rgba(255, 255, 255, 0.12);
      }
      .navbar-hijau .navbar-nav .nav-link.active {
        color: #ffffff;
        background-color: var(--hijau-utama);
      }
      .navbar-hijau .navbar-nav .nav-link.nav-cart {
        width: 42px;
        height: 42px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.12);
      }
      .navbar-hijau .navbar-nav .nav-link.nav-cart:hover {
        background-color: var(--hijau-utama);
      }
      .navbar-hijau .navbar-nav .nav-link.nav-profile {
        padding: 0.3rem 1rem 0.3rem 0.4rem;
        border: 1px solid rgba(255, 255, 255, 0.25);
      }
      .navbar-hijau .navbar-nav .nav-link.nav-profile:hover,
      .navbar-hijau .navbar-nav .nav-link.nav-profile.show {
        background-color: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.45);
      }
      .navbar-hijau .dropdown-menu {
        border: 0;
        border-radius: 14px;
        padding: 0.5rem;
        margin-top: 0.6rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
      }
      .navbar-hijau .dropdown-item {
        border-radius: 8px;
        padding: 0.5rem 0.9rem;
        font-weight: 500;
      }
      .navbar-hijau .dropdown-item:hover {
        background-color: rgba(25, 135, 84, 0.25);
      }
      .navbar-hijau .dropdown-item.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.18);
      }
      .navbar-hijau .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.35);
      }
      .navbar-hijau .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.45);
      }
      @media (max-width: 767.98px) {
        .navbar-hijau .navbar-nav {
          padding: 0.5rem 0;
        }
      }

      .form-control:focus, .form-select:focus, .form-check-input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
      }
      .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
      }
        </style>
</head>
<body class="bg-light">

    <header class="no-print" data-bs-theme="dark">
        <div class="navbar navbar-dark navbar-hijau shadow-sm">
            <div class="container">
                <a href="carousell.php" class="navbar-brand d-flex align-items-center gap-2">
                    <span>Teh Idaman</span>
                </a>
                <a href="carousell.php" class="btn btn-outline-light btn-sm rounded-pill px-3">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </header>

    <main class="py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mx-auto mb-3 no-print" style="max-width: 720px;">
                <button type="button" class="btn btn-success" onclick="window.print()">
                    🖨️ Cetak / Save PDF
                </button>
            </div>

            <div class="card invoice-card border-0">
                <div class="invoice-head">
                    <h2 class="fw-bold mb-1">TEH IDAMAN</h2>
                    <p class="mb-2 opacity-75">Cita Rasa Teh Otentik Indonesia</p>
                    <span class="badge bg-light text-success fw-bold px-3 py-2">Pesanan Berhasil</span>
                </div>

                <div class="card-body p-4">
                    <div class="row mb-4 pb-3 border-bottom">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">No. Transaksi</h6>
                            <p class="fw-bold mb-2"><?= htmlspecialchars($no_struk) ?></p>
                            <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Tanggal</h6>
                            <p class="mb-0"><?= date('d F Y', strtotime($transaksi['tanggal'])) ?></p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Pelanggan</h6>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($transaksi['nama_pelanggan'] ?? '-') ?></p>
                            <p class="text-muted small mb-0"><?= htmlspecialchars($transaksi['email'] ?? '-') ?></p>
                            <p class="text-muted small mb-0"><?= htmlspecialchars($transaksi['hp'] ?? '-') ?></p>
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th class="text-start">Produk</th>
                                    <th style="width: 80px;">Qty</th>
                                    <th style="width: 120px;">Harga</th>
                                    <th style="width: 130px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="images/<?= htmlspecialchars($r['poto']) ?>" class="product-thumb shadow-sm" alt="<?= htmlspecialchars($r['nama']) ?>">
                                            <span class="fw-semibold"><?= htmlspecialchars($r['nama']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= (int) $r['jumlah'] ?></td>
                                    <td class="text-center">Rp <?= number_format($r['harga'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold text-success">Rp <?= number_format($r['jumlah'] * $r['harga'], 0, ',', '.') ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="d-flex justify-content-between fs-5 fw-bold text-success">
                            <span>Total Bayar:</span>
                            <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div class="text-center text-muted small border-top pt-3">
                        <p class="mb-0">Terima kasih telah berbelanja di Teh Idaman!</p>
                        <p class="mb-0">Simpan bukti transaksi ini sebagai bukti pembayaran yang sah.</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

      <div class="container mt-auto pb-4 no-print">
      <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center pt-3 small text-body-secondary text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> Teh Idaman Indonesia. All rights reserved.</p>
      </div>
    </div>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>