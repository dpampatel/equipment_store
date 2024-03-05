<?php

require('fpdf184/fpdf.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = 1;
    $cart_id = 1;

    require('db_conn.php');

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION["user_id"], $_GET["cart_id"])) {
        $user_id = $_SESSION["user_id"];
        $cart_id = $_GET["cart_id"];
    }

    $userQuery = "SELECT * FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($userQuery);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        die('User not found');
    }

    $firstName = $userData['first_name'];
    $lastName = $userData['last_name'];
    $address = $userData['address'];
    $city = $userData['city'];
    $email = $userData['email'];
    $date = date('Y-m-d ');
    $receiptId = uniqid();

    $customerData = "$firstName $lastName\n$address\n$city\nEmail: $email";

    $cartItemsQuery = "SELECT tools.tool_name, tools.price, cart_items.quantity, tools.image_name
    FROM cart_items 
    INNER JOIN tools ON cart_items.tool_id = tools.tool_id 
    INNER JOIN carts ON cart_items.cart_id = carts.cart_id
    WHERE carts.user_id = :user_id AND carts.cart_id = :cart_id";

    $cartStmt = $pdo->prepare($cartItemsQuery);
    $cartStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $cartStmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
    $cartStmt->execute();
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$cartItems) {
        echo $cart_id;
        die('No items in the cart');
    }

    $orderData = [];
    $subtotal = 0;
    $totalAmount = 0;
    $count = 1;
    foreach ($cartItems as $item) {
        $itemName = $item['tool_name'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $subTotal = $quantity * $price;

        $tax = $subTotal * 0.13;

        $amount = $subTotal + $tax;

        $orderData[] = [
            '0' => $count++,
            '1' => $itemName,
            '2' => $quantity,
            '3' => '$' . number_format($subTotal, 2),
            '4' => '$' . number_format($tax, 2),
            '5' => '$' . number_format($amount, 2),
        ];

        $totalAmount += $amount;
    }

    class PDF extends FPDF
    {
        private $companyDetails;

        const COMPANY_NAME = 'Committers';
        const STREET = '108 University Ave, ON N2J 2W2';
        const CITY = 'Waterloo';
        const PROVINCE = 'Ontario';
        const EMAIL = 'committers@Committers.com';
        const PHONE = '+1234567890';

        const ORDER_DATA = [
            ['001', 'Product A', 2, '$100.00', '$15.00', '$115.00'],
            ['002', 'Product B', 1, '$50.00', '$7.50', '$57.50'],
            ['003', 'Product C', 3, '$150.00', '$22.50', '$172.50'],
        ];

        function __construct()
        {
            parent::__construct();

            $this->companyDetails = [
                'companyName' => self::COMPANY_NAME,
                'street' => self::STREET,
                'city' => self::CITY,
                'province' => self::PROVINCE,
                'email' => self::EMAIL,
                'phone' => self::PHONE,
            ];
        }

        public function setCompanyDetails($companyDetails)
        {
            $this->companyDetails = $companyDetails;
        }

        private function addCompanyHeader()
        {
            $this->Image('imgs/logo/logo.png', 160, 10, 40);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(80, 10, $this->companyDetails['companyName'], 0, 1, 'L');
            $this->SetFont('Arial', '', 10);
            $this->MultiCell(80, 10, $this->companyDetails['street'], 0, 'L');
            $this->MultiCell(80, 10, $this->companyDetails['city'] . ', ' . $this->companyDetails['province'], 0, 'L');
            $this->MultiCell(80, 10, 'Email: ' . $this->companyDetails['email'], 0, 'L');
            $this->Ln(10);
        }

        public function addCustomerInfo($customerData, $date, $receiptId)
        {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(80, 10, 'Customer Details', 0, 0, 'L');
            $this->Cell(0);
            $this->Cell(0, 10, "Date: $date", 0, 1, 'R');
            $this->SetFont('Arial', '', 10);
            $this->MultiCell(80, 10, $customerData, 0, 'L');
            $this->Cell(80);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 10, "Receipt ID: $receiptId", 0, 1, 'L');
            $this->Ln(10);
        }

        public function addOrderDetailsTable($orderData, $total)
        {
            $this->SetFont('Arial', 'B', 12);
            $this->SetFillColor(128, 191, 255);
            $this->Cell(30, 10, 'Order No.', 1, 0, 'C', true);
            $this->Cell(60, 10, 'Tool', 1, 0, 'C', true);
            $this->Cell(20, 10, 'Quantity', 1, 0, 'C', true);
            $this->Cell(25, 10, 'Sub Total', 1, 0, 'C', true);
            $this->Cell(20, 10, 'Tax', 1, 0, 'C', true);
            $this->Cell(35, 10, 'Amount', 1, 1, 'C', true);

            $this->SetFont('Arial', '', 10);
            foreach ($orderData as $row) {
                $this->Cell(30, 10, $row[0], 1);
                $this->Cell(60, 10, $row[1], 1);
                $this->Cell(20, 10, $row[2], 1);
                $this->Cell(25, 10, $row[3], 1);
                $this->Cell(20, 10, $row[4], 1);
                $this->Cell(35, 10, $row[5], 1, 1);
            }

            $this->Cell(135);
            $this->Cell(20, 10, 'Total', 1, 0, 'C', true);
            $this->Cell(35, 10, '$' . number_format($total, 2), 1, 1);
        }

        private function addCompanyFooter()
        {
            $this->SetY(-41);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
            $this->Ln();
            $this->Cell(0, 10, 'Copyright @ ' . date('Y') . ' Committers. All rights reserved.', 0, 0, 'C');
        }

        public function generatePDF($customerData, $date, $receiptId, $orderData, $subtotal, $totalAmount)
        {
            $this->AddPage();
            $this->addCompanyHeader();
            $this->addCustomerInfo($customerData, $date, $receiptId);
            $this->addOrderDetailsTable($orderData, $totalAmount);
            $this->addCompanyFooter();
            header('Content-Disposition: attachment; filename="receipt.pdf"');

            $this->Output();
        }
    }

    $pdf = new PDF();


    $pdf->generatePDF($customerData, $date, $receiptId, $orderData, $subtotal, $totalAmount);
}
