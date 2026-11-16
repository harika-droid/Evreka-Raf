<?php
require __DIR__ . '/db.php';
$table = $_GET['t'] ?? '';
$cols = collections();
$byTable = [];
foreach ($cols as $k => $v) $byTable[$v[0]] = $v[1];
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$error = null; $summary = []; $rows = []; $heads = [];
try {
    if ($table !== '' && isset($byTable[$table])) {
        $heads = array_map(fn($c) => trim($c, '`'), $byTable[$table]);
        $q = implode(',', array_map(fn($c) => $c === 'read' ? '`read`' : $c, $heads));
        $rows = db()->query("SELECT $q FROM $table LIMIT 50")->fetchAll(PDO::FETCH_NUM);
    } else {
        foreach ($byTable as $t => $_) $summary[$t] = (int) db()->query("SELECT COUNT(*) AS n FROM $t")->fetch()['n'];
    }
} catch (Throwable $e) { $error = $e->getMessage(); }
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Veritabanı — Evreka Raf</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand navbar-dark" style="background:#2d5b4b">
  <div class="container">
    <a class="navbar-brand" href="index.html">Evreka Raf</a>
    <div class="navbar-nav ms-auto"><a class="nav-link" href="index.html">Siteye dön</a><a class="nav-link" href="veritabani.php">Veritabanı</a></div>
  </div>
</nav>
<main class="container py-4">
<?php if ($error): ?>
  <div class="alert alert-danger"><b>Veritabanı hatası:</b> <?= h($error) ?><br>WampServer'da MySQL'in çalıştığını (W simgesi yeşil), portu (3306 / 3307) ve <code>config.php</code> ayarlarını kontrol edin.</div>
<?php elseif ($table !== '' && isset($byTable[$table])): ?>
  <a href="veritabani.php" class="btn btn-outline-secondary btn-sm mb-3">&larr; Tablolar</a>
  <h1 class="h4"><?= h($table) ?> <small class="text-muted">(ilk 50 kayıt)</small></h1>
  <div class="table-responsive"><table class="table table-striped table-sm bg-white">
    <thead><tr><?php foreach ($heads as $c): ?><th><?= h($c) ?></th><?php endforeach; ?></tr></thead>
    <tbody><?php if (!$rows): ?><tr><td colspan="<?= count($heads) ?>">Kayıt yok</td></tr><?php endif; ?>
    <?php foreach ($rows as $r): ?><tr><?php foreach ($r as $v): ?><td><?= h($v) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
  </table></div>
<?php else: ?>
  <h1 class="h3 mb-3">MySQL veritabanı tabloları</h1>
  <p class="text-muted">Siteden yapılan her işlem bu tablolara yazılır. Önce siteyi bir kez açın (tablolar dolsun).</p>
  <div class="row g-3">
  <?php foreach ($summary as $t => $n): ?>
    <div class="col-6 col-md-4 col-lg-3"><a class="card h-100 text-decoration-none" href="?t=<?= h($t) ?>">
      <div class="card-body"><div class="small text-muted"><?= h($t) ?></div><div class="display-6"><?= $n ?></div><div class="small">kayıt</div></div></a></div>
  <?php endforeach; ?>
  </div>
<?php endif; ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
