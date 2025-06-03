<?php
// process_contact.php - Memproses form contact
require_once 'config.php';

// Fungsi untuk membersihkan input
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk validasi email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Response array
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data dari form
    $name = cleanInput($_POST['name'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $message = cleanInput($_POST['message'] ?? '');
    
    // Validasi input
    $errors = array();
    
    if (empty($name)) {
        $errors[] = "Nama harus diisi";
    } elseif (strlen($name) < 2) {
        $errors[] = "Nama minimal 2 karakter";
    }
    
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!isValidEmail($email)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (empty($message)) {
        $errors[] = "Pesan harus diisi";
    } elseif (strlen($message) < 10) {
        $errors[] = "Pesan minimal 10 karakter";
    }
    
    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            // Prepare statement untuk keamanan
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
            $result = $stmt->execute([$name, $email, $message]);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = "Pesan Anda berhasil dikirim! Terima kasih telah menghubungi kami.";
            } else {
                $response['success'] = false;
                $response['message'] = "Terjadi kesalahan saat menyimpan pesan. Silakan coba lagi.";
            }
            
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'] = "Database error: " . $e->getMessage();
        }
    } else {
        $response['success'] = false;
        $response['message'] = implode(", ", $errors);
    }
} else {
    $response['success'] = false;
    $response['message'] = "Method tidak diizinkan";
}

// Return JSON response untuk AJAX
header('Content-Type: application/json');
echo json_encode($response);
?>