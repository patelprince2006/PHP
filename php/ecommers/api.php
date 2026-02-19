<?php
// api.php - Backend API for Nutra_leaf E-commerce

header('Content-Type: application/json');
// This line allows your frontend (e.g., running on localhost:3000) to talk to the backend
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// --- 1. Database Configuration (CRUCIAL: UPDATE THESE VALUES) ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // CHANGE THIS to your MySQL username
define('DB_PASS', '');     // CHANGE THIS to your MySQL password
define('DB_NAME', 'nutra_leaf_db');

// --- 2. Database Connection Function ---
function getDbConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        // Log error and return failure to client
        error_log("Database Connection Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['isOk' => false, 'error' => 'Database connection failed.']);
        exit();
    }
}

// --- 3. Helper Functions ---
function getRequestBody() {
    // Reads raw JSON data sent from JavaScript's fetch body
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

function respond($isOk, $data = null, $error = null, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode(['isOk' => $isOk, 'data' => $data, 'error' => $error]);
    exit();
}

// --- 4. Request Handling ---
$method = $_SERVER['REQUEST_METHOD'];
$db = getDbConnection();
$requestData = getRequestBody();

// Handle preflight OPTIONS request (CORS)
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$action = $requestData['action'] ?? $_GET['action'] ?? null;
$recordType = $requestData['record_type'] ?? null;

if (!$action) {
    respond(false, null, 'Missing action parameter.', 400);
}

if ($action === 'create') {
    if ($recordType === 'user') {
        // --- CREATE USER (Registration) ---
        if (empty($requestData['email']) || empty($requestData['password'])) {
            respond(false, null, 'Email and password are required.', 400);
        }

        $sql = "INSERT INTO users (user_id, name, email, phone, password, created_at)
                VALUES (:user_id, :name, :email, :phone, :password, :created_at)";
        $stmt = $db->prepare($sql);

        try {
            $stmt->execute([
                ':user_id' => $requestData['user_id'],
                ':name' => $requestData['name'] ?? '',
                ':email' => $requestData['email'],
                ':phone' => $requestData['phone'] ?? '',
                ':password' => $requestData['password'], // Note: Storing plain text to match JS logic
                ':created_at' => $requestData['created_at']
            ]);
            respond(true, ['__backendId' => $db->lastInsertId()]);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Duplicate entry error code
                respond(false, null, 'Email already registered.', 409);
            }
            error_log("User Creation Error: " . $e->getMessage());
            respond(false, null, 'Failed to save user data.', 500);
        }

    } elseif ($recordType === 'order') {
        // --- CREATE ORDER ---
        $requiredFields = ['order_id', 'customer_email', 'product_name', 'total_amount'];
        foreach ($requiredFields as $field) {
            if (empty($requestData[$field])) {
                respond(false, null, "$field is required for the order.", 400);
            }
        }

        $sql = "INSERT INTO orders (order_id, customer_name, customer_email, customer_phone, address, city, state, pincode, product_name, quantity, total_amount, order_date, status)
                VALUES (:order_id, :customer_name, :customer_email, :customer_phone, :address, :city, :state, :pincode, :product_name, :quantity, :total_amount, :order_date, :status)";
        $stmt = $db->prepare($sql);

        try {
            $stmt->execute([
                ':order_id' => $requestData['order_id'],
                ':customer_name' => $requestData['customer_name'] ?? '',
                ':customer_email' => $requestData['customer_email'],
                ':customer_phone' => $requestData['customer_phone'] ?? '',
                ':address' => $requestData['address'] ?? '',
                ':city' => $requestData['city'] ?? '',
                ':state' => $requestData['state'] ?? '',
                ':pincode' => $requestData['pincode'] ?? '',
                ':product_name' => $requestData['product_name'],
                ':quantity' => $requestData['quantity'] ?? 1,
                ':total_amount' => $requestData['total_amount'],
                ':order_date' => $requestData['order_date'],
                ':status' => $requestData['status'] ?? 'Confirmed'
            ]);
            respond(true, ['__backendId' => $db->lastInsertId()]);
        } catch (PDOException $e) {
            error_log("Order Creation Error: " . $e->getMessage());
            respond(false, null, 'Failed to save order data.', 500);
        }

    } else {
        respond(false, null, 'Invalid record_type for creation.', 400);
    }

} elseif ($action === 'update') {
    // --- UPDATE ORDER STATUS (Used in Admin Dashboard) ---
    if ($recordType === 'order') {
        $backendId = $requestData['__backendId'] ?? null;
        $newStatus = $requestData['status'] ?? null;

        if (!$backendId || !$newStatus) {
            respond(false, null, 'Missing __backendId or status for update.', 400);
        }

        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $db->prepare($sql);

        try {
            $stmt->execute([
                ':status' => $newStatus,
                ':id' => $backendId
            ]);

            if ($stmt->rowCount() > 0) {
                respond(true);
            } else {
                respond(false, null, 'Order not found or status already set.', 404);
            }
        } catch (PDOException $e) {
            error_log("Order Update Error: " . $e->getMessage());
            respond(false, null, 'Failed to update order status.', 500);
        }
    } else {
        respond(false, null, 'Invalid record_type for update.', 400);
    }

} elseif ($action === 'read_all') {
    // --- READ ALL DATA (Used for JS onDataChanged and Admin Dashboard) ---
    $allData = [];

    // Fetch all users
    // Note: Password is included for frontend client-side login fallback, 
    // but should be excluded in a secure production API.
    $stmtUsers = $db->query("SELECT id AS __backendId, 'user' AS record_type, user_id, name, email, phone, password, created_at FROM users");
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
    $allData = array_merge($allData, $users);

    // Fetch all orders
    $stmtOrders = $db->query("SELECT id AS __backendId, 'order' AS record_type, order_id, customer_name, customer_email, customer_phone, address, city, state, pincode, product_name, quantity, total_amount, order_date, status FROM orders ORDER BY order_date DESC");
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
    $allData = array_merge($allData, $orders);

    respond(true, $allData);

} elseif ($action === 'login') {
    // --- LOGIN (Dedicated API endpoint) ---
    $email = $requestData['email'] ?? null;
    $password = $requestData['password'] ?? null;

    if (!$email || !$password) {
        respond(false, null, 'Email and password are required for login.', 400);
    }

    $stmt = $db->prepare("SELECT id AS __backendId, user_id, name, email, phone, password FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // NOTE: This comparison is for simplicity and matching the frontend logic (plain text password).
    // In production, use password_verify($password, $user['password_hash']).
    if ($user && $user['password'] === $password) { 
        unset($user['password']); // Securely remove password before sending response
        respond(true, $user);
    } else {
        respond(false, null, 'Invalid email or password.', 401);
    }
} else {
    respond(false, null, 'Unknown or invalid API action.', 400);
}
?>