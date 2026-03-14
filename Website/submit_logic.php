<?php
include 'db.php';
$tid = $_SESSION['team_id'];
$qid = (int)$_GET['qid'];
$ans = $_GET['ans'];

$check = $conn->query("SELECT id FROM responses WHERE team_id=$tid AND question_id=$qid");
if($check->num_rows > 0) die("Already Answered!");

$q = $conn->query("SELECT correct_option FROM questions WHERE id=$qid")->fetch_assoc();
$correct = ($ans == $q['correct_option']) ? 1 : 0;

$conn->query("INSERT INTO responses (team_id, question_id, submitted_answer, is_correct) VALUES ($tid, $qid, '$ans', $correct)");
echo "Answer Submitted!";
?>