<?php
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "db_toko");

// Cek apakah session id ada (User sudah login)
$id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// PROSES TAMBAH KE KERANJANG
if (isset($_POST["add"])) {
    $id_produk = $_POST["id_produk"];
    $jumlah = (int) $_POST["jumlah"];

    if (isset($_SESSION["cart"][$id_produk])) {
        $_SESSION["cart"][$id_produk]['jumlah'] += $jumlah;
    } else {
        $_SESSION["cart"][$id_produk] = array(
            'id' => $id_produk,
            'nama' => $_POST["hidden_nama"],
            'harga' => $_POST["hidden_harga"],
            'foto' => $_POST["hidden_foto"],
            'jumlah' => $jumlah
        );
    }

    echo '<script>window.location="keranjang.php"</script>';
    exit;
}

// PROSES AKSI (HAPUS & BELI)
if (isset($_GET["aksi"])) {
    if ($_GET["aksi"] == "hapus") {
        $id_produk = $_GET["id"];
        if (isset($_SESSION["cart"][$id_produk])) {
            unset($_SESSION["cart"][$id_produk]);
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: "Produk berhasil dihapus.",
                        confirmButtonColor: "#198754"
                    }).then(() => {
                        window.location="keranjang.php";
                    });
                });
            </script>';
        }
    } elseif ($_GET["aksi"] == "beli") {
        if (empty($_SESSION["cart"])) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "warning",
                        title: "Keranjang Kosong",
                        text: "Keranjang Anda masih kosong!",
                        confirmButtonColor: "#198754"
                    }).then(() => {
                        window.location="carousell.php";
                    });
                });
            </script>';
        } elseif (!$id) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "info",
                        title: "Perlu Login",
                        text: "Silakan login terlebih dahulu untuk melakukan checkout.",
                        confirmButtonColor: "#198754"
                    }).then(() => {
                        window.location="login/login.php";
                    });
                });
            </script>';
        } else {
            $total = 0;
            foreach ($_SESSION["cart"] as $value) {
                $total += ($value["jumlah"] * $value["harga"]);
            }

            // Simpan transaksi utama
            $query = mysqli_query(
                $koneksi,
                "INSERT INTO tb_transaksi(tanggal, id_pelanggan, total_harga) 
                 VALUES ('" . date("Y-m-d") . "', '$id', '$total')"
            ) or die("Error insert transaksi: " . mysqli_error($koneksi));
            
            $id_transaksi = mysqli_insert_id($koneksi);

            // Simpan detail produk
            foreach ($_SESSION["cart"] as $value) {
                $id_produk = $value['id'];
                $jumlah = $value['jumlah'];
                $sql = "INSERT INTO tb_detail(id_transaksi, id_produk, jumlah) 
                        VALUES ('$id_transaksi', '$id_produk', '$jumlah')";
                mysqli_query($koneksi, $sql);
            }

            // Kosongkan keranjang setelah transaksi
            unset($_SESSION["cart"]);
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pesanan berhasil dibuat!',
                        confirmButtonColor: '#198754'
                    }).then(() => {
                        window.location='cetak.php?id=" . $id_transaksi . "';
                    });
                });
            </script>";
        }
    }
}
?>

<!doctype html>
<html lang="id" data-bs-theme="auto">

<head>
    <script src="assets/js/color-modes.js"></script>
    <meta charset="utf-8">
    <meta name="theme-color" content="#0f3d2a">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Belanja - Teh Idaman</title>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="theme-hijau.css" rel="stylesheet" />
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .cart-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }
        .product-img-thumb {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
        }
        .cart-card .table-dark {
            --bs-table-bg: var(--hijau-tua);
            --bs-table-border-color: #1d5a40;
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
    
      /* ===== Footer: ukuran ikon & link ===== */
      footer a {
        text-decoration: none;
        transition: color 0.2s ease-in-out;
      }
      .footer-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        fill: currentColor;
      }
    </style>
</head>

<body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path
          d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"
        ></path>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path
          d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"
        ></path>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path
          d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"
        ></path>
        <path
          d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"
        ></path>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path
          d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"
        ></path>
      </symbol>
    </svg>
    <div
      class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle"
    >
      <button
        class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
        id="bd-theme"
        type="button"
        aria-expanded="false"
        data-bs-toggle="dropdown"
        aria-label="Toggle theme (auto)"
      >
        <svg class="bi my-1 theme-icon-active" aria-hidden="true">
          <use href="#circle-half"></use>
        </svg>
        <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
      </button>
      <ul
        class="dropdown-menu dropdown-menu-end shadow"
        aria-labelledby="bd-theme-text"
      >
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center"
            data-bs-theme-value="light"
            aria-pressed="false"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#sun-fill"></use>
            </svg>
            Light
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center"
            data-bs-theme-value="dark"
            aria-pressed="false"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#moon-stars-fill"></use>
            </svg>
            Dark
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center active"
            data-bs-theme-value="auto"
            aria-pressed="true"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#circle-half"></use>
            </svg>
            Auto
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
      </ul>
    </div>
    <!-- Header / Navbar -->
    <header data-bs-theme="dark">
        <div class="navbar navbar-dark navbar-hijau shadow-sm">
            <div class="container">
                <a href="carousell.php" class="navbar-brand d-flex align-items-center gap-2">
                    <span>Teh Idaman</span>
                </a>
                <a href="carousell.php" class="btn btn-outline-light btn-sm rounded-pill px-3">&larr; Lanjut Belanja</a>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card cart-card bg-body-tertiary p-4">
                        <h3 class="fw-bold mb-4 text-center">🛒 Keranjang Belanja Anda</h3>

                        <?php if (!empty($_SESSION["cart"])) { ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-dark">
                                        <tr class="text-center">
                                            <th scope="col" class="text-start">Produk</th>
                                            <th scope="col" style="width: 120px;">Harga</th>
                                            <th scope="col" style="width: 100px;">Jumlah</th>
                                            <th scope="col" style="width: 150px;">Total</th>
                                            <th scope="col" style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total = 0;
                                        foreach ($_SESSION["cart"] as $key => $value) {
                                            $subtotal = $value["jumlah"] * $value["harga"];
                                            $total += $subtotal;
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="images/<?= htmlspecialchars($value["foto"]) ?>" class="product-img-thumb shadow-sm" alt="<?= htmlspecialchars($value["nama"]) ?>">
                                                        <span class="fw-semibold fs-6"><?= htmlspecialchars($value["nama"]) ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-center">Rp <?= number_format($value["harga"], 0, ',', '.') ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-success px-3 py-2 fs-6"><?= $value["jumlah"] ?></span>
                                                </td>
                                                <td class="text-center fw-bold text-success">
                                                    Rp <?= number_format($subtotal, 0, ',', '.') ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" onclick="confirmHapus('<?= $value['id']; ?>'); return false;" class="btn btn-outline-danger btn-sm">
                                                         Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Ringkasan Grand Total -->
                            <div class="row align-items-center mt-4 pt-3 border-top">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <a href="carousell.php" class="btn btn-outline-success">
                                        &larr; Tambah Menu Lain
                                    </a>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="mb-3">
                                        <span class="fs-5 text-body-secondary me-2">Grand Total:</span>
                                        <span class="fs-3 fw-bold text-success">Rp <?= number_format($total, 0, ',', '.') ?></span>
                                    </div>
                                    <a href="keranjang.php?aksi=beli" class="btn btn-success btn-lg px-5 py-2 fw-semibold shadow-sm">
                                        Checkout &amp; Beli Sekarang &rarr;
                                    </a>
                                </div>
                            </div>

                        <?php } else { ?>
                            <!-- Tampilan Saat Keranjang Kosong -->
                            <div class="text-center py-5">
                                <h4 class="fw-bold">Keranjang Belanja Anda Kosong</h4>
                                <p class="text-body-secondary mb-4">Yuk, pilih minuman kesukaanmu dan tambahkan ke keranjang!</p>
                                <a href="carousell.php" class="btn btn-success btn-lg px-4 fw-semibold">
                                    Lihat Menu Teh Idaman
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center pt-3 small text-body-secondary text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> Teh Idaman Indonesia. All rights reserved.</p>
    </div>
    </main>


    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmHapus(id) {
        Swal.fire({
            title: 'Yakin menghapus?',
            text: 'Produk akan dihapus dari keranjang belanja Anda.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#198754',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'keranjang.php?aksi=hapus&id=' + id;
            }
        });
    }
    </script>
</body>

</html>