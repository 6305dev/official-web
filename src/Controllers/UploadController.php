<?php

namespace App\Controllers;

class UploadController
{
    public function uploadFile(): void
    {
        header('Content-Type: application/json');

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['file'];
        $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($uploadError !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Upload failed']);
            return;
        }

        $tmpName = $file['tmp_name'] ?? '';
        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid upload']);
            return;
        }

        $targetDir = __DIR__ . '/../../public_html/share_files/';
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
            return;
        }

        // Enforce 5 MB max file size
        $maxSize = 5 * 1024 * 1024; // 5 MB
        $fileSize = $file['size'] ?? 0;

        if ($fileSize > $maxSize) {
            http_response_code(413);
            echo json_encode(['success' => false, 'message' => 'Ukuran file melebihi batas maksimal 5 MB']);
            return;
        }

        $originalName = basename($file['name'] ?? 'file');
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        $extension = pathinfo($safeName, PATHINFO_EXTENSION);
        $nameOnly = pathinfo($safeName, PATHINFO_FILENAME);
        $randomStr = substr(bin2hex(random_bytes(3)), 0, 6);
        $newFileName = $nameOnly . '_' . $randomStr . ($extension ? '.' . $extension : '');
        $targetFile = $targetDir . $newFileName;

        if (!move_uploaded_file($tmpName, $targetFile)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'fileUrl' => '/share_files/' . $newFileName,
            'fileName' => $newFileName,
        ]);
    }
}
