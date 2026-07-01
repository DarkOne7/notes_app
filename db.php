<?php   
$server = "localhost";
$username = "root";
$password = "";
$database = "notes_app";
$conn = new mysqli($server, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Ensure the users table exists and create a dummy admin account if needed.
$conn->query(
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

// Add email column to older schema versions that did not include it.
$emailColumnResult = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
if ($emailColumnResult && $emailColumnResult->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' AFTER username");
}

$adminUsername = 'admin';
$adminEmail = 'admin@example.com';
$adminPassword = 'admin123';

$stmt = $conn->prepare("SELECT id, password, email FROM users WHERE username = ?");
if ($stmt) {
    $stmt->bind_param("s", $adminUsername);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $existingPassword = $user['password'];
        $needsUpdate = false;

        if (password_get_info($existingPassword)['algo'] === 0) {
            $needsUpdate = true;
        }

        if (empty($user['email'])) {
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ?, email = ? WHERE id = ?");
            if ($update) {
                $update->bind_param("ssi", $hashedPassword, $adminEmail, $user['id']);
                $update->execute();
                $update->close();
            }
        }
    } else {
        $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($insert) {
            $insert->bind_param("sss", $adminUsername, $adminEmail, $hashedPassword);
            $insert->execute();
            $insert->close();
        }
    }

    $stmt->close();
}
?>
