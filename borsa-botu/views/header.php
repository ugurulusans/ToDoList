<!doctype html>
<html lang="tr" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Borsa Analiz ve Sinyal Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-link.active {
            font-weight: bold;
        }
        .table-sm th, .table-sm td {
            padding: 0.4rem;
        }
        .positive { color: #198754; }
        .negative { color: #dc3545; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?page=dashboard">📊 Borsa Botu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page === 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page === 'watchlist') ? 'active' : ''; ?>" href="index.php?page=watchlist">Takip Listesi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page === 'portfolio') ? 'active' : ''; ?>" href="index.php?page=portfolio">Cüzdanım</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                 <li class="nav-item">
                    <a class="nav-link <?php echo ($page === 'settings') ? 'active' : ''; ?>" href="index.php?page=settings">Ayarlar</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
