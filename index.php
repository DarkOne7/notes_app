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
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();
    include "./navBar.php";
    include "db.php";

    if (!isset($_SESSION['user_id'])) {
      header('Location: login.php');
      exit;
    }

    $user_id = $_SESSION['user_id'];
    $username = htmlspecialchars($_SESSION['username'] ?? '');
    $alertHtml = '';

    if (isset($_POST['submit'])) {
      $title = trim($_POST['title'] ?? '');
      $desc = trim($_POST['desc'] ?? '');

      if ($title === '' || $desc === '') {
        $alertHtml = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Warning!</strong> Title and description are required.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
      } else {
        $sql = "INSERT INTO `notes` (`title`, `description`, `user_id`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
          $stmt->bind_param('ssi', $title, $desc, $user_id);
          $result = $stmt->execute();

          if ($result) {
            $alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> Your note has been added successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
          } else {
            $alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> There was an issue adding your note: ' . htmlspecialchars($stmt->error) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
          }
          $stmt->close();
        } else {
          $alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                          <strong>Error!</strong> Database error: ' . htmlspecialchars($conn->error) . '
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        }
      }
    }

    if (isset($_GET['deleted'])) {
      $alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <strong>Success!</strong> Note deleted successfully.
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    } elseif (isset($_GET['error'])) {
      $error = htmlspecialchars($_GET['error']);
      $alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>Error!</strong> ' . $error . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    }
    ?>
    <?php echo $alertHtml; ?>
    <div class="container my-5">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card note-form mb-4">
            <div class="card-body">
              <h2 class="h4 mb-4 text-primary">Create a new note</h2>
              <form method="POST">
                <div class="mb-3">
                  <label for="title" class="form-label">Title</label>
                  <input type="text" class="form-control" name="title" id="title" placeholder="Enter title">
                </div>
                <div class="mb-3">
                  <label for="desc" class="form-label">Description</label>
                  <textarea class="form-control" name="desc" id="desc" placeholder="Enter description"></textarea>
                </div>
                <div class="mb-3 form-check">
                </div>
                <button type="submit" class="btn btn-primary" name="submit">Add Note</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container my-4">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4">
            <h1 class="h3 mb-2 mb-md-0">Your Notes</h1>
          </div>
          <?php 
          $sql = "SELECT id, title, description FROM `notes` WHERE user_id = ? ORDER BY id DESC";
          $stmt = $conn->prepare($sql);
          if ($stmt) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
              echo '<div class="row row-cols-1 row-cols-md-2 g-4">';
              while ($row = $result->fetch_assoc()) {
                echo '<div class="col">
                        <div class="card h-100 note-card">
                          <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-3">' . htmlspecialchars($row['title']) . '</h5>
                            <p class="card-text text-secondary flex-grow-1">' . nl2br(htmlspecialchars($row['description'])) . '</p>
                            <div class="mt-4">
                              <a href="./edit.php?id=' . $row['id'] . '" class="btn btn-sm btn-primary me-2">Edit</a>
                              <a href="./delete.php?id=' . $row['id'] . '" class="btn btn-sm btn-danger">Delete</a>
                            </div>
                          </div>
                        </div>
                      </div>';
              }
              echo '</div>';
            } else {
              echo '<div class="alert alert-info">No notes yet. Add your first note using the form above.</div>';
            }
            $stmt->close();
          }
          ?>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
