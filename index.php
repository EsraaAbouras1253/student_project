<?php
require_once "Helpers/headers.php";
send_json_api_headers('POST');
require_once "Config/db.php"; 
require_once "Helpers/response.php";

// رفض أي طلب نوع اتصاله ليس GET
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    response(405, "Only GET Method is allowed");
}

try {
    $email = $_POST['email']; // استلام الإيميل من الطلب
$checkQuery = "SELECT id FROM students WHERE email = ?";
$checkSql = $pdo->prepare($checkQuery);
$checkSql->execute([$email]);
if ($checkSql->fetch()) {
    response(400, "هذا الإيميل موجود مسبقاً، يرجى استخدام إيميل آخر");
}
    // تعديل الاستعلام ليناسب جدول الطلاب
    $query = "SELECT * FROM `students`"; 
    $sql = $pdo->prepare($query);
    $sql->execute();
    $data = $sql->fetchAll();

    if (count($data) > 0) {
        response(200, "Students retrieved successfully", [
            "count" => count($data),
            "data" => $data,
        ]);
    } else {
        response(404, "No Students Found", [
            "status" => "success",
            "data" => [],
        ]);
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
} catch (PDOException $e) {
    response(500, "server error: " . $e->getMessage());
}
?>