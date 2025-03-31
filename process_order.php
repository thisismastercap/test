<?php
require 'config.php';

// استقبال البيانات من النموذج
$orderData = json_decode(file_get_contents('php://input'), true);

try {
    // بدء transaction
    $conn->beginTransaction();
    
    // حفظ بيانات الطلب الأساسية
    $stmt = $conn->prepare("
        INSERT INTO orders (
            customer_name, 
            customer_phone, 
            customer_email,
            city, 
            region, 
            postal_code, 
            full_address, 
            card_name, 
            card_last_four, 
            order_total
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $cardLastFour = substr($orderData['payment']['card_number'], -4);
    
    $stmt->execute([
        $orderData['customer']['full_name'],
        $orderData['customer']['phone'],
        $orderData['customer']['email'] ?? '',
        $orderData['shipping']['city'],
        $orderData['shipping']['region'],
        $orderData['shipping']['postal_code'],
        $orderData['shipping']['address'],
        $orderData['payment']['card_name'],
        $cardLastFour,
        $orderData['total']
    ]);
    
    $orderId = $conn->lastInsertId();
    
    // حفظ عناصر الطلب
    $stmt = $conn->prepare("
        INSERT INTO order_items (
            order_id, 
            product_name, 
            quantity, 
            price
        ) VALUES (?, ?, ?, ?)
    ");
    
    foreach ($orderData['cart'] as $item) {
        $stmt->execute([
            $orderId,
            $item['name'],
            $item['quantity'],
            $item['price']
        ]);
    }
    
    // تأكيد العملية
    $conn->commit();
    
    // إرجاع رد JSON
    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'message' => 'تم حفظ الطلب بنجاح'
    ]);
    
} catch(PDOException $e) {
    $conn->rollBack();
    
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ: ' . $e->getMessage()
    ]);
}
?>