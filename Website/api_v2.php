<?php
include 'db.php';
$st = $conn->query("SELECT * FROM quiz_status WHERE id=1")->fetch_assoc();
$out = [
    'is_active' => $st['is_active'],
    'questions' => [],
    'categories' => []
];

// Fetch questions and categories regardless of round so team can switch tabs
$resQ = $conn->query("SELECT * FROM questions");
while($r = $resQ->fetch_assoc()) $out['questions'][] = $r;

$resC = $conn->query("SELECT * FROM portfolio_categories");
while($r = $resC->fetch_assoc()) $out['categories'][] = $r;

echo json_encode($out);
?>