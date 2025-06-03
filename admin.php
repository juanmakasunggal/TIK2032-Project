<?php
require_once 'config.php';

// Cek apakah user sudah login sebagai admin
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$action = $_GET['action'] ?? 'dashboard';
$message = '';
$error = '';

// Handle different actions
switch ($action) {
    case 'add_article':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = cleanInput($_POST['title'] ?? '');
            $content = cleanInput($_POST['content'] ?? '');
            $status = $_POST['status'] ?? 'published';
            
            if (!empty($title) && !empty($content)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO articles (title, content, status) VALUES (?, ?, ?)");
                    $stmt->execute([$title, $content, $status]);
                    $message = 'Artikel berhasil ditambahkan!';
                } catch (PDOException $e) {
                    $error = 'Error menambahkan artikel: ' . $e->getMessage();
                }
            } else {
                $error = 'Judul dan konten artikel harus diisi!';
            }
        }
        break;
        
    case 'edit_article':
        $id = $_GET['id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = cleanInput($_POST['title'] ?? '');
            $content = cleanInput($_POST['content'] ?? '');
            $status = $_POST['status'] ?? 'published';
            
            if (!empty($title) && !empty($content)) {
                try {
                    $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, status = ? WHERE id = ?");
                    $stmt->execute([$title, $content, $status, $id]);
                    $message = 'Artikel berhasil diupdate!';
                } catch (PDOException $e) {
                    $error = 'Error mengupdate artikel: ' . $e->getMessage();
                }
            }
        }
        
        // Get article data for editing
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        if (!$article) {
            $error = 'Artikel tidak ditemukan!';
            $action = 'dashboard';
        }
        break;
        
    case 'delete_article':
        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Artikel berhasil dihapus!';
        } catch (PDOException $e) {
            $error = 'Error menghapus artikel: ' . $e->getMessage();
        }
        $action = 'dashboard'; // Redirect to dashboard after delete
        break;
        
    case 'mark_read':
        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Pesan ditandai sebagai sudah dibaca!';
        } catch (PDOException $e) {
            $error = 'Error updating message: ' . $e->getMessage();
        }
        break;
        
    case 'delete_message':
        $id = $_GET['id'] ?? 0;
        try {
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Pesan berhasil dihapus!';
        } catch (PDOException $e) {
            $error = 'Error menghapus pesan: ' . $e->getMessage();
        }
        break;
}

// Get data for dashboard
$articles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll();
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$unread_count = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .admin-nav { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .admin-nav a { margin-right: 15px; padding: 8px 15px; background: #007cba; color: white; text-decoration: none; border-radius: 3px; }
        .admin-nav a:hover { background: #005a87; }
        .admin-nav a.active { background: #28a745; }
        
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { flex: 1; padding: 20px; background: #f8f9fa; border-radius: 5px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; color: #007cba; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .data-table th { background-color: #f8f9fa; font-weight: bold; }
        .data-table tr:hover { background-color: #f5f5f5; }
        
        .btn { padding: 6px 12px; margin: 2px; text-decoration: none; border-radius: 3px; font-size: 12px; }
        .btn-edit { background: #ffc107; color: #000; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-view { background: #17a2b8; color: white; }
        .btn-mark-read { background: #28a745; color: white; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { 
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; 
        }
        .form-group textarea { height: 200px; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .unread { background-color: #fff3cd !important; }
        .message-content { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="blog.php">Blog</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="admin.php">Admin Panel</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="admin-container">
        <div class="admin-nav">
            <a href="admin.php" class="<?php echo $action === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
            <a href="admin.php?action=add_article" class="<?php echo $action === 'add_article' ? 'active' : ''; ?>">Tambah Artikel</a>
            <a href="admin.php?action=view_messages" class="<?php echo $action === 'view_messages' ? 'active' : ''; ?>">Pesan Kontak (<?php echo $unread_count; ?>)</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($action === 'dashboard'): ?>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($articles); ?></div>
                    <div>Total Artikel</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($messages); ?></div>
                    <div>Total Pesan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $unread_count; ?></div>
                    <div>Pesan Belum Dibaca</div>
                </div>
            </div>

            <h3>Artikel Terbaru</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($articles, 0, 10) as $article): ?>
                    <tr>
                        <td><?php echo $article['id']; ?></td>
                        <td><?php echo htmlspecialchars($article['title']); ?></td>
                        <td><?php echo ucfirst($article['status']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($article['created_at'])); ?></td>
                        <td>
                            <a href="admin.php?action=edit_article&id=<?php echo $article['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="admin.php?action=delete_article&id=<?php echo $article['id']; ?>" 
                               onclick="return confirm('Yakin ingin menghapus artikel ini?')" class="btn btn-delete">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'add_article' || $action === 'edit_article'): ?>
            <h3><?php echo $action === 'add_article' ? 'Tambah' : 'Edit'; ?> Artikel</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="title">Judul Artikel:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Konten:</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($article['content'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="published" <?php echo ($article['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="draft" <?php echo ($article['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
                
                <button type="submit" class="btn" style="background: #007cba; color: white; padding: 12px 20px;">
                    <?php echo $action === 'add_article' ? 'Tambah' : 'Update'; ?> Artikel
                </button>
            </form>

        <?php elseif ($action === 'view_messages'): ?>
            <h3>Pesan Kontak</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Pesan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message_item): ?>
                    <tr class="<?php echo !$message_item['is_read'] ? 'unread' : ''; ?>">
                        <td><?php echo $message_item['id']; ?></td>
                        <td><?php echo htmlspecialchars($message_item['name']); ?></td>
                        <td><?php echo htmlspecialchars($message_item['email']); ?></td>
                        <td class="message-content" title="<?php echo htmlspecialchars($message_item['message']); ?>">
                            <?php echo htmlspecialchars(substr($message_item['message'], 0, 50)) . (strlen($message_item['message']) > 50 ? '...' : ''); ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($message_item['created_at'])); ?></td>
                        <td><?php echo $message_item['is_read'] ? 'Dibaca' : 'Belum Dibaca'; ?></td>
                        <td>
                            <?php if (!$message_item['is_read']): ?>
                                <a href="admin.php?action=mark_read&id=<?php echo $message_item['id']; ?>" class="btn btn-mark-read">Tandai Dibaca</a>
                            <?php endif; ?>
                            <a href="admin.php?action=view_message_detail&id=<?php echo $message_item['id']; ?>" class="btn btn-view">Lihat</a>
                            <a href="admin.php?action=delete_message&id=<?php echo $message_item['id']; ?>" 
                               onclick="return confirm('Yakin ingin menghapus pesan ini?')" class="btn btn-delete">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'view_message_detail'): ?>
            <?php 
            $id = $_GET['id'] ?? 0;
            $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $stmt->execute([$id]);
            $message_detail = $stmt->fetch();
            
            if ($message_detail && !$message_detail['is_read']) {
                // Mark as read when viewing
                $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
                $stmt->execute([$id]);
            }
            ?>
            
            <?php if ($message_detail): ?>
                <h3>Detail Pesan</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <p><strong>Dari:</strong> <?php echo htmlspecialchars($message_detail['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($message_detail['email']); ?></p>
                    <p><strong>Tanggal:</strong> <?php echo date('d F Y, H:i', strtotime($message_detail['created_at'])); ?></p>
                    <p><strong>Status:</strong> <?php echo $message_detail['is_read'] ? 'Sudah Dibaca' : 'Belum Dibaca'; ?></p>
                </div>
                
                <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                    <h4>Pesan:</h4>
                    <p style="line-height: 1.6;"><?php echo nl2br(htmlspecialchars($message_detail['message'])); ?></p>
                </div>
                
                <div style="margin-top: 20px;">
                    <a href="admin.php?action=view_messages" class="btn" style="background: #6c757d; color: white; padding: 10px 20px;">Kembali ke Daftar Pesan</a>
                    <a href="mailto:<?php echo htmlspecialchars($message_detail['email']); ?>" class="btn" style="background: #007cba; color: white; padding: 10px 20px;">Balas via Email</a>
                </div>
            <?php else: ?>
                <div class="alert alert-error">Pesan tidak ditemukan!</div>
                <a href="admin.php?action=view_messages" class="btn" style="background: #6c757d; color: white; padding: 10px 20px;">Kembali ke Daftar Pesan</a>
            <?php endif; ?>

        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Personal Homepage | Dibuat oleh Juan Makasunggal</p>
    </footer>
</body>
</html>