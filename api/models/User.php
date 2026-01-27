<?php
/**
 * User Model - Handles all user/tenant operations
 */
class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $name;
    public $username;
    public $firstname;
    public $middlename;
    public $lastname;
    public $email;
    public $password;
    public $contact;
    public $user_type;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all tenants
    public function getTenants() {
        $query = "SELECT u.*, ta.apartment_id, ta.date_in, a.apartment_no, a.price 
                  FROM " . $this->table . " u
                  LEFT JOIN tenantapartment ta ON u.id = ta.tenant_id AND ta.status = 1
                  LEFT JOIN apartments a ON ta.apartment_id = a.id
                  WHERE u.user_type = '2'
                  ORDER BY u.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get single user
    public function getById($id) {
        $query = "SELECT u.*, ta.apartment_id, ta.date_in, a.apartment_no, a.price, c.name as category_name
                  FROM " . $this->table . " u
                  LEFT JOIN tenantapartment ta ON u.id = ta.tenant_id AND ta.status = 1
                  LEFT JOIN apartments a ON ta.apartment_id = a.id
                  LEFT JOIN categories c ON a.category_id = c.id
                  WHERE u.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Login
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch();
        if ($user && ($user['password'] === $password || $user['password'] === md5($password) || password_verify($password, $user['password']))) {
            unset($user['password']);
            return $user;
        }
        return false;
    }

    // Register new user (tenant or landlord)
    public function register($userType = '2') {
        // Check if email already exists
        $stmt = $this->conn->prepare("SELECT id FROM " . $this->table . " WHERE email = :email");
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        if ($stmt->fetch()) {
            throw new Exception('Email already registered');
        }

        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO " . $this->table . " 
                  SET firstname = :firstname, middlename = :middlename, lastname = :lastname,
                      email = :email, contact = :contact, password = :password, user_type = :user_type";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':middlename', $this->middlename);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contact', $this->contact);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':user_type', $userType);
        
        if ($stmt->execute()) {
            $userId = $this->conn->lastInsertId();
            return $this->getById($userId);
        }
        return false;
    }

    // Create tenant (legacy method)
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET firstname = :firstname, middlename = :middlename, lastname = :lastname,
                      email = :email, contact = :contact, password = :password, user_type = '2'";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':middlename', $this->middlename);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contact', $this->contact);
        $stmt->bindParam(':password', $this->password);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Get all landlords
    public function getLandlords() {
        $query = "SELECT id, firstname, middlename, lastname, email, contact, user_type 
                  FROM " . $this->table . " 
                  WHERE user_type = '3'
                  ORDER BY firstname, lastname";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get landlord for an apartment
    public function getApartmentLandlord($apartment_id) {
        $query = "SELECT u.id, u.firstname, u.lastname, u.email, u.contact
                  FROM " . $this->table . " u
                  JOIN apartments a ON a.landlord_id = u.id
                  WHERE a.id = :apt_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':apt_id', $apartment_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Get tenant's payment history
    public function getPaymentHistory($tenant_id) {
        $query = "SELECT p.*, a.apartment_no 
                  FROM payments p
                  LEFT JOIN tenantapartment ta ON p.tenant_id = ta.tenant_id AND ta.status = 1
                  LEFT JOIN apartments a ON ta.apartment_id = a.id
                  WHERE p.tenant_id = :tenant_id
                  ORDER BY p.date_created DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Get landlord's income summary
    public function getLandlordIncome($landlord_id) {
        $query = "SELECT 
                    COALESCE(SUM(p.amount), 0) as total_income,
                    COUNT(DISTINCT p.id) as total_payments,
                    COUNT(DISTINCT a.id) as total_properties
                  FROM apartments a
                  LEFT JOIN tenantapartment ta ON a.id = ta.apartment_id AND ta.status = 1
                  LEFT JOIN payments p ON ta.tenant_id = p.tenant_id
                  WHERE a.landlord_id = :landlord_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':landlord_id', $landlord_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Get landlord's properties with tenant info
    public function getLandlordProperties($landlord_id) {
        $query = "SELECT a.*, c.name as category_name,
                         CASE WHEN ta.tenant_id IS NOT NULL THEN 1 ELSE 0 END as is_occupied,
                         CONCAT(u.firstname, ' ', u.lastname) as tenant_name,
                         u.email as tenant_email,
                         u.contact as tenant_contact,
                         (SELECT filename FROM apartment_photos WHERE apartment_id = a.id AND is_primary = 1 LIMIT 1) as primary_photo
                  FROM apartments a
                  LEFT JOIN categories c ON a.category_id = c.id
                  LEFT JOIN tenantapartment ta ON a.id = ta.apartment_id AND ta.status = 1
                  LEFT JOIN users u ON ta.tenant_id = u.id
                  WHERE a.landlord_id = :landlord_id
                  ORDER BY a.apartment_no";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':landlord_id', $landlord_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Update user
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET firstname = :firstname, middlename = :middlename, lastname = :lastname,
                      email = :email, contact = :contact
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':middlename', $this->middlename);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contact', $this->contact);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Get dashboard stats
    public function getDashboardStats() {
        $stats = [];
        
        // Total tenants
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = '2'");
        $stats['total_tenants'] = $stmt->fetch()['count'];
        
        // Total apartments
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM apartments");
        $stats['total_apartments'] = $stmt->fetch()['count'];
        
        // Occupied apartments
        $stmt = $this->conn->query("SELECT COUNT(DISTINCT apartment_id) as count FROM tenantapartment WHERE status = 1");
        $stats['occupied_apartments'] = $stmt->fetch()['count'];
        
        // Total payments this month
        $stmt = $this->conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE DATE_FORMAT(date_created, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')");
        $stats['monthly_revenue'] = $stmt->fetch()['total'];
        
        // Total payments all time
        $stmt = $this->conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments");
        $stats['total_revenue'] = $stmt->fetch()['total'];
        
        return $stats;
    }
}
