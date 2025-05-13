<?php
include 'koneksi.php';

$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $konten = mysqli_real_escape_string($conn, $_POST['konten']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $konten_url = mysqli_real_escape_string($conn, $_POST['konten_url']);
    $gambar_url = '';

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
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

    $tanggal_publikasi = date('Y-m-d');
    if (!empty($konten_url)) {
        $tanggal_publikasi = ekstrakTanggalPublikasi($konten_url);
    }

    $query = "INSERT INTO artikel (judul, konten, gambar_url, konten_url, kategori, tanggal_dibuat) 
            VALUES ('$judul', '$konten', '$gambar_url', '$konten_url', '$kategori', '$tanggal_publikasi')";
    if (mysqli_query($conn, $query)) {
        $pesan = '<div style="color:green;margin-bottom:20px;">Artikel berhasil ditambahkan!</div>';
        header("refresh:2;url=blog.php");
    } else {
        $pesan = '<div style="color:red;margin-bottom:20px;">Error: ' . mysqli_error($conn) . '</div>';
    }
}

function ekstrakTanggalPublikasi($url)
{
    $html = @file_get_contents($url);
    if (!$html) return date('Y-m-d');
    $doc = new DOMDocument();
    @$doc->loadHTML($html);
    $metas = $doc->getElementsByTagName('meta');
    foreach ($metas as $meta) {
        if (
            $meta->getAttribute('property') == 'article:published_time' ||
            $meta->getAttribute('name') == 'pubdate' ||
            $meta->getAttribute('name') == 'publishdate' ||
            $meta->getAttribute('itemprop') == 'datePublished'
        ) {
            $tanggal = $meta->getAttribute('content');
            if ($tanggal) return date('Y-m-d', strtotime($tanggal));
        }
    }
    return date('Y-m-d');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tambah Artikel - Portofolio Saya</title>
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
            border: 2px solid transparent;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        button:hover {
            background: #1a56b3;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        button:active {
            background: #0a3270 !important;
            transform: translateY(0) !important;
            border-color: white !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
        }

        small {
            color: #777;
            display: block;
            margin-top: 3px
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

        /* Garis putih pada navigasi */
        nav a {
            position: relative;
            padding-bottom: 5px;
        }

        nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: white;
            transition: width 0.3s ease, left 0.3s ease;
        }

        nav a:hover::after {
            width: 100%;
            left: 0;
        }
    </style>
</head>

<body>
    <div class="header-nav-container">
        <header>
            <h1>Tambah Artikel</h1>
            <p>Tambahkan artikel baru ke blog</p>
        </header>
        <nav><a href="index.html">Home</a><a href="gallery.html">Gallery</a><a href="blog.php">Blog</a><a href="contact.html">Contact</a></nav>
    </div>
    <hr class="header-divider">
    <div class="form-container"><?= $pesan ?>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group"><label for="judul">Judul Artikel</label><input type="text" id="judul" name="judul" required></div>
            <div class="form-group"><label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Teknologi">Teknologi</option>
                    <option value="Keamanan">Keamanan</option>
                    <option value="Web Development">Web Development</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="form-group"><label>Gambar Artikel</label>
                <div class="tab-container">
                    <div class="tab active" onclick="switchTab('upload')">Upload Gambar</div>
                    <div class="tab" onclick="switchTab('url')">URL Gambar</div>
                </div>
                <div id="upload-tab" class="tab-content active">
                    <input type="file" name="gambar" accept="image/*">
                    <small>Upload gambar dari komputer Anda (JPG, PNG, GIF max 2MB)</small>
                </div>
                <div id="url-tab" class="tab-content">
                    <input type="text" id="gambar_url" name="gambar_url" placeholder="https://example.com/gambar.jpg">
                    <small>Masukkan URL gambar dari internet</small>
                </div>
            </div>
            <div class="form-group"><label for="konten_url">URL Konten/Artikel Lengkap (Opsional)</label>
                <input type="text" id="konten_url" name="konten_url" placeholder="https://example.com/artikel-lengkap">
                <small>Masukkan URL ke artikel lengkap atau sumber referensi</small>
            </div>
            <div class="form-group"><label for="konten">Konten Artikel</label><textarea id="konten" name="konten" required></textarea></div>
            <div style="text-align:center">
                <a href="blog.php" style="display:inline-block;margin-right:10px;text-decoration:none;color:#777;padding:10px">Batal</a>
                <button type="submit">Simpan Artikel</button>
            </div>
        </form>
    </div>
    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabName + '-tab').classList.add('active');
            document.querySelectorAll('.tab').forEach(tab => {
                if (tab.textContent.toLowerCase().includes(tabName)) tab.classList.add('active')
            })
        }
    </script>
</body>

</html>