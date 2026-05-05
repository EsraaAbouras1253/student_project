<?php
require_once "Helpers/headers.php";
send_json_api_headers('POST');

require_once "Config/db.php"; 
require_once "Helpers/response.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    response(405, "Only POST Method is allowed");
    exit; 
}

//  استقبال البيانات بصيغة JSON
$input = json_decode(file_get_contents("php://input"));

try {
    
    if(!isset($input->name) || !isset($input->email)){
        response(400, "Bad request: name and email are missing");
        exit;
    }

    $name = $input->name;
    $email = $input->email;

    // 4. منع تكرار الإيميل (طلب الأستاذ الثالث)
    $check_query = "SELECT id FROM students WHERE email = :email";
    $check_stmnt = $pdo->prepare($check_query);
    $check_stmnt->bindParam(':email', $email, PDO::PARAM_STR);
    $check_stmnt->execute();
    
    if ($check_stmnt->fetch()) {
        response(400, "هذا الإيميل مستخدم مسبقاً!");
        exit;
    }

    
    $create_query = "INSERT INTO `students` (`name`, `email`, `created_timestamp`) VALUES (:name, :email, NOW())";
    $create_stmnt = $pdo->prepare($create_query);
    
    $create_stmnt->bindParam(':name', $name, PDO::PARAM_STR);
    $create_stmnt->bindParam(':email', $email, PDO::PARAM_STR);

    if($create_stmnt->execute()){
        response(201, "Student created successfully.");
    } else {
        response(503, "Unable to create the student");
    }
}
catch (PDOException $e) {
    response(500, "server error: " . $e->getMessage());
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}
?>