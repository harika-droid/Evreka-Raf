<?php
$CFG = require __DIR__ . '/config.php';

function collections(): array {
    $g = fn($x, $k) => $x[$k] ?? null;
    return [
        'users' => ['users', ['id','email','kadi','ad','role','status','il','ilce','created'],
            fn($x) => [$g($x,'id'),$g($x,'email'),$g($x,'kadi'),$g($x,'ad'),$g($x,'role'),$g($x,'status'),$g($x,'il'),$g($x,'ilce'),$g($x,'created')]],
        'books' => ['books', ['id','owner_id','ad','yazar','kat','tur','il','status','created'],
            fn($x) => [$g($x,'id'),$g($x,'ownerId'),$g($x,'ad'),$g($x,'yazar'),$g($x,'kat'),$g($x,'tur'),$g($x,'il'),$g($x,'status'),$g($x,'created')]],
        'requests' => ['requests', ['id','book_id','from_id','to_id','status','created'],
            fn($x) => [$g($x,'id'),$g($x,'bookId'),$g($x,'fromId'),$g($x,'toId'),$g($x,'status'),$g($x,'created')]],
        'messages' => ['messages', ['id','req_id','from_id','t'],
            fn($x) => [$g($x,'id'),$g($x,'reqId'),$g($x,'fromId'),$g($x,'t')]],
        'reviews' => ['reviews', ['id','status','puan','created'],
            fn($x) => [$g($x,'id'),$g($x,'status'),$g($x,'puan'),$g($x,'created')]],
        'contacts' => ['contacts', ['id','created'], fn($x) => [$g($x,'id'),$g($x,'created')]],
        'notifs' => ['notifs', ['id','uid','`read`','t'],
            fn($x) => [$g($x,'id'),$g($x,'uid'),!empty($x['read']) ? 1 : 0,$g($x,'t')]],
        'wishes' => ['wishes', ['id','uid','ad','created'],
            fn($x) => [$g($x,'id'),$g($x,'uid'),$g($x,'ad'),$g($x,'created')]],
        'ratings' => ['ratings', ['id','req_id','from_id','to_id','stars','t'],
            fn($x) => [$g($x,'id'),$g($x,'reqId'),$g($x,'fromId'),$g($x,'toId'),$g($x,'stars'),$g($x,'t')]],
        'reports' => ['reports', ['id','from_id','status','t'],
            fn($x) => [$g($x,'id'),$g($x,'fromId'),$g($x,'status'),$g($x,'t')]],
        'clubs' => ['clubs', ['id','ad','il','kat','owner_id','created'],
            fn($x) => [$g($x,'id'),$g($x,'ad'),$g($x,'il'),$g($x,'kat'),$g($x,'ownerId'),$g($x,'created')]],
        'announcements' => ['announcements', ['id','hedef','t'],
            fn($x) => [$g($x,'id'),$g($x,'hedef'),$g($x,'t')]],
    ];
}

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    global $CFG;
    $opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    if ($CFG['type'] === 'sqlite') {
        $pdo = new PDO('sqlite:' . $CFG['sqlite_file'], null, null, $opt);
        $pdo->exec(file_get_contents(__DIR__ . '/sql/schema_sqlite.sql'));
        return $pdo;
    }

    $srv = new PDO("mysql:host={$CFG['host']};port={$CFG['port']};charset=utf8mb4", $CFG['user'], $CFG['pass'], $opt);
    $srv->exec("CREATE DATABASE IF NOT EXISTS `{$CFG['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci");
    $pdo = new PDO("mysql:host={$CFG['host']};port={$CFG['port']};dbname={$CFG['name']};charset=utf8mb4", $CFG['user'], $CFG['pass'], $opt);
    $sql = file_get_contents(__DIR__ . '/sql/schema_mysql.sql');
    $sql = preg_replace('/^\s*(CREATE DATABASE|USE)\b.*?;\s*$/mi', '', $sql);
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $st) $pdo->exec($st);
    return $pdo;
}

function is_empty(): bool {
    return (int) db()->query('SELECT COUNT(*) AS n FROM users')->fetch()['n'] === 0;
}

function read_all(): array {
    $out = [];
    foreach (collections() as $k => [$table]) {
        $out[$k] = array_map(fn($r) => json_decode($r['data'], true), db()->query("SELECT data FROM $table")->fetchAll());
    }
    foreach (db()->query('SELECT k, v FROM meta')->fetchAll() as $r) $out[$r['k']] = json_decode($r['v'], true);
    return $out;
}

function write_all(array $state): void {
    global $CFG;
    $pdo = db();
    $pdo->beginTransaction();
    try {
        foreach (collections() as $k => [$table, $cols, $fn]) {
            $rows = array_values(array_filter($state[$k] ?? [], fn($x) => is_array($x) && !empty($x['id'])));
            $ph = implode(',', array_fill(0, count($cols) + 1, '?'));
            if ($CFG['type'] === 'sqlite') {
                $sql = "INSERT OR REPLACE INTO $table (" . implode(',', $cols) . ",data) VALUES ($ph)";
            } else {
                $upd = implode(',', array_map(fn($c) => "$c=VALUES($c)", array_merge(array_slice($cols, 1), ['data'])));
                $sql = "INSERT INTO $table (" . implode(',', $cols) . ",data) VALUES ($ph) ON DUPLICATE KEY UPDATE $upd";
            }
            $st = $pdo->prepare($sql);
            $ids = [];
            foreach ($rows as $x) {
                $st->execute(array_merge($fn($x), [json_encode($x, JSON_UNESCAPED_UNICODE)]));
                $ids[] = $x['id'];
            }
            if ($ids) {
                $pdo->prepare("DELETE FROM $table WHERE id NOT IN (" . implode(',', array_fill(0, count($ids), '?')) . ")")->execute($ids);
            } else {
                $pdo->exec("DELETE FROM $table");
            }
        }
        $metaSql = $CFG['type'] === 'sqlite'
            ? 'INSERT OR REPLACE INTO meta (k,v) VALUES (?,?)'
            : 'INSERT INTO meta (k,v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)';
        $m = $pdo->prepare($metaSql);
        foreach ($state as $k => $v) if (!isset(collections()[$k])) $m->execute([$k, json_encode($v)]);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}
