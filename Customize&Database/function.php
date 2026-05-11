<?php

require_once __DIR__ . '/../Vendor/phpMailer/PHPMailer.php';
require_once __DIR__ . '/../Vendor/phpMailer/SMTP.php';
require_once __DIR__ . '/../Vendor/phpMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';          
        $mail->SMTPAuth   = true;
        $mail->Username   = 'phangyuxue@graduate.utm.my';    
        $mail->Password   = 'wcuxenryjgflqdio';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom('phangyuxue@graduate.utm.my', 'BookNest System');  
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);  

        $mail->send();
        return true;
    } 
    
    catch (Exception $e) {
        error_log("There is an error on sending email: " . $mail->ErrorInfo);
        return false;
    }
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