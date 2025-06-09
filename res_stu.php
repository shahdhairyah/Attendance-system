<!-- res_stu.php -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "attendance_db");

    $name = $_POST['name'];
    $roll = $_POST['roll'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "INSERT INTO student_requests (name, roll_number, email, password, phone, address) 
            VALUES ('$name', '$roll', '$email', '$pass', '$phone', '$address')";
    if ($conn->query($sql)) {
        $msg = "<div class='alert alert-success'>Request has been sent!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Student Request Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h2>Student Request Form</h2>
    <?= isset($msg) ? $msg : "" ?>
    <form method="POST">
      <input class="form-control my-2" type="text" name="name" placeholder="Name" required>
      <input class="form-control my-2" type="text" name="roll" placeholder="Roll Number" required>
      <input class="form-control my-2" type="email" name="email" placeholder="Email" required>
      <input class="form-control my-2" type="password" name="password" placeholder="Password" required>
      <input class="form-control my-2" type="text" name="phone" placeholder="Phone Number" required>
      <textarea class="form-control my-2" name="address" placeholder="Address" required></textarea>
      <button class="btn btn-primary" type="submit">Send Request</button>
    </form>
  </div>
</body>
</html>
