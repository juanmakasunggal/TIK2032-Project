<?php
// contact_handler.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfigurasi database
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "personal_website";

// Debug: Cek apakah form di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h3>DEBUG: Form telah di-submit</h3>";
    echo "<pre>Data POST yang diterima: ";
    print_r($_POST);
    echo "</pre>";
    
    // Ambil data dari form
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    echo "<h3>DEBUG: Data yang akan diinsert:</h3>";
    echo "Name: " . htmlspecialchars($name) . "<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Message: " . htmlspecialchars($message) . "<br>";
    
    // Validasi data
    if (empty($name) || empty($email) || empty($message)) {
        die("ERROR: Semua field harus diisi!");
    }
    
    try {
        // Buat koneksi database
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Cek koneksi
        if ($conn->connect_error) {
            die("DEBUG: Koneksi gagal: " . $conn->connect_error);
        }
        
        echo "<h3>DEBUG: Koneksi database berhasil</h3>";
        
        // Siapkan query
        $sql = "INSERT INTO contacts (name, email, message, status, created_at) VALUES (?, ?, ?, 'unread', NOW())";
        
        echo "<h3>DEBUG: Query yang akan dijalankan:</h3>";
        echo htmlspecialchars($sql) . "<br>";
        
        // Prepared statement
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("DEBUG: Prepare statement gagal: " . $conn->error);
        }
        
        // Bind parameter
        $stmt->bind_param("sss", $name, $email, $message);
        
        // Eksekusi query
        if ($stmt->execute()) {
            echo "<h3 style='color: green;'>SUCCESS: Data berhasil disimpan!</h3>";
            echo "ID baru: " . $conn->insert_id . "<br>";
            
            // Verifikasi dengan query SELECT
            $verify_sql = "SELECT * FROM contacts WHERE id = " . $conn->insert_id;
            $result = $conn->query($verify_sql);
            
            if ($result && $result->num_rows > 0) {
                echo "<h3>DEBUG: Verifikasi data yang baru disimpan:</h3>";
                $row = $result->fetch_assoc();
                echo "<pre>";
                print_r($row);
                echo "</pre>";
            }
            
        } else {
            echo "<h3 style='color: red;'>ERROR: Gagal menyimpan data</h3>";
            echo "Error: " . $stmt->error . "<br>";
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        echo "<h3 style='color: red;'>EXCEPTION: " . $e->getMessage() . "</h3>";
    }
    
} else {
    echo "<h3>DEBUG: Form belum di-submit (method bukan POST)</h3>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Form Test</title>
</head>
<body>
    <h2>Test Contact Form</h2>
    <form method="POST" action="">
        <div>
            <label>Name:</label><br>
            <input type="text" name="name" required style="width: 300px; padding: 5px;">
        </div><br>
        
        <div>
            <label>Email:</label><br>
            <input type="email" name="email" required style="width: 300px; padding: 5px;">
        </div><br>
        
        <div>
            <label>Message:</label><br>
            <textarea name="message" required style="width: 300px; height: 100px; padding: 5px;"></textarea>
        </div><br>
        
        <button type="submit" style="padding: 10px 20px;">Send Message</button>
    </form>
    
    <hr>
    <h3>Cara Debugging:</h3>
    <ol>
        <li>Jalankan file ini di browser</li>
        <li>Isi form dan submit</li>
        <li>Lihat pesan debug yang muncul</li>
        <li>Cek apakah ada error message</li>
        <li>Refresh phpMyAdmin untuk melihat data baru</li>
    </ol>
</body>
</html>