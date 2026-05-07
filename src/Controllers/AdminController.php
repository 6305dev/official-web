<?php

namespace App\Controllers;

use App\Databases\SQLite;

class AdminController
{

    private SQLite $sqlite;

    public function __construct()
    {
        $dbPath = __DIR__ . '/../../src/Databases/data.db';
        $this->sqlite = new SQLite($dbPath);
    }

    public function createArticle(): void
    {
        header('Content-Type: application/json');

        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $params = [
            'title' => $data['title'],
            'created' => $data['created'],
            'cover' => $data['image'],
            'permalink' => $data['permalink'],
            'content' => $data['content'],
        ];

        $this->sqlite->create('articles', $params);

        echo json_encode(['success' => true, 'message' => 'Article created successfully']);
    }

    public function updateArticle(): void
    {
        header('Content-Type: application/json');

        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $id = $data['id'] ?? null;
        if ($id === null || $id === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing article id']);
            return;
        }

        $params = [
            'title' => $data['title'],
            'created' => $data['created'],
            'cover' => $data['image'],
            'permalink' => $data['permalink'],
            'content' => $data['content'],
        ];

        $this->sqlite->update('articles', $params, ['id' => $id]);

        echo json_encode(['success' => true, 'message' => 'Article updated successfully']);
    }

    public function getArticle(int $id): void
    {
        header('Content-Type: application/json');

        $result = $this->sqlite->read('articles', ['id' => $id], 1);
        if (count($result) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Article not found']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $result[0]]);
    }

    public function deleteArticle(int $id): void
    {
        header('Content-Type: application/json');

        $result = $this->sqlite->delete('articles', ['id' => $id]);
        if (!$result) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete article']);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'Article deleted successfully']);
    }

    public function createDocument(): void
    {
        header('Content-Type: application/json');

        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $params = [
            'title' => $data['title'],
            'path' => $data['path'],
            'category' => $data['category'],
            'created' => date('Y-m-d H:i:s'),
        ];

        $this->sqlite->create('documents', $params);

        echo json_encode(['success' => true, 'message' => 'Document created successfully']);
    }

    public function deleteDocument(int $id): void
    {
        header('Content-Type: application/json');

        // Fetch document to get file path before deleting
        $doc = $this->sqlite->read('documents', ['id' => $id], 1);
        if (count($doc) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Document not found']);
            return;
        }

        // Delete file from storage
        $filePath = __DIR__ . '/../../public_html/share_files/' . ($doc[0]['path'] ?? '');
        if (is_file($filePath)) {
            unlink($filePath);
        }

        $result = $this->sqlite->delete('documents', ['id' => $id]);
        if (!$result) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete document']);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
    }

    public function getSliders(): void
    {
        header('Content-Type: application/json');

        $imgDir = __DIR__ . '/../../public_html/assets/img/';
        $sliders = [];

        for ($i = 1; $i <= 4; $i++) {
            $filename = 'slide' . $i . '.jpg';
            $filePath = $imgDir . $filename;
            $sliders[] = [
                'slot' => $i,
                'filename' => $filename,
                'url' => '/assets/img/' . $filename,
                'exists' => file_exists($filePath),
                'size' => file_exists($filePath) ? filesize($filePath) : 0,
            ];
        }

        echo json_encode(['success' => true, 'data' => $sliders]);
    }

    public function updateSlider(): void
    {
        header('Content-Type: application/json');

        if (!isset($_FILES['file']) || !isset($_POST['slot'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File dan slot harus diisi']);
            return;
        }

        $slot = (int) $_POST['slot'];
        if ($slot < 1 || $slot > 4) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Slot tidak valid (1-4)']);
            return;
        }

        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Upload gagal']);
            return;
        }

        $tmpName = $file['tmp_name'] ?? '';
        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Upload tidak valid']);
            return;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($tmpName) ?: ($file['type'] ?? '');
        if (!in_array($fileType, $allowedTypes, true)) {
            http_response_code(415);
            echo json_encode(['success' => false, 'message' => 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.']);
            return;
        }

        $targetDir = __DIR__ . '/../../public_html/assets/img/';
        $targetFile = $targetDir . 'slide' . $slot . '.jpg';

        if (!move_uploaded_file($tmpName, $targetFile)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file']);
            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Slider ' . $slot . ' berhasil diperbarui',
            'url' => '/assets/img/slide' . $slot . '.jpg?v=' . time(),
        ]);
    }
}
