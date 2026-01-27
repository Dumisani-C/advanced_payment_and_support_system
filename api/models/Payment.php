<?php
/**
 * Payment Model - Handles all payment operations
 */
class Payment {
    private $conn;
    private $table = 'payments';

    public $id;
    public $tenant_id;
    public $apartment_no;
    public $amount;
    public $invoice;
    public $date_created;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all payments
    public function getAll() {
        $query = "SELECT p.*, 
                         CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) as tenant_name,
                         u.email as tenant_email
                  FROM " . $this->table . " p
                  INNER JOIN users u ON p.tenant_id = u.id
                  ORDER BY p.date_created DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get payments by tenant
    public function getByTenant($tenant_id) {
        $query = "SELECT p.*, a.apartment_no as apt_no
                  FROM " . $this->table . " p
                  LEFT JOIN apartments a ON p.apartment_no = a.apartment_no
                  WHERE p.tenant_id = :tenant_id
                  ORDER BY p.date_created DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Get single payment
    public function getById($id) {
        $query = "SELECT p.*, 
                         CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) as tenant_name,
                         u.email as tenant_email, u.contact as tenant_contact
                  FROM " . $this->table . " p
                  INNER JOIN users u ON p.tenant_id = u.id
                  WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Create payment
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET tenant_id = :tenant_id, apartment_no = :apartment_no, 
                      amount = :amount, invoice = :invoice, date_created = :date_created";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':tenant_id', $this->tenant_id, PDO::PARAM_INT);
        $stmt->bindParam(':apartment_no', $this->apartment_no);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':invoice', $this->invoice);
        $stmt->bindParam(':date_created', $this->date_created);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Get monthly report
    public function getMonthlyReport($year_month) {
        $query = "SELECT p.*, 
                         CONCAT(u.firstname, ' ', u.lastname) as tenant_name,
                         a.apartment_no as apt_no
                  FROM " . $this->table . " p
                  INNER JOIN users u ON p.tenant_id = u.id
                  LEFT JOIN apartments a ON p.apartment_no = a.apartment_no
                  WHERE DATE_FORMAT(p.date_created, '%Y-%m') = :year_month
                  ORDER BY p.date_created ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_month', $year_month);
        $stmt->execute();
        return $stmt;
    }

    // Get payment summary by tenant
    public function getTenantSummary($tenant_id) {
        $query = "SELECT 
                    COALESCE(SUM(amount), 0) as total_paid,
                    COUNT(*) as payment_count,
                    MAX(date_created) as last_payment
                  FROM " . $this->table . " 
                  WHERE tenant_id = :tenant_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
