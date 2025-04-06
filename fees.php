<?php
header("Content-Type: application/json");
include 'db.php'; // Include your database connection file

// Check database connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Decode the JSON data sent from the frontend
$data = json_decode(file_get_contents("php://input"), true);

// Log the received data for debugging
error_log("Received data: " . json_encode($data));

// Validate required fields
if (empty($data["name"]) || empty($data["rollnumber"]) || empty($data["semesteryear"]) || 
    empty($data["studenttype"]) || empty($data["category"]) || 
    empty($data["totalfee"]) || empty($data["payment_id"]) || empty($data["branch"])) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Validate totalfee
if (!is_numeric($data["totalfee"])) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Total fee must be a numeric value."]);
    exit;
}

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO fee_submissions (name, rollnumber, semesteryear, studenttype, category, totalfee, payment_id, branch) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["success" => false, "message" => "Failed to prepare the statement: " . $conn->error]);
    exit;
}

// Bind parameters
$stmt->bind_param(
    "ssssssss",
    $data["name"],
    $data["rollnumber"],
    $data["semesteryear"],
    $data["studenttype"],
    $data["category"],
    $data["totalfee"],
    $data["payment_id"],
    $data["branch"]
    // Assuming branch is also part of the data sent from the frontend
);

// Execute the statement
if ($stmt->execute()) {
    http_response_code(200); // Success
    echo json_encode([
        "success" => true,
        "message" => "Data saved successfully.",
        "redirect_url" => "fee_receipt.php?rollnumber=" . urlencode($data["rollnumber"]) . "&payment_id=" . urlencode($data["payment_id"])
    ]);
} else {
    error_log("Database error: " . $stmt->error); // Log the error
    http_response_code(500); // Internal Server Error
    echo json_encode(["success" => false, "message" => "Failed to save data."]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>