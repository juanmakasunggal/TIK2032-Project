<?php
require_once 'config.php';

$message_sent = false;
$error_message = '';

// Proses form ketika di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $message = cleanInput($_POST['message'] ?? '');
    
    // Validasi input
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = 'Semua field harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Format email tidak valid!';
    } else {
        // Simpan ke database
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $message_sent = true;
            
            // Reset form values
            $name = $email = $message = '';
            
        } catch (PDOException $e) {
            $error_message = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="styles.css">
    <script src="javascript.js" defer></script>
</head>
<body>
    <header>
        <h1>Contact</h1>
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
        <section id="contact">
            <h2>Contact</h2>
            
            <?php if ($message_sent): ?>
                <div class="success-message" style="color: green; padding: 10px; margin: 10px 0; border: 1px solid green; border-radius: 5px; background-color: #e8f5e8;">
                    Pesan Anda berhasil dikirim! Terima kasih telah menghubungi kami.
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="error-message" style="color: red; padding: 10px; margin: 10px 0; border: 1px solid red; border-radius: 5px; background-color: #ffe6e6;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="contact.php">
                <label for="name">Nama:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                
                <label for="message">Pesan:</label>
                <textarea id="message" name="message" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                
                <button type="submit">Kirim</button>
            </form>
            
            <h3>Contact Person</h3>
            <div class="contact-info">
                <p>Phone: <a href="tel:+6281341281104">+62-813-4128-1104</a></p>
                <p>Instagram: <a href="https://instagram.com/juanatanel" target="_blank">@juanatanel</a></p>
                <p>Email: <a href="mailto:juanmakasunggal026@student.unsrat.ac.id">juanmakasunggal026@student.unsrat.ac.id</a></p>
            </div>

            <?php if (isAdmin()): ?>
                <div class="admin-actions" style="margin-top: 20px; padding: 15px; background-color: #f0f0f0; border-radius: 5px;">
                    <h4>Admin Actions</h4>
                    <a href="admin.php?action=view_messages" style="display: inline-block; padding: 8px 15px; background-color: #007cba; color: white; text-decoration: none; border-radius: 3px;">Lihat Pesan Masuk</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Personal Homepage | Dibuat oleh Juan Makasunggal</p>
    </footer>

    <style>
        form {
            max-width: 600px;
            margin-bottom: 30px;
        }
        
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        form input, form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }
        
        form textarea {
            height: 120px;
            resize: vertical;
        }
        
        form button {
            background-color: #007cba;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        form button:hover {
            background-color: #005a87;
        }
        
        .contact-info {
            margin: 20px 0;
        }
        
        .contact-info p {
            margin: 8px 0;
        }
        
        .contact-info a {
            color: #007cba;
            text-decoration: none;
        }
        
        .contact-info a:hover {
            text-decoration: underline;
        }
    </style>
</body>
</html>