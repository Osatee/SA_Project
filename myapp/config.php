<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sa";

// ฟังก์ชันสำหรับการเชื่อมต่อฐานข้อมูล
function getDBConnection() {
    global $host, $user, $password, $dbname;
    
    try {
        // เชื่อมต่อฐานข้อมูล
        $conn = new PDO("mysql:host={$host};dbname={$dbname}", $user, $password);
        // ตั้งค่าให้แสดงข้อผิดพลาด (error) กรณีมีปัญหา
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        // แสดงข้อความหากเชื่อมต่อไม่สำเร็จ
        echo "Connection failed: " . $e->getMessage();
        return null; // คืนค่า null หากเชื่อมต่อไม่สำเร็จ
    }
}
?>
