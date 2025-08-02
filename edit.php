<?php
// edit.php
session_start(); // เริ่ม session เพื่อใช้ในการเก็บข้อมูลแจ้งเตือน
require_once('config/db.php'); // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล


// เช็คว่ามีการส่งค่า id มาหรือไม่ หรือ id เป็นค่าว่าง
if (isset($_GET['id']) == false || $_GET['id'] == ''){
  header('location: index.php'); // ถ้าไม่มี id ให้กลับไปที่หน้า index.php
  $_SESSION['error'] = "User ID not provided."; // ตั้ง session error เพื่อแจ้งว่าผู้ใช้ไม่พบ
  exit();
}
// ดึงข้อมูลผู้ใช้ตาม ID ที่ส่งมาจาก index.php
$id = $_GET['id']; // รับค่า id จาก URL
$sql = $conn->prepare("SELECT * FROM users WHERE id = :id"); // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลผู้ใช้ตาม ID
$sql->bindParam(':id', $id); // bind ค่าตัวแปรกับ parameter ใน SQL query bindParam ใช้สำหรับ bind ค่าตัวแปรกับ parameter ใน SQL query
$sql->execute(); // เริ่มทำการดึงข้อมูล
$data = $sql->fetch(); // ดึงข้อมูลมาเก็บในตัวแปร data


// เมื่อกดปุ่ม Update จะทำการอัพเดทข้อมูลผู้ใช้
if (isset($_POST['update'])) {
  $id = $_POST['id']; // รับค่า ID จากฟอร์ม
  $firstName = $_POST['firstname']; // รับค่า First Name จากฟอร์ม
  $lastName = $_POST['lastname']; // รับค่า Last Name จากฟอร์ม
  $position = $_POST['position']; // รับค่า Position จากฟอร์ม
  $img = $_FILES['img']; // รับค่าไฟล์รูปภาพจากฟอร์ม

  $oldImg = $_POST['old_img']; // เก็บค่า รูปภาพเก่าที่ได้รับมากจากฐานข้อมูล เมื่อไม่ต้องการอัพเดทรูปภาพใหม่
  $upload = $_FILES['img']['name']; // เก็บชื่อไฟล์รูปภาพที่อัพโหลดใหม่

  // ถ้ารูปภาพไม่เป็นค่าว่าง 
  if ($upload != "") {
    // Handle file upload
    $allow = array('jpg', 'jpeg', 'png', 'gif'); // ยอมรับนามสกุลไฟล์ jpg, jpeg, png, gif
    $extenstion = explode('.', $img['name']); // แยกนามสกุลไฟล์
    $fileActualExt = strtolower(end($extenstion)); // รับนามสกุลไฟล์ เป็นตัวพิมพ์เล็ก
    $fileNameNew = rand() . '.' . $fileActualExt;  // ตั้งชื่อไฟล์ใหม่ ไม่ให้ซ้ำกัน
    $filePath = 'uploads/' . $fileNameNew; // กำหนด path ที่จะเก็บไฟล์

    if (in_array($fileActualExt, $allow)) {
      // ถ้าไฟล์มีขนาดมากกว่า 0 และไม่มี error
      if ($img['size'] > 0 && $img['error'] == 0) {
        move_uploaded_file($img['tmp_name'], $filePath); // ย้ายไฟล์ไปยัง path ที่กำหนด
      }
    }
  } else {
    // ถ้าไม่มีการอัพโหลดไฟล์ใหม่ ให้ใช้ไฟล์เก่าที่มีอยู่
    $fileNameNew = $oldImg; // ใช้ไฟล์เก่าที่มีอยู่
  }

  // เมื่อผ่านการตรวจสอบไฟล์ ภาพแล้ว ใหทำการอัพเดทข้อมูลในฐานข้อมูล
  $sql = $conn->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, position = :position, img = :img WHERE id = :id");
  $sql->bindParam(':firstname', $firstName); // bind ค่าตัวแปรกับ parameter ใน SQL query
  $sql->bindParam(':lastname', $lastName); // bind ค่าตัวแปรกับ parameter ใน SQL query
  $sql->bindParam(':position', $position); // bind ค่าตัวแปรกับ parameter ใน SQL query
  $sql->bindParam(':img', $fileNameNew); // bind ค่าตัวแปรกับ parameter ใน SQL query
  $sql->bindParam(':id', $id); // bind ค่าตัวแปรกับ parameter ใน SQL query
  $sql->execute(); // เริ่มทำการอัพเดทข้อมูล

  // เก็บข้อมูลลงใน session เพื่อแจ้งว่าการอัพเดทข้อมูลสำเร็จ
  if ($sql) {
    $_SESSION['success'] = 'User updated successfully.'; // ตั้ง session success เพื่อแจ้งว่าการอัพเดทข้อมูลสำเร็จ
    header('location: index.php'); // กลับไปที่หน้า index.php
  } else {
    $_SESSION['error'] = 'Failed to update user.'; // ตั้ง session error เพื่อแจ้งว่าการอัพเดทข้อมูลไม่สำเร็จ
    header('location: index.php'); // กลับไปที่หน้า index.php
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>

  <!-- bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

  <style>
    .container {
      max-width: 600px;
      margin: auto;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h1>Edit User</h1>
    <?php
    // ถ้าไม่มีข้อมูลผู้ใช้ที่ตรงกับ ID ที่ส่งมา ให้แสดงข้อความแจ้งเตือน
    if (isset($data['id']) == false || $data['id'] == '') {
      header('refresh:0; location: index.php'); // กลับไปที่หน้า index.php
      $_SESSION['error'] = "User not found."; // ตั้ง session error เพื่อแจ้งว่าผู้ใช้ไม่พบ
      exit();
    }
    ?>
    <hr>
    <!-- เริ่มดึงข้อมูลตาม ID -->
    <!-- form -->
    <form action="edit.php" method="post" enctype="multipart/form-data">
      <!-- first name -->
      <div class="mb-3">
        <label for="id" class="col-form-label">ID:</label>
        <!-- ใส่ค่า ID ที่ได้จากฐานข้อมูล -->
        <input type="text" value="<?php echo $data['id']; ?>" class="form-control" id="id" name="id" readonly>
        <label for="firstname" class="col-form-label">First Name:</label>
        <!-- ใส่ค่า First Name ที่ได้จากฐานข้อมูล -->
        <input type="text" class="form-control" id="first-name" name="firstname" value="<?php echo $data['firstname']; ?>" required>
        <!-- เก็บค่า รูปภาพเก่าที่ได้รับมากจากฐานข้อมูล เมื่อไม่ต้องการอัพเดทรูปภาพใหม่-->
        <input type="hidden" name="old_img" value="<?php echo $data['img']; ?>">
      </div>
      <!-- last name -->
      <div class="mb-3">
        <label for="lastname" class="col-form-label">Last Name:</label>
        <input type="text" class="form-control" id="last-name" name="lastname" value="<?php echo $data['lastname']; ?>" required>
      </div>
      <!-- position -->
      <div class="mb-3">
        <label for="position" class="col-form-label">Position:</label>
        <input type="text" class="form-control" id="position" name="position" value="<?php echo $data['position']; ?>" required>
      </div>
      <!-- preview image / upload / file / img -->
      <div class="mb-3">
        <label for="img" class="col-form-label">Image:</label>
        <input type="file" class="form-control" id="imgInput" name="img">
        <!-- preview รูปภาพเก่า และใหม่ -->
        <img width="100%" id="previewImg" src="uploads/<?php echo $data['img']; ?>" alt="">
      </div>
      <!-- button -->
      <a href="index.php" class="btn btn-secondary">Go Back</a>
      <button type="submit" name="update" class="btn btn-success">Update</button>
  </div>
  </form>
  </div>

  <!-- bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous"></script>

  <!-- preview image -->
  <script>
    let imgInput = document.getElementById('imgInput');
    let previewImg = document.getElementById('previewImg');

    imgInput.onchange = evt => {
      const [file] = imgInput.files;
      if (file) {
        previewImg.src = URL.createObjectURL(file);
      }
    }
  </script>
</body>

</html>