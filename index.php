<?php
session_start(); // เริ่ม session เพื่อใช้ในการเก็บข้อมูลชั่วคราว
require_once 'config/db.php'; // เพิ่มการเชื่อมต่อฐานข้อมูล

// เตรียมคำสั่ง SQL เพื่อดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll();

if (!$users) {
  echo "<p><td colspan='5'>No users found</td></p>";
}
$count = 0; // ตัวนับสำหรับแสดงลำดับ

// ลบข้อมูลผู้ใช้ 
if (isset($_GET['delete'])) {
  $deleteId = $_GET['delete']; // รับค่า id ของผู้ใช้ที่ต้องการลบ
  $sqlDelete = $conn->prepare("DELETE FROM users WHERE id = $deleteId"); // เตรียมคำสั่ง SQL สำหรับลบข้อมูล
  $sqlDelete->execute(); // เริ่มทำการลบข้อมูล

  // ทำให้แจ้งเตือนเมื่อการลบข้อมูลสำเร็จ
  if ($sqlDelete) {
    echo "<script>alert('User deleted successfully.');</script>"; // แจ้งว่าการลบข้อมูลสำเร็จ
    $_SESSION['success'] = 'User deleted successfully.'; // ตั้ง session success เพื่อแจ้งว่าการลบข้อมูลสำเร็จ
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

</head>

<body>


  <!-- modal -->
  <div class="modal fade" id="idUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Add user</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- form -->
          <form action="insert.php" method="post" enctype="multipart/form-data">
            <!-- first name -->
            <div class="mb-3">
              <label for="firstname" class="col-form-label">First Name:</label>
              <input type="text" class="form-control" id="first-name" name="firstname" required>
            </div>
            <!-- last name -->
            <div class="mb-3">
              <label for="lastname" class="col-form-label">Last Name:</label>
              <input type="text" class="form-control" id="last-name" name="lastname" required>
            </div>
            <!-- position -->
            <div class="mb-3">
              <label for="position" class="col-form-label">Position:</label>
              <input type="text" class="form-control" id="position" name="position" required>
            </div>
            <!-- preview image / upload / file / img -->
            <div class="mb-3">
              <label for="img" class="col-form-label">Image:</label>
              <input type="file" class="form-control" id="imgInput" name="img" required>
              <img width="100%" id="previewImg" alt="">
            </div>
            <!-- button -->
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" name="submit" class="btn btn-success">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- add user modal -->
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-6 mb-3">
        <h1>CRUD Application</h1>
      </div>
      <div class="col-md-6 mb-3 d-flex justify-content-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#idUserModal">Add User</button>
      </div>
    </div>

    <!-- alert -->
    <hr>
    <?php if (isset($_SESSION['success'])) { ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php } ?>
    <?php if (isset($_SESSION['error'])) { ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php } ?>

    <!-- table -->
    <table class="table caption-top">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Firstname</th>
          <th scope="col">Lastname</th>
          <th scope="col">Position</th>
          <th scope="col">Img</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php // php foreach loop
        // วน loop เพื่อแสดงข้อมูลผู้ใช้
        foreach ($users as $user) {
        ?>
          <tr>
            <th scope="row"><?php echo ++$count; ?></th>
            <td><?php echo $user['firstname'] ?></td>
            <td><?php echo $user['lastname'] ?></td>
            <td><?php echo $user['position'] ?></td>
            <td><img src="uploads/<?php echo $user['img'] ?>" alt="" width="100"></td>
            <td>
              <!-- action buttons -->
              <!-- เมื่อกดปุ่ม Edit จะนำไปยังหน้า edit.php พร้อมกับส่งค่า id ของผู้ใช้ -->
              <a href="edit.php?id=<?php echo $user['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
              <!-- เมื่อกดปุ่ม Delete จะนำไปยังหน้า delete.php พร้อมกับส่งค่า id ของผู้ใช้ และ ขึ้นแจ้งเตือน -->
              <!-- รีบค่าจาก URL -->
              <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
            </td>
          </tr>
        <?php } ?>
        <!-- end loop -->
      </tbody>
    </table>

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