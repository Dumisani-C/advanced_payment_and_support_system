<?php
/**
 * Modern REST API Router
 * Handles all API requests with proper CORS and JSON responses
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include dependencies
require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'models/Payment.php';
require_once 'models/Apartment.php';
require_once 'models/Message.php';
require_once 'models/ApartmentPhoto.php';
require_once 'models/StripePayment.php';
require_once 'models/MobileMoneyPayment.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Get request info
$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_GET['request']) ? explode('/', trim($_GET['request'], '/')) : [];
$endpoint = $request[0] ?? '';
$id = $request[1] ?? null;

// Helper function for JSON response
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// Get POST data
function getPostData() {
    $json = file_get_contents('php://input');
    return json_decode($json, true) ?? $_POST;
}

$subaction = $request[2] ?? null;

try {
    switch ($endpoint) {
        case 'auth':
            handleAuth($db, $method, $id);
            break;
        case 'users':
        case 'tenants':
            handleUsers($db, $method, $id);
            break;
        case 'payments':
            handlePayments($db, $method, $id);
            break;
        case 'apartments':
            handleApartments($db, $method, $id, $subaction);
            break;
        case 'messages':
            handleMessages($db, $method, $id, $subaction);
            break;
        case 'photos':
            handlePhotos($db, $method, $id);
            break;
        case 'dashboard':
            handleDashboard($db);
            break;
        case 'categories':
            handleCategories($db, $method);
            break;
        case 'landlords':
            handleLandlords($db, $method, $id);
            break;
        case 'browse':
            handlePublicBrowse($db, $method, $id);
            break;
        case 'stripe':
            handleStripePayments($db, $method, $id);
            break;
        case 'webhook':
            handleStripeWebhook($db);
            break;
        case 'mobile-payment':
            handleMobilePayments($db, $method, $id);
            break;
        default:
            jsonResponse(['error' => 'Endpoint not found', 'available' => ['auth', 'users', 'tenants', 'payments', 'apartments', 'messages', 'photos', 'dashboard', 'categories', 'landlords', 'browse', 'stripe', 'mobile-payment']], 404);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

// Auth handlers
function handleAuth($db, $method, $action) {
    $user = new User($db);
    
    if ($method === 'POST' && $action === 'login') {
        $data = getPostData();
        $result = $user->login($data['email'] ?? '', $data['password'] ?? '');
        
        if ($result) {
            jsonResponse([
                'success' => true,
                'user' => $result,
                'token' => base64_encode(json_encode(['id' => $result['id'], 'type' => $result['user_type']]))
            ]);
        } else {
            jsonResponse(['success' => false, 'error' => 'Invalid credentials'], 401);
        }
    }
    
    // Register new user (tenant or landlord)
    if ($method === 'POST' && $action === 'register') {
        $data = getPostData();
        
        // Validate required fields
        if (empty($data['email']) || empty($data['password']) || empty($data['firstname']) || empty($data['lastname'])) {
            jsonResponse(['success' => false, 'error' => 'Missing required fields'], 400);
        }
        
        $user->firstname = $data['firstname'];
        $user->middlename = $data['middlename'] ?? '';
        $user->lastname = $data['lastname'];
        $user->email = $data['email'];
        $user->contact = $data['contact'] ?? '';
        $user->password = $data['password'];
        
        // user_type: 2 = tenant, 3 = landlord
        $userType = ($data['user_type'] ?? 'tenant') === 'landlord' ? '3' : '2';
        
        try {
            $result = $user->register($userType);
            if ($result) {
                jsonResponse([
                    'success' => true,
                    'user' => $result,
                    'token' => base64_encode(json_encode(['id' => $result['id'], 'type' => $result['user_type']]))
                ], 201);
            }
            jsonResponse(['success' => false, 'error' => 'Registration failed'], 500);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }
    
    jsonResponse(['error' => 'Invalid auth action. Use: login, register'], 400);
}

// User/Tenant handlers
function handleUsers($db, $method, $id) {
    $user = new User($db);
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $result = $user->getById($id);
                if ($result) {
                    jsonResponse($result);
                }
                jsonResponse(['error' => 'User not found'], 404);
            } else {
                $stmt = $user->getTenants();
                $tenants = $stmt->fetchAll();
                jsonResponse($tenants);
            }
            break;
            
        case 'POST':
            $data = getPostData();
            $user->firstname = $data['firstname'] ?? '';
            $user->middlename = $data['middlename'] ?? '';
            $user->lastname = $data['lastname'] ?? '';
            $user->email = $data['email'] ?? '';
            $user->contact = $data['contact'] ?? '';
            $user->password = $data['password'] ?? '12345678';
            
            $newId = $user->create();
            if ($newId) {
                jsonResponse(['success' => true, 'id' => $newId], 201);
            }
            jsonResponse(['error' => 'Failed to create user'], 500);
            break;
            
        case 'PUT':
            if (!$id) jsonResponse(['error' => 'ID required'], 400);
            $data = getPostData();
            $user->id = $id;
            $user->firstname = $data['firstname'] ?? '';
            $user->middlename = $data['middlename'] ?? '';
            $user->lastname = $data['lastname'] ?? '';
            $user->email = $data['email'] ?? '';
            $user->contact = $data['contact'] ?? '';
            
            if ($user->update()) {
                jsonResponse(['success' => true]);
            }
            jsonResponse(['error' => 'Failed to update user'], 500);
            break;
    }
}

// Payment handlers
function handlePayments($db, $method, $id) {
    $payment = new Payment($db);
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $result = $payment->getById($id);
                if ($result) {
                    jsonResponse($result);
                }
                jsonResponse(['error' => 'Payment not found'], 404);
            } else {
                // Check for tenant filter
                $tenant_id = $_GET['tenant_id'] ?? null;
                $month = $_GET['month'] ?? null;
                
                if ($tenant_id) {
                    $stmt = $payment->getByTenant($tenant_id);
                } elseif ($month) {
                    $stmt = $payment->getMonthlyReport($month);
                } else {
                    $stmt = $payment->getAll();
                }
                jsonResponse($stmt->fetchAll());
            }
            break;
            
        case 'POST':
            $data = getPostData();
            $payment->tenant_id = $data['tenant_id'] ?? 0;
            $payment->apartment_no = $data['apartment_no'] ?? '';
            $payment->amount = $data['amount'] ?? 0;
            $payment->invoice = $data['invoice'] ?? 'INV-' . time();
            $payment->date_created = $data['date_created'] ?? date('Y-m-d');
            
            $newId = $payment->create();
            if ($newId) {
                jsonResponse(['success' => true, 'id' => $newId], 201);
            }
            jsonResponse(['error' => 'Failed to create payment'], 500);
            break;
    }
}

// Apartment handlers
function handleApartments($db, $method, $id) {
    $apartment = new Apartment($db);
    
    switch ($method) {
        case 'GET':
            if ($id === 'available') {
                $stmt = $apartment->getAvailable();
                jsonResponse($stmt->fetchAll());
            } elseif ($id) {
                $result = $apartment->getById($id);
                if ($result) {
                    jsonResponse($result);
                }
                jsonResponse(['error' => 'Apartment not found'], 404);
            } else {
                $stmt = $apartment->getAll();
                jsonResponse($stmt->fetchAll());
            }
            break;
            
        case 'POST':
            $data = getPostData();
            $apartment->apartment_no = $data['apartment_no'] ?? '';
            $apartment->category_id = $data['category_id'] ?? 1;
            $apartment->description = $data['description'] ?? '';
            $apartment->price = $data['price'] ?? 0;
            
            $newId = $apartment->create();
            if ($newId) {
                jsonResponse(['success' => true, 'id' => $newId], 201);
            }
            jsonResponse(['error' => 'Failed to create apartment'], 500);
            break;
            
        case 'PUT':
            if (!$id) jsonResponse(['error' => 'ID required'], 400);
            $data = getPostData();
            $apartment->id = $id;
            $apartment->apartment_no = $data['apartment_no'] ?? '';
            $apartment->category_id = $data['category_id'] ?? 1;
            $apartment->description = $data['description'] ?? '';
            $apartment->price = $data['price'] ?? 0;
            
            if ($apartment->update()) {
                jsonResponse(['success' => true]);
            }
            jsonResponse(['error' => 'Failed to update apartment'], 500);
            break;
            
        case 'DELETE':
            if (!$id) jsonResponse(['error' => 'ID required'], 400);
            if ($apartment->delete($id)) {
                jsonResponse(['success' => true]);
            }
            jsonResponse(['error' => 'Failed to delete apartment'], 500);
            break;
    }
}

// Dashboard handler
function handleDashboard($db) {
    $user = new User($db);
    $stats = $user->getDashboardStats();
    jsonResponse($stats);
}

// Categories handler
function handleCategories($db, $method) {
    if ($method === 'GET') {
        $stmt = $db->query("SELECT * FROM categories ORDER BY name");
        jsonResponse($stmt->fetchAll());
    }
    jsonResponse(['error' => 'Method not allowed'], 405);
}

// Messages handler
function handleMessages($db, $method, $id, $subaction) {
    $message = new Message($db);
    
    switch ($method) {
        case 'GET':
            if ($id === 'conversations') {
                // Get all conversations for a user
                $user_id = $_GET['user_id'] ?? null;
                if (!$user_id) jsonResponse(['error' => 'user_id required'], 400);
                $stmt = $message->getConversations($user_id);
                jsonResponse($stmt->fetchAll());
            } elseif ($id === 'unread') {
                $user_id = $_GET['user_id'] ?? null;
                if (!$user_id) jsonResponse(['error' => 'user_id required'], 400);
                jsonResponse(['count' => $message->getUnreadCount($user_id)]);
            } elseif ($id && $subaction) {
                // Get conversation between two users
                $stmt = $message->getConversation($id, $subaction);
                jsonResponse($stmt->fetchAll());
            }
            jsonResponse(['error' => 'Invalid request'], 400);
            break;
            
        case 'POST':
            $data = getPostData();
            $message->sender_id = $data['sender_id'] ?? 0;
            $message->receiver_id = $data['receiver_id'] ?? 0;
            $message->apartment_id = $data['apartment_id'] ?? null;
            $message->message = $data['message'] ?? '';
            
            if (empty($message->message)) {
                jsonResponse(['error' => 'Message cannot be empty'], 400);
            }
            
            $newId = $message->send();
            if ($newId) {
                jsonResponse(['success' => true, 'id' => $newId], 201);
            }
            jsonResponse(['error' => 'Failed to send message'], 500);
            break;
            
        case 'PUT':
            // Mark messages as read
            $data = getPostData();
            if ($message->markAsRead($data['sender_id'], $data['receiver_id'])) {
                jsonResponse(['success' => true]);
            }
            jsonResponse(['error' => 'Failed to mark as read'], 500);
            break;
    }
}

// Photos handler
function handlePhotos($db, $method, $id) {
    $photo = new ApartmentPhoto($db);
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $photo->getByApartment($id);
                $photos = $stmt->fetchAll();
                // Add full URLs
                foreach ($photos as &$p) {
                    $p['url'] = '/api/uploads/apartments/' . $p['filename'];
                }
                jsonResponse($photos);
            }
            jsonResponse(['error' => 'Apartment ID required'], 400);
            break;
            
        case 'POST':
            if (!isset($_FILES['photo'])) {
                jsonResponse(['error' => 'No photo uploaded'], 400);
            }
            
            $photo->apartment_id = $_POST['apartment_id'] ?? 0;
            $photo->is_primary = $_POST['is_primary'] ?? 0;
            
            try {
                $newId = $photo->upload($_FILES['photo']);
                jsonResponse(['success' => true, 'id' => $newId], 201);
            } catch (Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 400);
            }
            break;
            
        case 'DELETE':
            if (!$id) jsonResponse(['error' => 'Photo ID required'], 400);
            if ($photo->delete($id)) {
                jsonResponse(['success' => true]);
            }
            jsonResponse(['error' => 'Failed to delete photo'], 500);
            break;
    }
}

// Landlords handler
function handleLandlords($db, $method, $id) {
    $user = new User($db);
    
    switch ($method) {
        case 'GET':
            if ($id === 'properties') {
                $landlord_id = $_GET['landlord_id'] ?? null;
                if (!$landlord_id) jsonResponse(['error' => 'landlord_id required'], 400);
                $stmt = $user->getLandlordProperties($landlord_id);
                jsonResponse($stmt->fetchAll());
            } elseif ($id === 'income') {
                $landlord_id = $_GET['landlord_id'] ?? null;
                if (!$landlord_id) jsonResponse(['error' => 'landlord_id required'], 400);
                jsonResponse($user->getLandlordIncome($landlord_id));
            } elseif ($id) {
                $result = $user->getById($id);
                if ($result && $result['user_type'] == '3') {
                    jsonResponse($result);
                }
                jsonResponse(['error' => 'Landlord not found'], 404);
            } else {
                $stmt = $user->getLandlords();
                jsonResponse($stmt->fetchAll());
            }
            break;
    }
}

// Stripe payment handlers
function handleStripePayments($db, $method, $id) {
    $stripe = new StripePayment($db);
    
    switch ($method) {
        case 'GET':
            if ($id === 'config') {
                // Return publishable key for frontend
                jsonResponse([
                    'publishableKey' => $stripe->getPublishableKey()
                ]);
            } elseif ($id === 'history') {
                // Get payment history for tenant
                $tenant_id = $_GET['tenant_id'] ?? null;
                if (!$tenant_id) jsonResponse(['error' => 'tenant_id required'], 400);
                $stmt = $stripe->getPaymentHistory($tenant_id);
                jsonResponse($stmt->fetchAll());
            }
            jsonResponse(['error' => 'Invalid request'], 400);
            break;
            
        case 'POST':
            $data = getPostData();
            
            if ($id === 'create-payment-intent') {
                // Create a new payment intent
                if (empty($data['amount']) || empty($data['tenant_id']) || empty($data['apartment_no'])) {
                    jsonResponse(['error' => 'Missing required fields: amount, tenant_id, apartment_no'], 400);
                }
                
                try {
                    $result = $stripe->createPaymentIntent(
                        $data['amount'],
                        $data['tenant_id'],
                        $data['apartment_no'],
                        $data['description'] ?? 'Rent Payment'
                    );
                    jsonResponse($result, 201);
                } catch (Exception $e) {
                    jsonResponse(['error' => $e->getMessage()], 400);
                }
            } elseif ($id === 'confirm') {
                // Confirm payment was successful
                if (empty($data['paymentIntentId'])) {
                    jsonResponse(['error' => 'paymentIntentId required'], 400);
                }
                
                try {
                    $result = $stripe->confirmPayment($data['paymentIntentId']);
                    jsonResponse($result);
                } catch (Exception $e) {
                    jsonResponse(['error' => $e->getMessage()], 400);
                }
            }
            
            jsonResponse(['error' => 'Invalid action'], 400);
            break;
    }
}

// Mobile Money payment handlers (Airtel Money, TNM Mpamba, Bank Transfer)
function handleMobilePayments($db, $method, $action) {
    $mobileMoney = new MobileMoneyPayment($db);
    
    switch ($method) {
        case 'GET':
            if ($action === 'banks') {
                // Get list of supported banks
                jsonResponse($mobileMoney->getSupportedBanks());
            } elseif ($action === 'status') {
                // Check payment status
                $reference = $_GET['reference'] ?? null;
                if (!$reference) jsonResponse(['error' => 'reference required'], 400);
                try {
                    $status = $mobileMoney->checkPaymentStatus($reference);
                    jsonResponse($status);
                } catch (Exception $e) {
                    jsonResponse(['error' => $e->getMessage()], 404);
                }
            }
            jsonResponse(['error' => 'Invalid request'], 400);
            break;
            
        case 'POST':
            $data = getPostData();
            
            if ($action === 'airtel') {
                // Initiate Airtel Money payment
                if (empty($data['amount']) || empty($data['phone']) || empty($data['tenant_id']) || empty($data['apartment_no'])) {
                    jsonResponse(['error' => 'Missing required fields: amount, phone, tenant_id, apartment_no'], 400);
                }
                try {
                    $result = $mobileMoney->initiateAirtelPayment(
                        $data['amount'],
                        $data['phone'],
                        $data['tenant_id'],
                        $data['apartment_no'],
                        $data['description'] ?? 'Rent Payment'
                    );
                    jsonResponse($result, 201);
                } catch (Exception $e) {
                    jsonResponse(['error' => $e->getMessage()], 400);
                }
            } elseif ($action === 'mpamba') {
                // Initiate TNM Mpamba payment
                if (empty($data['amount']) || empty($data['phone']) || empty($data['tenant_id']) || empty($data['apartment_no'])) {
                    jsonResponse(['error' => 'Missing required fields: amount, phone, tenant_id, apartment_no'], 400);
                }
                try {
                    $result = $mobileMoney->initiateMpambaPayment(
                        $data['amount'],
                        $data['phone'],
                        $data['tenant_id'],
                        $data['apartment_no'],
                        $data['description'] ?? 'Rent Payment'
                    );
                    jsonResponse($result, 201);
                } catch (Exception $e) {
                    jsonResponse(['error' => $e->getMessage()], 400);
                }
            } elseif ($action === 'bank-transfer') {
                // Initiate bank transfer
                if (empty($data['amount']) || empty($data['tenant_id']) || empty($data['apartment_no']) || empty($data['bank_code'])) {
                    jsonResponse(['error' => 'Missing required fields: amount, tenant_id, apartment_no, bank_code'], 400);
                }
                try {
                    $result = $mobileMoney->initiateBankTransfer(
                        $data['amount'],
                        $data['tenant_id'],
                        $data['apartment_no'],
                        $data['bank_code'],
                        $data['description'] ?? 'Rent Payment'
                    );
                    jsonResponse($result, 201);
                } catch (Exception $e) {
                    jsonResponse(['error' => $e->getMessage()], 400);
                }
            } elseif ($action === 'callback') {
                // Handle payment callback from mobile money providers
                $provider = $data['provider'] ?? $_GET['provider'] ?? null;
                if (!$provider) jsonResponse(['error' => 'provider required'], 400);
                try {
                    $result = $mobileMoney->handleCallback($provider, $data);
                    jsonResponse($result);
                } catch (Exception $e) {
                    jsonResponse(['error' => $e->getMessage()], 400);
                }
            } elseif ($action === 'confirm-bank') {
                // Admin confirms bank transfer
                if (empty($data['reference'])) {
                    jsonResponse(['error' => 'reference required'], 400);
                }
                try {
                    $result = $mobileMoney->confirmBankTransfer($data['reference'], $data['notes'] ?? '');
                    jsonResponse($result);
                } catch (Exception $e) {
                    jsonResponse(['error' => $e->getMessage()], 400);
                }
            }
            
            jsonResponse(['error' => 'Invalid action'], 400);
            break;
    }
}

// Stripe webhook handler
function handleStripeWebhook($db) {
    $stripe = new StripePayment($db);
    
    $payload = file_get_contents('php://input');
    $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
    
    try {
        $stripe->handleWebhook($payload, $sigHeader);
        jsonResponse(['received' => true]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}

// Public browse handler (no auth required)
function handlePublicBrowse($db, $method, $id) {
    $apartment = new Apartment($db);
    $photo = new ApartmentPhoto($db);
    
    if ($method !== 'GET') {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
    
    if ($id) {
        // Get single apartment details
        $result = $apartment->getByIdWithDetails($id);
        if ($result) {
            // Get photos
            $photoStmt = $photo->getByApartment($id);
            $photos = $photoStmt->fetchAll();
            foreach ($photos as &$p) {
                $p['url'] = '/api/uploads/apartments/' . $p['filename'];
            }
            $result['photos'] = $photos;
            jsonResponse($result);
        }
        jsonResponse(['error' => 'Apartment not found'], 404);
    } else {
        // Get all available apartments
        $stmt = $apartment->getAvailableWithDetails();
        $apartments = $stmt->fetchAll();
        // Add photo URLs
        foreach ($apartments as &$apt) {
            if ($apt['primary_photo']) {
                $apt['primary_photo_url'] = '/api/uploads/apartments/' . $apt['primary_photo'];
            }
        }
        jsonResponse($apartments);
    }
}
