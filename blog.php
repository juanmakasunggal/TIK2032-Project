<?php
require_once 'config.php';

// Ambil artikel dari database
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE status = 'published' ORDER BY created_at DESC");
    $stmt->execute();
    $articles = $stmt->fetchAll();
} catch (PDOException $e) {
    $articles = [];
    $error_message = "Error mengambil artikel: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="styles.css">
    <script src="javascript.js" defer></script>
</head>
<body>
    <header>
        <h1>Blog</h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="gallery.html">Gallery</a></li>
                <li><a href="blog.php">Blog</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="admin.php">Admin Panel</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Admin Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section id="blog">
            <h2>Blog</h2>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message" style="color: red; padding: 10px; margin: 10px 0; border: 1px solid red; border-radius: 5px;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($articles)): ?>
                <p>Belum ada artikel yang dipublikasikan.</p>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <article class="blog-article">
                        <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                        <div class="article-meta">
                            <small>Dipublikasikan pada: <?php echo date('d F Y, H:i', strtotime($article['created_at'])); ?></small>
                        </div>
                        <div class="article-content">
                            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                        </div>
                        <?php if ($article['updated_at'] != $article['created_at']): ?>
                            <div class="article-updated">
                                <small><em>Terakhir diupdate: <?php echo date('d F Y, H:i', strtotime($article['updated_at'])); ?></em></small>
                            </div>
                        <?php endif; ?>
                    </article>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
                <div class="admin-actions" style="margin-top: 20px; padding: 15px; background-color: #f0f0f0; border-radius: 5px;">
                    <h4>Admin Actions</h4>
                    <a href="admin.php?action=add_article" style="display: inline-block; padding: 8px 15px; background-color: #007cba; color: white; text-decoration: none; border-radius: 3px; margin-right: 10px;">Tambah Artikel Baru</a>
                    <a href="admin.php" style="display: inline-block; padding: 8px 15px; background-color: #666; color: white; text-decoration: none; border-radius: 3px;">Kelola Artikel</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Personal Homepage | Dibuat oleh Juan Makasunggal</p>
    </footer>

    <style>
        .blog-article {
            margin-bottom: 20px;
            padding: 15px;
            border-left: 4px solid #007cba;
        }
        
        .article-meta {
            color: #666;
            margin-bottom: 10px;
        }
        
        .article-content {
            line-height: 1.6;
            margin-bottom: 10px;
        }
        
        .article-updated {
            color: #888;
            font-style: italic;
        }
        
        .error-message {
            background-color: #ffe6e6;
        }
    </style>
</body>
</html>