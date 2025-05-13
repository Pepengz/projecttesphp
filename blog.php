<?php
include 'koneksi.php';

session_start();
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$articles = mysqli_query($conn, "SELECT * FROM artikel ORDER BY tanggal_dibuat DESC LIMIT $start, $limit");
$total_articles = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM artikel"));
$total_pages = ceil($total_articles / $limit);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $article_id = $_POST['article_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    if (strpos($article_id, 'static') !== false) {
        if (!isset($_SESSION['static_comments'])) $_SESSION['static_comments'] = [];
        $_SESSION['static_comments'][] = ['article_id' => $article_id, 'name' => $name, 'comment' => $comment, 'time' => date('Y-m-d H:i:s')];
        header("Location: blog.php?page=$page#$article_id");
        exit;
    } else {
        mysqli_query($conn, "INSERT INTO komentar (artikel_id, nama, isi_komentar, tanggal_dibuat) VALUES (" . intval($article_id) . ", '$name', '$comment', NOW())");
        header("Location: blog.php?page=$page#article$article_id");
        exit;
    }
}

$static_titles = ["Prediksi Tren Perkembangan AI Tahun 2025", "Keamanan Siber di Indonesia", "Era Teknologi Kuantum 2.0: Potensi dan Tantangan Global"];
$static_imgs = ["https://desaplembutan.gunungkidulkab.go.id/assets/files/artikel/sedang_1676695344What-is-M.Tech-in-Artificial-Intelligence_AI.jpg", "https://images.theconversation.com/files/624182/original/file-20241008-17-zoa0vc.jpg?ixlib=rb-4.1.0&q=45&auto=format&w=668&h=324&fit=crop", "https://b.acaraseru.com/images/2024/11/11//binary-code-3.jpg"];
$static_dates = ["5 Maret 2025", "26 Juni 2024", "10 Januari 2025"];
$static_categs = ["Teknologi", "Keamanan", "Teknologi"];
$static_content = [
    "Artificial Intelligence atau AI telah menjadi penggerak utama dalam revolusi teknologi global, memberikan dampak signifikan di berbagai sektor seperti kesehatan, pendidikan, dan bisnis. Teknologi AI terus berkembang pesat, menghadirkan solusi yang tidak hanya mengotomatisasi tugas rutin tetapi juga membuka peluang baru dalam analisis data dan inovasi.",
    "Serangan siber yang menargetkan data beberapa instansi pemerintah dalam waktu berdekatan marak dilaporkan. Disarikan dari beberapa pemberitaan Kompas.com, serangan siber dilaporkan menyasar Pusat Data Nasional (PDN) Sementara, Indonesia Automatic Fingerprint Identification System (Inafis), Badan Intelijen Strategi Indonesia (Bais), dan Kementerian Perhubungan.
\n\nPDN Sementara diserang ransomware hingga membuat 210 kinerja instansi pemerintah terganggu, sejak Kamis (20/6/2024) lalu. Selain itu, data lama Inafis milik Polri, berupa identitas sidik jari dan email, dilaporkan dijual dengan harga 1.000 dollar AS atau Rp 16.500.000 di dark web. 
Sementara, Bais TNI yang menyimpan data dokumen intelijen file terkompres tunggal tahun 2020-2022 dijual dengan harga 7.000 dollar AS atau Rp 115.500.000.",
    "Dalam dua dekade terakhir, teknologi kuantum generasi baru yang dikenal sebagai \"Teknologi Kuantum 2.0\" telah menunjukkan perkembangan yang pesat dan signifikan. 
Teknologi ini bukan hanya bertumpu pada dasar teori fisika kuantum, tetapi juga membawa berbagai aplikasi praktis yang diharapkan mampu mengubah wajah dunia teknologi informasi, industri, kesehatan, hingga keamanan global. Dengan tiga pilar utamanya yaitu komputasi, komunikasi, dan penginderaan kuantum, teknologi ini menawarkan potensi revolusioner yang diyakini akan membawa dampak besar di berbagai sektor.
\n\nTeknologi kuantum 2.0 memanfaatkan prinsip-prinsip dasar fisika kuantum suatu bidang ilmu yang meneliti perilaku partikel-partikel pada skala atom dan sub-atom. Teknologi ini didasari oleh tiga konsep utama: superposisi, entanglement, dan pengukuran kuantum. 
Setiap prinsip ini membuka peluang untuk menghadirkan terobosan baru dalam teknologi, menjadikannya sangat berbeda dan lebih canggih dibandingkan teknologi klasik yang ada saat ini."
];
$static_links = ["https://eduparx.id/blog/insight/artificial-intelligence/prediksi-tren-perkembangan-ai-tahun-2025/", "https://www.kompas.com/tren/read/2024/06/26/120000865/menilik-peringkat-keamanan-siber-indonesia-usai-pdn-inafis-bais-kemenhub?page=all", "https://www.cloudcomputing.id/pengetahuan-dasar/teknologi-kuantum-2-0"];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Blog - Portofolio Saya</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <style>
        .comment-button {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
            display: inline-block;
            text-decoration: none
        }
        .comment-button:hover {
            background: #357ae8
        }
        .comment-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
            display: none
        }
        .comment-form.active {
            display: block;
            animation: slideDown .3s ease
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            margin: 30px auto;
            max-width: 800px
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }
        .add-article-btn {
            background: #0e4496;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.25s ease-in-out !important;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(14, 68, 150, 0.3);
            position: relative;
            overflow: hidden;
            border: none;
            outline: none;
        }
        .add-article-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0);
            transition: all 0.3s ease;
        }
        .add-article-btn:hover {
            background: #0a3370;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(14, 68, 150, 0.4);
        }
        .add-article-btn:hover::after {
            background: rgba(255, 255, 255, 0.1);
        }
        .add-article-btn:active {
            transform: translateY(2px) scale(0.98);
            box-shadow: 0 2px 6px rgba(14, 68, 150, 0.3);
            background: #072a5e;
        }
        .add-article-btn:active::after {
            background: rgba(0, 0, 0, 0.1);
        }
        .action-buttons a.add-article-btn:hover,
        .action-buttons a.add-article-btn:active,
        .action-buttons a.add-article-btn:focus {
            color: #fff !important;
            text-decoration: none !important;
        }
    </style>
</head>

<body>
    <div class="header-nav-container">
        <header>
            <h1>Blog</h1>
            <p>Artikel artikelnya, silakan dibaca!</p>
        </header>
        <nav><a href="index.html">Home</a><a href="gallery.html">Gallery</a><a href="blog.php">Blog</a><a href="contact.html">Contact</a></nav>
    </div>
    <hr class="header-divider">
    <div class="blog-container">
        <?php if (mysqli_num_rows($articles) > 0): while ($row = mysqli_fetch_assoc($articles)): ?>
                <article id="article<?= $row['id'] ?>">
                    <?php if ($row['gambar_url']): ?><img src="<?= htmlspecialchars($row['gambar_url']) ?>" alt="Gambar Artikel" width="550"><?php endif; ?>
                    <h2><?= htmlspecialchars($row['judul']) ?></h2>
                    <p><em>Ditulis pada <?= date('d M Y', strtotime($row['tanggal_dibuat'])) ?> | Kategori: <?= htmlspecialchars($row['kategori']) ?></em></p>
                    <p><?= nl2br(htmlspecialchars($row['konten'])) ?></p>
                    <?php if (!empty($row['konten_url'])): ?><p>Untuk informasi lengkapnya, kunjungi <a href="<?= htmlspecialchars($row['konten_url']) ?>" target="_blank">link ini</a>.</p><?php endif; ?>
                    <div style="margin-top:20px;text-align:left"><strong>Komentar:</strong>
                        <?php $comments = mysqli_query($conn, "SELECT * FROM komentar WHERE artikel_id={$row['id']} ORDER BY tanggal_dibuat DESC");
                        if (mysqli_num_rows($comments) > 0): while ($c = mysqli_fetch_assoc($comments)): ?>
                                <div style="margin-bottom:8px"><b><?= htmlspecialchars($c['nama']) ?>:</b> <?= htmlspecialchars($c['isi_komentar']) ?>
                                    <span style="color:#888;font-size:12px">(<?= date('d-m-Y H:i', strtotime($c['tanggal_dibuat'])) ?>)</span>
                                </div>
                            <?php endwhile;
                        else: ?>
                            <p style="color:#777;font-style:italic">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top:10px">
                        <a href="edit_artikel.php?id=<?= $row['id'] ?>">Edit</a> |
                        <a href="delete_artikel.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus artikel ini?')">Hapus</a> |
                        <a href="javascript:void(0)" class="comment-button" onclick="toggleCommentForm('dynamic<?= $row['id'] ?>')">Beri Komentar</a>
                    </div>
                    <div class="comment-form" id="comment-form-dynamic<?= $row['id'] ?>">
                        <h3>Beri Komentar</h3>
                        <form method="post">
                            <input type="hidden" name="article_id" value="<?= $row['id'] ?>">
                            <div style="margin-bottom:15px"><label for="name-dynamic<?= $row['id'] ?>" style="display:block;margin-bottom:5px;font-weight:bold">Nama:</label>
                                <input type="text" id="name-dynamic<?= $row['id'] ?>" name="name" required style="width:100%;padding:8px;border-radius:4px;border:1px solid #ddd">
                            </div>
                            <div style="margin-bottom:15px"><label for="comment-dynamic<?= $row['id'] ?>" style="display:block;margin-bottom:5px;font-weight:bold">Komentar:</label>
                                <textarea id="comment-dynamic<?= $row['id'] ?>" name="comment" required style="width:100%;padding:8px;border-radius:4px;border:1px solid #ddd;height:100px"></textarea>
                            </div>
                            <div style="text-align:right"><button type="submit" name="comment_submit" style="background:#4a90e2;color:#fff;padding:10px 20px;border:none;border-radius:4px;cursor:pointer">Kirim Komentar</button></div>
                        </form>
                    </div>
                </article>
        <?php endwhile;
        endif; ?>

        <?php for ($i = 0; $i < 3; $i++): ?>
            <article id="static<?= $i + 1 ?>">
                <img src="<?= $static_imgs[$i] ?>" alt="<?= $static_titles[$i] ?>" width="550">
                <h2><?= $static_titles[$i] ?></h2>
                <p><em>Ditulis pada <?= $static_dates[$i] ?> | Kategori: <?= $static_categs[$i] ?></em></p>
                <p><?= nl2br($static_content[$i]) ?></p>
                <p>Untuk informasi lengkapnya, kunjungi <a href="<?= $static_links[$i] ?>" target="_blank">link ini</a>.</p>
                <div style="margin-top:20px;text-align:left;">
                    <strong>Komentar:</strong>
                    <?php if (isset($_SESSION['static_comments'])):
                        $hasComments = false;
                        foreach ($_SESSION['static_comments'] as $c):
                            if ($c['article_id'] === "static" . ($i + 1)):
                                $hasComments = true; ?>
                                <div style="margin-bottom:8px;">
                                    <b><?= htmlspecialchars($c['name']) ?>:</b> <?= htmlspecialchars($c['comment']) ?>
                                    <span style="color:#888;font-size:12px;">(<?= date('d-m-Y H:i', strtotime($c['time'])) ?>)</span>
                                </div>
                            <?php endif;
                        endforeach;
                        if (!$hasComments): ?>
                            <p style="color:#777;font-style:italic;">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                        <?php endif;
                    else: ?>
                        <p style="color:#777;font-style:italic;">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                    <?php endif; ?>
                </div>
                <div style="margin-top:10px;">
                    <a href="javascript:void(0)" class="comment-button" onclick="toggleCommentForm('static<?= $i + 1 ?>')">Beri Komentar</a>
                </div>
                <div class="comment-form" id="comment-form-static<?= $i + 1 ?>">
                    <h3>Beri Komentar</h3>
                    <form method="post">
                        <input type="hidden" name="article_id" value="static<?= $i + 1 ?>">
                        <div style="margin-bottom:15px;">
                            <label for="name-static<?= $i + 1 ?>" style="display:block;margin-bottom:5px;font-weight:bold;">Nama:</label>
                            <input type="text" id="name-static<?= $i + 1 ?>" name="name" required style="width:100%;padding:8px;border-radius:4px;border:1px solid #ddd;">
                        </div>
                        <div style="margin-bottom:15px;">
                            <label for="comment-static<?= $i + 1 ?>" style="display:block;margin-bottom:5px;font-weight:bold;">Komentar:</label>
                            <textarea id="comment-static<?= $i + 1 ?>" name="comment" required style="width:100%;padding:8px;border-radius:4px;border:1px solid #ddd;height:100px;"></textarea>
                        </div>
                        <div style="text-align:right;">
                            <button type="submit" name="comment_submit" style="background:#4a90e2;color:#fff;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;">Kirim Komentar</button>
                        </div>
                    </form>
                </div>
            </article>
        <?php endfor; ?>
    </div>
    <?php if ($total_pages > 1): ?>
        <div style="text-align:center;margin:30px 0">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="blog.php?page=<?= $i ?>" style="margin:0 5px;<?= $i == $page ? 'font-weight:bold;text-decoration:underline' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    <div class="action-buttons"><a href="tambah_artikel.php" class="add-article-btn">+ Tambah Artikel Baru</a></div>
    <script>
        function toggleCommentForm(id) {
            const formElement = document.getElementById('comment-form-' + id);
            const allForms = document.querySelectorAll('.comment-form');
            allForms.forEach(form => {
                if (form !== formElement) form.classList.remove('active')
            });
            formElement.classList.toggle('active');
            if (formElement.classList.contains('active')) {
                document.getElementById('name-' + id).focus();
                setTimeout(() => {
                    formElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    })
                }, 100)
            }
        }
    </script>
</body>
</html>