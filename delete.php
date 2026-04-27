<?php
require_once __DIR__ . "/Config/db.php";
require_once "Helpers/headers.php";
send_json_api_headers('DELETE');

// تضمين ملف الاتصال:
require_once __DIR__ . "/Config/db.php";
require_once "Helpers/response.php";

// رفض أي طلب نوع اتصاله ليس DELETE
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    response(405, "Only DELETE Method is allowed");
}

// استقبال البيانات من جسم الطلب من نوع json
// وتحويله إلى مصفوفة ترابطية
$input = json_decode(file_get_contents("php://input"));

try {
    // التأكد أن رقم الطالب موجود:
    if(!isset($input->id)){
        response(400, "Bad request: 'id' is required");
    }
    // سحب الـ id من جسم الطلب
    $id = $input->id;

    // التأكد أن القيمة رقمية
    if(!is_numeric($id)){
        response(400, "Bad request: id must be integer only.");
    }

    $select_query = "SELECT `id` FROM `students` WHERE `id` = :id";
    $check = $pdo->prepare($select_query);
    $check->bindParam(':id', $id, PDO::PARAM_INT);
    $check->execute();

    if($check->rowCount() === 0){
        response(404, "Bad request: Student not found!");
    }

    // حذف الطالب
    $delete_query = "DELETE FROM `students` WHERE `id` = :id";
    $delete_stmnt = $pdo->prepare($delete_query);
    $delete_stmnt->bindParam(':id', $id, PDO::PARAM_INT);
    
    // تنفيذ الحذف والتحقق من النتيجة
    $delete_stmnt->execute();
    
    if($delete_stmnt->rowCount() > 0){
        response(200, "Student deleted successfully.");
    } else {
        response(503, "Unable to delete the student");
    }
}
catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
} catch (PDOException $e) {
    response(500, "server error: " . $e->getMessage());
}
?>