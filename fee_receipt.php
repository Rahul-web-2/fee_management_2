<?php
include 'db.php'; // Include database connection
require('fpdf186/fpdf.php'); // Include the FPDF library

// Get rollnumber and payment_id from the GET request
$rollnumber = isset($_GET['rollnumber']) ? trim($_GET['rollnumber']) : '';
$payment_id = isset($_GET['payment_id']) ? trim($_GET['payment_id']) : '';

// Log the received parameters for debugging
error_log("Roll Number: " . $rollnumber);
error_log("Payment ID: " . $payment_id);

// Validate input parameters
if (empty($rollnumber) || empty($payment_id)) {
    die("Error: Missing roll number or payment ID in the URL.");
}

// Fetch student and payment details from the database
$query = "
    SELECT 
        name,  
        rollnumber,
        branch, 
        semesteryear, 
        studenttype, 
        category, 
        totalfee, 
        payment_id 
    FROM 
        fee_submissions 
    WHERE 
        rollnumber = ? AND payment_id = ?
";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error: Failed to prepare the SQL statement.");
}

$stmt->bind_param("ss", $rollnumber, $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Check if the record exists
if (!$student) {
    die("Error: No record found for the provided roll number and payment ID.");
}

// Debugging: Log the fetched student details
error_log("Fetched Student Data: " . json_encode($student));

// Generate the PDF receipt
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Add receipt title
$pdf->Cell(0, 10, 'Fee Receipt', 0, 1, 'C');
$pdf->Ln(10);

// Add student details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Student Name: ' . htmlspecialchars($student['name']), 0, 1);
$pdf->Cell(0, 10, 'Roll Number: ' . htmlspecialchars($student['rollnumber']), 0, 1);
$pdf->Cell(0, 10, 'Branch: ' . htmlspecialchars($student['branch']), 0, 1); // Ensure branch is included
$pdf->Cell(0, 10, 'Semester & Year: ' . htmlspecialchars($student['semesteryear']), 0, 1);
$pdf->Cell(0, 10, 'Student Type: ' . htmlspecialchars($student['studenttype']), 0, 1);
$pdf->Cell(0, 10, 'Category: ' . htmlspecialchars($student['category']), 0, 1);
$pdf->Ln(10);

// Add payment details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Payment Details:', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Payment ID: ' . htmlspecialchars($student['payment_id']), 0, 1);
$pdf->Cell(0, 10, 'Total Fee Paid: ₹' . htmlspecialchars($student['totalfee']), 0, 1);

// Output the PDF as a downloadable file
$filename = 'Fee_Receipt_' . htmlspecialchars($student['rollnumber']) . '_' . date('YmdHis') . '.pdf';
$pdf->Output('D', $filename); // 'D' forces download

// Close the database connection
$conn->close();
?>