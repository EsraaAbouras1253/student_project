<?php
// استخدام المسارات الصحيحة للتعامل مع الملفات
require_once __DIR__ . "/Helpers/headers.php";
require_once __DIR__ . "/Config/db.php"; // تأكدي أن هذا هو اسم ملف الاتصال
require_once __DIR__ . "/Helpers/response.php";

send_json_api_headers('POST');

// رفض أي طلب نوع اتصاله ليس POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    response(405, "Only POST Method is allowed");
}

// استقبال البيانات
$input = json_decode(file_get_contents("php://input"));

try {
    // التحقق من وجود الحقول الضرورية
    if (!isset($input->id) || !isset($input->name) || !isset($input->email)) {
        response(400, "Bad request: 'id', 'name', and 'email' are required");
    }

    $id = $input->id;
    $name = $input->name;
    $email = $input->email;

    // التحقق من أن ID رقمي
    if (!is_numeric($id)) {
        response(400, "Bad request: id must be numeric.");
    }

    // التحقق من وجود الطالب في قاعدة البيانات
    $select_query = "SELECT `id` FROM `students` WHERE `id` = :id";
    $check = $pdo->prepare($select_query);
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() === 0) {
        response(404, "Bad request: Student not found!");
    }

    // تحديث البيانات في جدول students
    $update_query = "UPDATE `students` SET `name` = :name, `email` = :email WHERE `id` = :id";
    $update_stmnt = $pdo->prepare($update_query);
    $update_stmnt->bindParam(":id", $id, PDO::PARAM_INT);
    $update_stmnt->bindParam(":name", $name, PDO::PARAM_STR);
    $update_stmnt->bindParam(":email", $email, PDO::PARAM_STR);

    if ($update_stmnt->execute()) {
        response(200, "Student updated successfully.");
    } else {
        response(503, "Unable to update the student");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}
?>