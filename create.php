<?php
require_once "Helpers/headers.php";
send_json_api_headers('POST');

require_once "Config/db.php";
require_once "Helpers/response.php";

$input = json_decode(file_get_contents("php://input"));

if (!isset($input->name) || !isset($input->email)) {
    response(400, "خطأ: يجب إرسال الاسم والإيميل");
}

try {
    // تم تعديل اسم العمود ليطابق قاعدة البيانات لديك (created_timestamp)
    $sql = "INSERT INTO students (name, email, created_timestamp) VALUES (:name, :email, NOW())";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':name' => $input->name,
        ':email' => $input->email
    ]);

    response(200, "تمت إضافة الطالب بنجاح");

} catch (PDOException $e) {
    response(500, "خطأ في قاعدة البيانات: " . $e->getMessage());
}
?>