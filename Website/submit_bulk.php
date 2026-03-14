<?php
include 'db.php';
if(!isset($_SESSION['team_id'])) exit;

$tid = $_SESSION['team_id'];
$answers = $_POST['answers'] ?? [];

// Check if already submitted
$check = $conn->query("SELECT id FROM responses WHERE team_id = $tid LIMIT 1");
if($check->num_rows > 0) exit; // Silent exit if already done

foreach($answers as $qid => $ans) {
    $qid = (int)$qid;
    // Verify correct answer
    $q_res = $conn->query("SELECT correct_option FROM questions WHERE id = $qid");
    $q_row = $q_res->fetch_assoc();
    $is_correct = ($ans == $q_row['correct_option']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO responses (team_id, question_id, submitted_answer, is_correct) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $tid, $qid, $ans, $is_correct);
    $stmt->execute();
}
echo "Quiz responses saved.";
?>