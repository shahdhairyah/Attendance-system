<!-- con_stu.php -->
<?php
$conn = new mysqli("localhost", "root", "", "attendance_system");


// Approve request
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $res = $conn->query("SELECT * FROM student_requests WHERE id = $id");
    if ($res->num_rows > 0) {
        $data = $res->fetch_assoc();
        $conn->query("INSERT INTO students (name, roll_number, email, password, phone, address) 
            VALUES ('{$data['name']}', '{$data['roll_number']}', '{$data['email']}', '{$data['password']}', '{$data['phone']}', '{$data['address']}')");
        $conn->query("DELETE FROM student_requests WHERE id = $id");
    }
    header("Location: con_stu.php");
    exit;
}

// Cancel request
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];
    $conn->query("DELETE FROM student_requests WHERE id = $id");
    header("Location: con_stu.php");
    exit;
}

// Fetch all requests
$result = $conn->query("SELECT * FROM student_requests");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Approve Student Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h2>Pending Student Requests</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Name</th><th>Roll</th><th>Email</th><th>Phone</th><th>Address</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['name'] ?></td>
          <td><?= $row['roll_number'] ?></td>
          <td><?= $row['email'] ?></td>
          <td><?= $row['phone'] ?></td>
          <td><?= $row['address'] ?></td>
          <td>
            <a href="?approve=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
            <a href="?cancel=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
