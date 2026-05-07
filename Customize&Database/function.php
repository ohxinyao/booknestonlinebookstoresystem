<?php
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function sendEmail($to, $subject, $body) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: noreply@booknest.local\r\n";
    
    return mail($to, $subject, $body, $headers);
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function uploadFile($file, $targetDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $targetDir . $fileName;
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return false;
    }
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    }
    return false;
}

function getStarRating($rating) {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    $stars = str_repeat('★', $full) . ($half ? '½' : '') . str_repeat('☆', $empty);
    return $stars;
}
?>