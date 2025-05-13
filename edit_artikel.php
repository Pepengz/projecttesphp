<?php
include 'koneksi.php';

$pesan = '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: blog.php');
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM artikel WHERE id = $id");
if (mysqli_num_rows($result) == 0) {
    header('Location: blog.php');
    exit;
}
$artikel = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $konten = mysqli_real_escape_string($conn, $_POST['konten']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $konten_url = mysqli_real_escape_string($conn, $_POST['konten_url']);
    $gambar_url = $artikel['gambar_url'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($ext), $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                if (!empty($artikel['gambar_url']) && strpos($artikel['gambar_url'], 'uploads/') === 0 && file_exists($artikel['gambar_url'])) {
                    unlink($artikel['gambar_url']);
                }
                $gambar_url = $upload_dir . $new_filename;
            } else {
                $pesan = '<div style="color:red">Gagal mengupload gambar!</div>';
            }
        } else {
            $pesan = '<div style="color:red">Format file tidak diizinkan! Gunakan JPG, JPEG, PNG, atau GIF.</div>';
        }
    } elseif (!empty($_POST['gambar_url'])) {
        $gambar_url = mysqli_real_escape_string($conn, $_POST['gambar_url']);
    }

    $query = "UPDATE artikel SET judul = '$judul', konten = '$konten', gambar_url = '$gambar_url', 
              konten_url = '$konten_url', kategori = '$kategori' WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        $pesan = '<div style="color:green;margin-bottom:20px;">Artikel berhasil diupdate!</div>';
        $result = mysqli_query($conn, "SELECT * FROM artikel WHERE id = $id");
        $artikel = mysqli_fetch_assoc($result);
        header("refresh:2;url=blog.php");
    } else {
        $pesan = '<div style="color:red;margin-bottom:20px;">Error: ' . mysqli_error($conn) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artikel - Portofolio Saya</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <style>
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 15px
        }
        .form-group {
            margin-bottom: 20px
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold
        }
        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px
        }
        textarea {
            height: 200px
        }
        button {
            background: #0e4496;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer
        }
        small {
            color: #777;
            display: block;
            margin-top: 3px
        }
        .current-image {
            margin-bottom: 15px;
            text-align: center
        }
        .current-image img {
            max-width: 300px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 5px
        }
        .tab-container {
            margin-bottom: 20px
        }
        .tab {
            display: inline-block;
            padding: 10px 20px;
            cursor: pointer;
            background: #eee;
            border-radius: 5px 5px 0 0
        }
        .tab.active {
            background: #0e4496;
            color: white
        }
        .tab-content {
            display: none;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 0 5px 5px 5px
        }
        .tab-content.active {
            display: block
        }
    </style>
</head>

<body>
    <div class="header-nav-container">
        <header>
            <h1>Edit Artikel</h1>
            <p>Edit artikel yang sudah ada</p>
        </header>

        <nav>
            <a href="index.html">Home</a>
            <a href="gallery.html">Gallery</a>
            <a href="blog.php">Blog</a>
            <a href="contact.html">Contact</a>
        </nav>
    </div>

    <hr class="header-divider">

    <div class="form-container">
        <?= $pesan ?>

        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judul">Judul Artikel</label>
                <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($artikel['judul']) ?>" required>
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Teknologi" <?= $artikel['kategori'] == 'Teknologi' ? 'selected' : '' ?>>Teknologi</option>
                    <option value="Keamanan" <?= $artikel['kategori'] == 'Keamanan' ? 'selected' : '' ?>>Keamanan</option>
                    <option value="Web Development" <?= $artikel['kategori'] == 'Web Development' ? 'selected' : '' ?>>Web Development</option>
                    <option value="Lainnya" <?= $artikel['kategori'] == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label>Gambar Artikel</label>

                <?php if (!empty($artikel['gambar_url'])): ?>
                    <div class="current-image">
                        <p>Gambar saat ini:</p>
                        <img src="<?= htmlspecialchars($artikel['gambar_url']) ?>" alt="Gambar Artikel">
                    </div>
                <?php endif; ?>

                <div class="tab-container">
                    <div class="tab active" onclick="switchTab('upload')">Upload Gambar Baru</div>
                    <div class="tab" onclick="switchTab('url')">URL Gambar</div>
                </div>

                <div id="upload-tab" class="tab-content active">
                    <input type="file" name="gambar" accept="image/*">
                    <small>Upload gambar dari komputer Anda (JPG, PNG, GIF max 2MB)</small>
                </div>

                <div id="url-tab" class="tab-content">
                    <input type="text" id="gambar_url" name="gambar_url" value="<?= htmlspecialchars($artikel['gambar_url']) ?>" placeholder="https://example.com/gambar.jpg">
                    <small>Masukkan URL gambar dari internet</small>
                </div>
            </div>

            <div class="form-group">
                <label for="konten_url">URL Konten/Artikel Lengkap (Opsional)</label>
                <input type="text" id="konten_url" name="konten_url" value="<?= htmlspecialchars($artikel['konten_url']) ?>" placeholder="https://example.com/artikel-lengkap">
                <small>Masukkan URL ke artikel lengkap atau sumber referensi</small>
            </div>

            <div class="form-group">
                <label for="konten">Konten Artikel</label>
                <textarea id="konten" name="konten" required><?= htmlspecialchars($artikel['konten']) ?></textarea>
            </div>

            <div style="text-align:center;">
                <a href="blog.php" style="display:inline-block; margin-right:10px; text-decoration:none; color:#777; padding:10px;">Batal</a>
                <button type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById(tabName + '-tab').classList.add('active');
            document.querySelectorAll('.tab').forEach(tab => {
                if (tab.textContent.toLowerCase().includes(tabName)) {
                    tab.classList.add('active');
                }
            });
        }
    </script>
</body>
</html>