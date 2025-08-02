<?php
// insert.php
require_once('config/db.php'); // Include your database connection file
session_start();

if (isset($_POST['submit'])) { // เช็คว่ามีการ submit form หรือไม่
  $firstName = $_POST['firstname'];
  $lastName = $_POST['lastname'];
  $position = $_POST['position'];
  $img = $_FILES['img'];

  // Handle file upload
  $allow = array('jpg', 'jpeg', 'png', 'gif'); // ยอมรับนามสกุลไฟล์ jpg, jpeg, png, gif
  $extenstion = explode('.', $img['name']); // แยกนามสกุลไฟล์
  $fileActualExt = strtolower(end($extenstion)); // รับนามสกุลไฟล์ เป็นตัวพิมพ์เล็ก
  $fileNameNew = rand() . '.' . $fileActualExt;  // ตั้งชื่อไฟล์ใหม่ ไม่ให้ซ้ำกัน
  $filePath = 'uploads/' . $fileNameNew; // กำหนด path ที่จะเก็บไฟล์

  if (in_array($fileActualExt, $allow)) {
    if ($img['size'] > 0 && $img['error'] == 0) { // เช็คว่าขนาดไฟล์ไม่เป็น 0 และไม่มี error

      if (move_uploaded_file($img['tmp_name'], $filePath)) { // ย้ายไฟล์ไปยัง path ที่กำหนด
        // ทำการเพิ่มข้อมูลลงฐานข้อมูล
        $sql = $conn->prepare("INSERT INTO users (firstname, lastname, position, img) VALUES (:firstname, :lastname, :position, :img)");
        $sql->bindParam(':firstname', $firstName); // bindParam ใช้สำหรับ bind ค่าตัวแปรกับ parameter ใน SQL query SQL query คือคำสั่ง SQL ที่ใช้ในการดึงข้อมูลจากฐานข้อมูล
        $sql->bindParam(':lastname', $lastName); // bindParam ใช้สำหรับ bind ค่าตัวแปรกับ parameter ใน SQL query 
        $sql->bindParam(':position', $position); // bindParam ใช้สำหรับ bind ค่าตัวแปรกับ parameter ใน SQL query
        $sql->bindParam(':img', $fileNameNew); // bindParam ใช้สำหรับ bind ค่าตัวแปรกับ parameter ใน SQL query
        $sql->execute(); // เริ่มทำการ เพิ่มข้อมูล

        // ถ้าเพิ่มข้อมูลสำเร็จ
        if ($sql) {
          $_SESSION['success'] = 'User added successfully.'; // ตั้ง session success เพื่อแจ้งว่าการเพิ่มข้อมูลสำเร็จ 
          header('location: index.php'); // กลับไปที่หน้า index.php
        } else {
          $_SESSION['error'] = 'Failed to add user.'; // ตั้ง session error เพื่อแจ้งว่าการเพิ่มข้อมูลไม่สำเร็จ
          header('location: index.php'); // กลับไปที่หน้า index.php
        }
      }
    }
  }
}
?>
