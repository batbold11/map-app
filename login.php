<?php
require_once __DIR__ . '/config/auth.php';

if (isLoggedIn()) {
    header('Location: admin.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        // Session fixation-с сэргийлж session id-г шинэчилнэ.
        session_regenerate_id(true);
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_user'] = $username;
        header('Location: admin.php');
        exit;
    }

    $error = 'Нэвтрэх нэр эсвэл нууц үг буруу байна.';
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Нэвтрэх — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
<div class="container" style="max-width:380px;">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h5 class="card-title text-center mb-3"><i class="bi bi-shield-lock"></i> Admin нэвтрэх</h5>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label class="form-label">Нэвтрэх нэр</label>
          <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Нууц үг</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-box-arrow-in-right"></i> Нэвтрэх
        </button>
      </form>

      <div class="text-center mt-3">
        <a href="index.php" class="small text-muted">&larr; Газрын зураг руу буцах</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
