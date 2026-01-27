<?php
/**
 * ApartmentPhoto Model - Handles apartment photo uploads
 */
class ApartmentPhoto {
    private $conn;
    private $table = 'apartment_photos';
    private $uploadDir = '../uploads/apartments/';

    public $id;
    public $apartment_id;
    public $filename;
    public $is_primary;

    public function __construct($db) {
        $this->conn = $db;
        // Create upload directory if not exists
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // Create photos table if not exists
    public function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            apartment_id INT NOT NULL,
            filename VARCHAR(255) NOT NULL,
            is_primary TINYINT DEFAULT 0,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (apartment_id) REFERENCES apartments(id) ON DELETE CASCADE
        )";
        return $this->conn->exec($query);
    }

    // Upload photo
    public function upload($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            throw new Exception('File too large. Maximum size is 5MB.');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'apt_' . $this->apartment_id . '_' . time() . '_' . uniqid() . '.' . $extension;
        $destination = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $this->filename = $filename;
            return $this->save();
        }
        
        throw new Exception('Failed to upload file.');
    }

    // Save photo record
    private function save() {
        // Check if this is the first photo (make it primary)
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM " . $this->table . " WHERE apartment_id = :apt_id");
        $stmt->bindParam(':apt_id', $this->apartment_id, PDO::PARAM_INT);
        $stmt->execute();
        $isPrimary = $stmt->fetch()['count'] == 0 ? 1 : ($this->is_primary ? 1 : 0);

        $query = "INSERT INTO " . $this->table . " 
                  SET apartment_id = :apartment_id, filename = :filename, is_primary = :is_primary";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':apartment_id', $this->apartment_id, PDO::PARAM_INT);
        $stmt->bindParam(':filename', $this->filename);
        $stmt->bindParam(':is_primary', $isPrimary, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Get photos for apartment
    public function getByApartment($apartment_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE apartment_id = :apt_id 
                  ORDER BY is_primary DESC, date_created ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':apt_id', $apartment_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Set primary photo
    public function setPrimary($photo_id, $apartment_id) {
        // First, unset all as primary
        $query = "UPDATE " . $this->table . " SET is_primary = 0 WHERE apartment_id = :apt_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':apt_id', $apartment_id, PDO::PARAM_INT);
        $stmt->execute();

        // Set this one as primary
        $query = "UPDATE " . $this->table . " SET is_primary = 1 WHERE id = :id AND apartment_id = :apt_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $photo_id, PDO::PARAM_INT);
        $stmt->bindParam(':apt_id', $apartment_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Delete photo
    public function delete($photo_id) {
        // Get filename first
        $stmt = $this->conn->prepare("SELECT filename FROM " . $this->table . " WHERE id = :id");
        $stmt->bindParam(':id', $photo_id, PDO::PARAM_INT);
        $stmt->execute();
        $photo = $stmt->fetch();

        if ($photo) {
            // Delete file
            $filepath = $this->uploadDir . $photo['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            // Delete record
            $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id = :id");
            $stmt->bindParam(':id', $photo_id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        return false;
    }

    // Get upload URL path
    public function getPhotoUrl($filename) {
        return '/uploads/apartments/' . $filename;
    }
}
