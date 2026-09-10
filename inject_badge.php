<?php
$dir = 'resources/views';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$edited = []; $skipHasBadge = []; $skipNoBody = []; $skipAlready = [];
foreach ($rii as $f) {
    if ($f->isDir()) continue;
    $path = str_replace('\','/',$f->getPathname());
    if (!str_ends_with($path, '.blade.php')) continue;
    if (str_contains($path, 'partials/user_badge')) continue; // ตัว partial เอง

    $html = file_get_contents($path);

    // มี badge ของตัวเองแล้ว -> ข้าม (กันซ้ำ)
    if (stripos($html, 'user-badge') !== false) { $skipHasBadge[] = $path; continue; }
    // ใส่ไปแล้ว -> ข้าม
    if (str_contains($html, "@include('partials.user_badge')")) { $skipAlready[] = $path; continue; }
    // ไม่มี <body> -> ข้าม (partial/sub-view)
    if (!preg_match('/<body\b[^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE)) { $skipNoBody[] = $path; continue; }

    $insertAt = $m[0][1] + strlen($m[0][0]);
    $new = substr($html, 0, $insertAt) . "\n@include('partials.user_badge')" . substr($html, $insertAt);
    file_put_contents($path, $new);
    $edited[] = $path;
}
echo "EDITED (".count($edited)."):\n"; foreach($edited as $p) echo "  $p\n";
echo "\nSKIP_HAS_OWN_BADGE (".count($skipHasBadge)."):\n"; foreach($skipHasBadge as $p) echo "  $p\n";
echo "\nSKIP_NO_BODY (".count($skipNoBody)."):\n"; foreach($skipNoBody as $p) echo "  $p\n";
