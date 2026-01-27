<?php
/**
 * Apartment Model - Handles all apartment operations
 */
class Apartment {
    private $conn;
    private $table = 'apartments';

    public $id;
    public $apartment_no;
    public $category_id;
    public $description;
    public $price;
    public $landlord_id;
    public $location;
    public $bedrooms;
    public $bathrooms;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all apartments with category and occupancy info
    public function getAll() {
        $query = "SELECT a.*, c.name as category_name,
                         CASE WHEN ta.tenant_id IS NOT NULL THEN 1 ELSE 0 END as is_occupied,
                         CONCAT(u.firstname, ' ', u.lastname) as tenant_name,
                         CONCAT(l.firstname, ' ', l.lastname) as landlord_name,
                         l.email as landlord_email,
                         l.contact as landlord_contact,
                         (SELECT filename FROM apartment_photos WHERE apartment_id = a.id AND is_primary = 1 LIMIT 1) as primary_photo
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN tenantapartment ta ON a.id = ta.apartment_id AND ta.status = 1
                  LEFT JOIN users u ON ta.tenant_id = u.id
                  LEFT JOIN users l ON a.landlord_id = l.id
                  ORDER BY a.apartment_no";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get available apartments for public browsing (with photos)
    public function getAvailableWithDetails() {
        $query = "SELECT a.*, c.name as category_name,
                         CONCAT(l.firstname, ' ', l.lastname) as landlord_name,
                         l.id as landlord_id,
                         (SELECT filename FROM apartment_photos WHERE apartment_id = a.id AND is_primary = 1 LIMIT 1) as primary_photo,
                         (SELECT COUNT(*) FROM apartment_photos WHERE apartment_id = a.id) as photo_count
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN users l ON a.landlord_id = l.id
                  WHERE a.id NOT IN (SELECT apartment_id FROM tenantapartment WHERE status = 1)
                  ORDER BY a.date_created DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get single apartment with all details
    public function getByIdWithDetails($id) {
        $query = "SELECT a.*, c.name as category_name,
                         CASE WHEN ta.tenant_id IS NOT NULL THEN 1 ELSE 0 END as is_occupied,
                         CONCAT(l.firstname, ' ', l.lastname) as landlord_name,
                         l.id as landlord_id,
                         l.email as landlord_email,
                         l.contact as landlord_contact
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN tenantapartment ta ON a.id = ta.apartment_id AND ta.status = 1
                  LEFT JOIN users l ON a.landlord_id = l.id
                  WHERE a.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Get single apartment
    public function getById($id) {
        $query = "SELECT a.*, c.name as category_name
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  WHERE a.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Get available apartments
    public function getAvailable() {
        $query = "SELECT a.*, c.name as category_name
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  WHERE a.id NOT IN (SELECT apartment_id FROM tenantapartment WHERE status = 1)
                  ORDER BY a.apartment_no";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create apartment
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET apartment_no = :apartment_no, category_id = :category_id,
                      description = :description, price = :price";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':apartment_no', $this->apartment_no);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Update apartment
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET apartment_no = :apartment_no, category_id = :category_id,
                      description = :description, price = :price
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':apartment_no', $this->apartment_no);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Delete apartment
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
