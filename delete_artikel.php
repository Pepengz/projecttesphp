<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: blog.php');
    exit;
}

$result = mysqli_query($conn, "SELECT gambar_url FROM artikel WHERE id = $id");
if (mysqli_num_rows($result) == 0) {
    header('Location: blog.php');
    exit;
}
$artikel = mysqli_fetch_assoc($result);

if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    if (!empty($artikel['gambar_url']) && strpos($artikel['gambar_url'], 'uploads/') === 0 && file_exists($artikel['gambar_url'])) {
        unlink($artikel['gambar_url']);
    }

    mysqli_query($conn, "DELETE FROM komentar WHERE artikel_id = $id");
    mysqli_query($conn, "DELETE FROM artikel WHERE id = $id");

    header('Location: blog.php');
    exit;
}

$result = mysqli_query($conn, "SELECT judul FROM artikel WHERE id = $id");
$artikel = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Artikel - Portofolio Saya</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <style>
        .confirm-container {
            background: rgba(255, 255, 255, .95);
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            margin: 30px auto;
            text-align: center
        }

        .btn-container {
            margin-top: 30px
        }

        .btn {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold
        }

        .btn-delete {
            background: #dc3545;
            color: white
        }

        .btn-cancel {
            background: #6c757d;
            color: white
        }
    </style>
</head>

<body>
    <div class="header-nav-container">
        <header>
            <h1>Hapus Artikel</h1>
            <p>Konfirmasi penghapusan artikel</p>
        </header>

        <nav>
            <a href="index.html">Home</a>
            <a href="gallery.html">Gallery</a>
            <a href="blog.php">Blog</a>
            <a href="contact.html">Contact</a>
        </nav>
    </div>

    <hr class="header-divider">

    <div class="confirm-container">
        <h2>Konfirmasi Penghapusan</h2>

        <?php if ($artikel): ?>
            <p>Anda yakin ingin menghapus artikel:<br><strong><?= htmlspecialchars($artikel['judul']) ?></strong>?</p>
            <p>Semua komentar terkait artikel ini juga akan dihapus.</p>
            <p>Tindakan ini tidak dapat dibatalkan.</p>
        <?php else: ?>
            <p>Artikel tidak ditemukan.</p>
        <?php endif; ?>

        <div class="btn-container">
            <a href="blog.php" class="btn btn-cancel">Batal</a>
            <a href="delete_artikel.php?id=<?= $id ?>&confirm=yes" class="btn btn-delete">Hapus</a>
        </div>
    </div>
</body>

</html>