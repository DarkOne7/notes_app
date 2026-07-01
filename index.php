    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include "./navBar.php";
    include "db.php";
    if (isset($_POST['submit'])) {
      $title = $_POST['title'];
      $desc = $_POST['desc'];
      $sql = "INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$desc')";
      $result = mysqli_query($conn, $sql);
      if ($result) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Your note has been added successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> There was an issue adding your note: ' . mysqli_error($conn) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
    }

    if (isset($_GET['deleted'])) {
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Success!</strong> Note deleted successfully.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    } elseif (isset($_GET['error'])) {
      $error = htmlspecialchars($_GET['error']);
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Error!</strong> ' . $error . '
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    ?>
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Notes App</title>
      <link rel="stylesheet" href="styles.css?v=2">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    </head>
    <body>
    <div class="container my-3">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <form method="POST">
            <div class="mb-3">
              <label for="title" class="form-label">title</label>
              <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title">
            </div>
            <div class="mb-3">
              <label for="desc" class="form-label">Description</label>
              <textarea class="form-control" name="desc" id="desc" placeholder="Enter Description"></textarea>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="exampleCheck1">
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Add Note</button>
          </form>
      </div>
    </div>
    <div class="container justify-content-center">
      <div class="row">
        <div class="col-lg-10">
          <h1> Your Notes</h1>
              <?php 
              $sql = "SELECT * FROM `notes`";
              $result = mysqli_query($conn, $sql);
              while ($row = mysqli_fetch_assoc($result)) {
              echo '<div class="card">
              <div class="card-body">
                <h5 class="card-title">' . $row['title'] . '</h5>
                <p class="card-text">' . $row['description'] . '</p>
                <a href="./edit.php?id=' . $row['id'] . '" class="btn btn-primary">Edit</a>
                <a href="./delete.php?id=' . $row['id'] . '" class="btn btn-danger">Delete</a>
              </div>
            </div>';
              }
              ?>
            
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
