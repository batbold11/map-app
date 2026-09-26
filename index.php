<?php
// Аппликейшны нэвтрэх цэг. Одоогоор энд серверийн тусгай логик хэрэггүй тул
// зөвхөн HTML render хийнэ. Цаашид сессион/DB нэмэх бол энд оруулна.
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Хаягийн интерактив газрын зураг</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<!-- Leaflet.markercluster CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">

<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-3">
  <button class="btn btn-outline-light btn-sm d-lg-none me-2" type="button"
          data-bs-toggle="offcanvas" data-bs-target="#sidebarPanel" aria-controls="sidebarPanel">
    <i class="bi bi-list"></i>
  </button>
  <span class="navbar-brand mb-0 h1 flex-grow-1"><i class="bi bi-geo-alt-fill"></i> Хаягийн интерактив газрын зураг</span>
  <a href="admin.php" class="btn btn-outline-light btn-sm"><i class="bi bi-database"></i> Өгөгдөл удирдах</a>
</nav>

<div class="container-fluid py-3">
  <div class="row g-3">

    <!-- Зүүн панель: Upload + Хайлт (гар утсан дээр off-canvas гулсдаг цэс) -->
    <div class="offcanvas-lg offcanvas-start col-lg-3" tabindex="-1" id="sidebarPanel" aria-labelledby="sidebarPanelLabel">
      <div class="offcanvas-header d-lg-none">
        <h5 class="offcanvas-title" id="sidebarPanelLabel">Цэс</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarPanel" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body d-block p-lg-0">
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-database"></i> MySQL-с ачаалах</h6>
          <p class="form-text mb-2">Өгөгдлөө <a href="admin.php">Admin</a> хуудсаар нэмсэн бол энд дарж газрын зурагт шинэчилж болно.</p>
          <button id="reloadDbBtn" class="btn btn-outline-primary btn-sm w-100">
            <i class="bi bi-arrow-clockwise"></i> Дахин ачаалах
          </button>
        </div>
      </div>

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-file-earmark-spreadsheet"></i> Эсвэл Excel/CSV файл оруулах</h6>
          <input type="file" id="fileInput" class="form-control" accept=".xlsx,.xls,.csv">
          <div class="form-text">
            Баганууд: <b>Нэр</b>, <b>uilchilgeeni baiguulga1</b>, <b>uilchilgeeni baiguulga2</b>
            (заавал биш: Утас)
          </div>
          <div id="uploadStatus" class="mt-2 small"></div>
          <div class="progress mt-2 d-none" id="progressWrap" style="height:6px;">
            <div class="progress-bar" id="progressBar" style="width:0%"></div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-search"></i> Нэр эсвэл утасны дугаараар хайх</h6>
          <div class="position-relative">
            <input type="text" id="searchInput" class="form-control" placeholder="Нэр эсвэл утасны дугаар бичих..." autocomplete="off" disabled>
            <div id="searchResults" class="list-group position-absolute w-100 shadow-sm" style="z-index:1000; max-height:260px; overflow-y:auto;"></div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3 mb-1">
            <span class="small text-muted">Бүх хүмүүс</span>
            <button id="showAllBtn" class="btn btn-sm btn-outline-secondary py-0 px-2">
              <i class="bi bi-arrows-fullscreen"></i> Бүгдийг харах
            </button>
          </div>
          <div id="peopleList" class="list-group" style="max-height:320px; overflow-y:auto;"></div>
        </div>
      </div>

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="card-title">Тэмдэглэгээ</h6>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center">
              <img src="https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-green.png" style="width:18px;margin-right:8px;">
              <span>Байгууллага 1</span>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" id="toggleOrg1" checked>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center">
              <img src="https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-blue.png" style="width:18px;margin-right:8px;">
              <span>Байгууллага 2</span>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" id="toggleOrg2" checked>
            </div>
          </div>
          <div id="statsBox" class="small text-muted mt-2"></div>
        </div>
      </div>

      <div class="card shadow-sm" id="routeCard" style="display:none;">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-signpost-split"></i> Чиглэл</h6>
          <p class="form-text mb-2">Сонгосон хүний Байгууллага 1 ба Байгууллага 2 хоёрын хоорондох замыг харуулах.</p>
          <button id="showRouteBtn" class="btn btn-outline-primary btn-sm w-100">
            <i class="bi bi-signpost-2"></i> Замыг харуулах
          </button>
          <button id="clearRouteBtn" class="btn btn-outline-secondary btn-sm w-100 mt-2 d-none">
            <i class="bi bi-x-circle"></i> Замыг арилгах
          </button>
          <div id="routeInfo" class="small text-muted mt-2"></div>
        </div>
      </div>
      </div>
    </div>

    <!-- Газрын зураг -->
    <div class="col-12 col-lg-9">
      <div id="map" class="shadow-sm rounded"></div>
    </div>

  </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Leaflet.markercluster JS -->
<script src="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<!-- SheetJS (Excel/CSV parser) -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<!-- Bootstrap bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/app.js"></script>
</body>
</html>
