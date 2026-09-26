<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$editRow = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM people WHERE id = :id');
    $stmt->execute([':id' => (int) $_GET['edit']]);
    $editRow = $stmt->fetch();
}

// ---------- Хуудаслалт (pagination) ----------
$perPage = 20;
$page = max(1, (int) ($_GET['page'] ?? 1));
$totalCount = (int) $pdo->query('SELECT COUNT(*) FROM people')->fetchColumn();
$totalPages = max(1, (int) ceil($totalCount / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare('SELECT * FROM people ORDER BY id DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$people = $stmt->fetchAll();

$messages = [
    'added'            => ['success', 'Амжилттай нэмэгдлээ.'],
    'updated'          => ['success', 'Амжилттай шинэчлэгдлээ.'],
    'deleted'          => ['success', 'Устгагдлаа.'],
    'error_no_name'    => ['danger',  'Нэр заавал бөглөнө үү.'],
    'error_duplicate'  => ['danger',  'Ийм нэртэй хүн аль хэдийн бүртгэлтэй байна.'],
    'error_phone'      => ['danger',  'Утасны дугаар буруу форматтай (8 оронтой тоо байх ёстой).'],
];
$msgKey = $_GET['msg'] ?? null;
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Өгөгдөл удирдах (Admin)</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
  <span class="navbar-brand mb-0 h1"><i class="bi bi-database"></i> Өгөгдөл удирдах (MySQL)</span>
  <div class="d-flex align-items-center gap-2">
    <span class="text-white-50 small d-none d-sm-inline"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['admin_user'] ?? '') ?></span>
    <a href="index.php" class="btn btn-outline-light btn-sm"><i class="bi bi-map"></i> Газрын зураг руу</a>
    <a href="api/export.php" class="btn btn-outline-light btn-sm"><i class="bi bi-file-earmark-excel"></i> Excel export</a>
    <a href="api/logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Гарах</a>
  </div>
</nav>

<div class="container py-4">

  <?php if ($msgKey && isset($messages[$msgKey])): [$type, $text] = $messages[$msgKey]; ?>
    <div class="alert alert-<?= $type ?>"><?= htmlspecialchars($text) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title"><?= $editRow ? 'Мэдээлэл засах' : 'Шинэ хүн нэмэх' ?></h5>
      <form method="post" action="api/save-person.php" class="row g-3">
        <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'insert' ?>">
        <?php if ($editRow): ?>
          <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
        <?php endif; ?>

        <div class="col-md-6">
          <label class="form-label">Нэр *</label>
          <input type="text" name="name" class="form-control" required
                 value="<?= htmlspecialchars($editRow['name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Утас</label>
          <input type="text" name="phone" class="form-control" pattern="[0-9]{8}" maxlength="8"
                 placeholder="8 оронтой тоо, ж: 99001122" title="Утасны дугаар 8 оронтой тоо байх ёстой"
                 value="<?= htmlspecialchars($editRow['phone'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Байгууллага 1 хаяг</label>
          <input type="text" name="org1_address" class="form-control"
                 value="<?= htmlspecialchars($editRow['org1_address'] ?? '') ?>">
          <div class="row g-1 mt-1">
            <div class="col-6">
              <input type="text" name="org1_lat_manual" class="form-control form-control-sm" placeholder="Lat (гараар, заавал биш)"
                     value="<?= $editRow && ($editRow['org1_precision'] ?? '') === 'manual' ? htmlspecialchars($editRow['org1_lat']) : '' ?>">
            </div>
            <div class="col-6">
              <input type="text" name="org1_lng_manual" class="form-control form-control-sm" placeholder="Lng (гараар, заавал биш)"
                     value="<?= $editRow && ($editRow['org1_precision'] ?? '') === 'manual' ? htmlspecialchars($editRow['org1_lng']) : '' ?>">
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Байгууллага 2 хаяг</label>
          <input type="text" name="org2_address" class="form-control"
                 value="<?= htmlspecialchars($editRow['org2_address'] ?? '') ?>">
          <div class="row g-1 mt-1">
            <div class="col-6">
              <input type="text" name="org2_lat_manual" class="form-control form-control-sm" placeholder="Lat (гараар, заавал биш)"
                     value="<?= $editRow && ($editRow['org2_precision'] ?? '') === 'manual' ? htmlspecialchars($editRow['org2_lat']) : '' ?>">
            </div>
            <div class="col-6">
              <input type="text" name="org2_lng_manual" class="form-control form-control-sm" placeholder="Lng (гараар, заавал биш)"
                     value="<?= $editRow && ($editRow['org2_precision'] ?? '') === 'manual' ? htmlspecialchars($editRow['org2_lng']) : '' ?>">
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="alert alert-info small mb-0">
            <i class="bi bi-info-circle"></i> Байрны дугаар зэрэг нарийн хаяг автоматаар олдохгүй бол:
            Google Maps дээр яг байрлалыг олоод, тэр цэг дээрээ <b>хулганы баруун товч дараад координатыг хуулж</b>
            (жишээ: <code>47.912345, 106.912345</code> — эхнийх нь Lat, хоёр дахь нь Lng), дээрх Lat/Lng талбаруудад тус тусад нь бичээд хадгалахад
            яг тэр цэг дээр маркер тогтмол харагдана (дараа дахин geocode хийхгүй).
          </div>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2"></i> <?= $editRow ? 'Хадгалах' : 'Нэмэх' ?>
          </button>
          <?php if ($editRow): ?>
            <a href="admin.php" class="btn btn-outline-secondary">Цуцлах</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title"><i class="bi bi-file-earmark-arrow-up"></i> Excel/CSV-с шууд MySQL руу оруулах (Bulk import)</h5>
      <p class="form-text mb-2">
        Баганууд: <b>Нэр</b>, <b>uilchilgeeni baiguulga1</b>, <b>uilchilgeeni baiguulga2</b> (заавал биш: Утас).
        Оруулсны дараа хаягууд газрын зураг хуудсанд анх ачаалахад автоматаар geocode хийгдэнэ.
        Давхардсан нэр байвал алгасна.
      </p>
      <input type="file" id="bulkImportInput" class="form-control" accept=".xlsx,.xls,.csv">
      <div id="bulkImportStatus" class="mt-2 small"></div>
      <div class="progress mt-2 d-none" id="bulkImportProgressWrap" style="height:6px;">
        <div class="progress-bar" id="bulkImportProgressBar" style="width:0%"></div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title">Бүх өгөгдөл (нийт <?= $totalCount ?>)</h5>
      <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
          <thead>
            <tr>
              <th>Нэр</th><th>Утас</th><th>Байгууллага 1</th><th>Байгууллага 2</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($people as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['phone'] ?? '') ?></td>
                <td class="small"><?= htmlspecialchars($p['org1_address'] ?? '') ?>
                  <?php if ($p['org1_lat']): ?>
                    <?php if (($p['org1_precision'] ?? '') === 'manual'): ?>
                      <span class="badge bg-primary ms-1">Гараар</span>
                    <?php elseif (($p['org1_precision'] ?? 'exact') !== 'exact'): ?>
                      <span class="badge bg-warning text-dark ms-1">Ойролцоо (<?= htmlspecialchars($p['org1_precision']) ?>)</span>
                    <?php else: ?>
                      <span class="badge bg-success ms-1">OK</span>
                    <?php endif; ?>
                  <?php elseif ($p['org1_address']): ?>
                    <span class="badge bg-secondary ms-1">geocode хийгдээгүй</span>
                  <?php endif; ?>
                </td>
                <td class="small"><?= htmlspecialchars($p['org2_address'] ?? '') ?>
                  <?php if ($p['org2_lat']): ?>
                    <?php if (($p['org2_precision'] ?? '') === 'manual'): ?>
                      <span class="badge bg-primary ms-1">Гараар</span>
                    <?php elseif (($p['org2_precision'] ?? 'exact') !== 'exact'): ?>
                      <span class="badge bg-warning text-dark ms-1">Ойролцоо (<?= htmlspecialchars($p['org2_precision']) ?>)</span>
                    <?php else: ?>
                      <span class="badge bg-success ms-1">OK</span>
                    <?php endif; ?>
                  <?php elseif ($p['org2_address']): ?>
                    <span class="badge bg-secondary ms-1">geocode хийгдээгүй</span>
                  <?php endif; ?>
                </td>
                <td class="text-nowrap">
                  <a href="admin.php?edit=<?= (int) $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                  <form method="post" action="api/save-person.php" class="d-inline" onsubmit="return confirm('Устгах уу?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalPages > 1): ?>
        <nav aria-label="Хуудаслалт">
          <ul class="pagination pagination-sm justify-content-center mb-0 mt-2">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- SheetJS (Excel/CSV parser) — bulk import-д хэрэглэнэ -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="assets/js/admin.js"></script>
</body>
</html>
