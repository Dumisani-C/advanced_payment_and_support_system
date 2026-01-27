<?php
/**
 * Message Model - Handles messaging between tenants and landlords
 */
class Message {
    private $conn;
    private $table = 'messages';

    public $id;
    public $sender_id;
    public $receiver_id;
    public $apartment_id;
    public $message;
    public $is_read;
    public $date_created;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create messages table if not exists
    public function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            apartment_id INT NULL,
            message TEXT NOT NULL,
            is_read TINYINT DEFAULT 0,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id),
            FOREIGN KEY (receiver_id) REFERENCES users(id),
            FOREIGN KEY (apartment_id) REFERENCES apartments(id) ON DELETE SET NULL
        )";
        return $this->conn->exec($query);
    }

    // Send a message
    public function send() {
        $query = "INSERT INTO " . $this->table . " 
                  SET sender_id = :sender_id, receiver_id = :receiver_id, 
                      apartment_id = :apartment_id, message = :message";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sender_id', $this->sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $this->receiver_id, PDO::PARAM_INT);
        $stmt->bindParam(':apartment_id', $this->apartment_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $this->message);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Get conversation between two users
    public function getConversation($user1_id, $user2_id) {
        $query = "SELECT m.*, 
                         CONCAT(s.firstname, ' ', s.lastname) as sender_name,
                         CONCAT(r.firstname, ' ', r.lastname) as receiver_name,
                         a.apartment_no
                  FROM " . $this->table . " m
                  LEFT JOIN users s ON m.sender_id = s.id
                  LEFT JOIN users r ON m.receiver_id = r.id
                  LEFT JOIN apartments a ON m.apartment_id = a.id
                  WHERE (m.sender_id = :user1 AND m.receiver_id = :user2)
                     OR (m.sender_id = :user2b AND m.receiver_id = :user1b)
                  ORDER BY m.date_created ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user1', $user1_id, PDO::PARAM_INT);
        $stmt->bindParam(':user2', $user2_id, PDO::PARAM_INT);
        $stmt->bindParam(':user1b', $user1_id, PDO::PARAM_INT);
        $stmt->bindParam(':user2b', $user2_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Get all conversations for a user (grouped by other party)
    public function getConversations($user_id) {
        $query = "SELECT 
                    CASE WHEN m.sender_id = :uid THEN m.receiver_id ELSE m.sender_id END as other_user_id,
                    CONCAT(u.firstname, ' ', u.lastname) as other_user_name,
                    u.user_type as other_user_type,
                    MAX(m.date_created) as last_message_time,
                    SUM(CASE WHEN m.receiver_id = :uid2 AND m.is_read = 0 THEN 1 ELSE 0 END) as unread_count,
                    (SELECT message FROM messages m2 
                     WHERE ((m2.sender_id = :uid3 AND m2.receiver_id = u.id) 
                        OR (m2.sender_id = u.id AND m2.receiver_id = :uid4))
                     ORDER BY m2.date_created DESC LIMIT 1) as last_message
                  FROM " . $this->table . " m
                  JOIN users u ON u.id = CASE WHEN m.sender_id = :uid5 THEN m.receiver_id ELSE m.sender_id END
                  WHERE m.sender_id = :uid6 OR m.receiver_id = :uid7
                  GROUP BY other_user_id, other_user_name, other_user_type
                  ORDER BY last_message_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':uid2', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':uid3', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':uid4', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':uid5', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':uid6', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':uid7', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Mark messages as read
    public function markAsRead($sender_id, $receiver_id) {
        $query = "UPDATE " . $this->table . " 
                  SET is_read = 1 
                  WHERE sender_id = :sender AND receiver_id = :receiver AND is_read = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sender', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':receiver', $receiver_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Get unread count for user
    public function getUnreadCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE receiver_id = :uid AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch()['count'];
    }

    // Get enquiries about a specific apartment (for landlords)
    public function getApartmentEnquiries($apartment_id) {
        $query = "SELECT m.*, 
                         CONCAT(s.firstname, ' ', s.lastname) as sender_name,
                         s.email as sender_email,
                         s.contact as sender_contact
                  FROM " . $this->table . " m
                  JOIN users s ON m.sender_id = s.id
                  WHERE m.apartment_id = :apt_id
                  ORDER BY m.date_created DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':apt_id', $apartment_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
