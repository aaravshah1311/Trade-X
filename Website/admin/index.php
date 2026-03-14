<?php
include '../db.php';
header("Refresh: 10"); // Auto-refresh every 5 seconds for live tracking

// 1. START/STOP GAME LOGIC
if(isset($_POST['toggle_game'])) {
    $new = ($_POST['current_state'] == 0) ? 1 : 0;
    $conn->query("UPDATE quiz_status SET is_active = $new WHERE id = 1");
    header("Location: index.php");
    exit;
}

// 2. CREATE QUESTION
if(isset($_POST['add_q'])) {
    $stmt = $conn->prepare("INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $_POST['q'], $_POST['a'], $_POST['b'], $_POST['c'], $_POST['d'], $_POST['cor']);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// 3. DELETE QUESTION
if(isset($_GET['del_q'])) {
    $id = (int)$_GET['del_q'];
    $conn->query("DELETE FROM questions WHERE id = $id");
    header("Location: index.php");
    exit;
}

// 4. RESET GAME (Scores only)
if(isset($_POST['reset_scores'])) {
    $conn->query("TRUNCATE TABLE responses");
    $conn->query("TRUNCATE TABLE team_portfolio");
    $conn->query("UPDATE quiz_status SET is_active = 0 WHERE id = 1");
    header("Location: index.php");
    exit;
}

$status = $conn->query("SELECT is_active FROM quiz_status WHERE id=1")->fetch_assoc();
$is_open = $status['is_active'] ?? 0;
$categories = $conn->query("SELECT * FROM portfolio_categories")->fetch_all(MYSQLI_ASSOC);

// 5. GENERATE SQL SCRIPT FOR TRADEX DB
$sql_script = "USE tradex_db;\n\n";
$sql_script .= "-- Clear existing teams to avoid duplicates\n";
$sql_script .= "TRUNCATE TABLE teams;\n\n";
$sql_script .= "-- Insert teams and their allocated shares\n";

$teams_query = $conn->query("SELECT * FROM teams");
while ($t = $teams_query->fetch_assoc()) {
    $tid = $t['id'];
    $team_name = $conn->real_escape_string($t['team_name']);
    
    $stocks = [];
    $company_id = 0; // Assuming categories map sequentially to company IDs (0 to 6)
    $total_shares = 0; // Track total shares won by the team
    
    foreach($categories as $cat) {
        $p = $conn->query("SELECT quantity FROM team_portfolio WHERE team_id=$tid AND category_id=".$cat['id'])->fetch_assoc();
        $qty = (int)($p['quantity'] ?? 0);
        
        $stocks[(string)$company_id] = $qty;
        $total_shares += $qty; // Add to the total count
        $company_id++;
    }
    
    // Convert the stocks array to a JSON string
    $stocks_json = $conn->real_escape_string(json_encode($stocks));
    
    // Calculate financial distribution
    $share_price = 100;
    $total_wealth = 10000;
    
    // Purse is the remaining liquid cash after deducting the value of shares won
    $purse = $total_wealth - ($total_shares * $share_price); 
    
    // Failsafe: Ensure purse doesn't go below 0 if they somehow answer >100 questions
    if ($purse < 0) {
        $purse = 0;
    }
    
    // Insert into DB with calculated purse and total wealth
    $sql_script .= "INSERT INTO teams (name, purse, wealth, stocks, last_bid, wealth_history) VALUES ('$team_name', $purse, $total_wealth, '$stocks_json', 0, '[$total_wealth]');\n";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.php">
    <title>TRADE-X 2.0 Admin</title>
    <link rel="shortcut icon" href="../logo.ico" type="image/x-icon">
</head>
<body class="p-3 bg-light">
<div class="container-fluid">
    <div class="card p-3 mb-4 shadow-sm border-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1 class="logo-font mb-0 text-dark">TRADE-X 2.0 ADMIN</h1>
            <form method="POST" class="d-flex gap-2">
                <input type="hidden" name="current_state" value="<?= $is_open ?>">
                <button type="submit" name="toggle_game" class="btn btn-<?= $is_open ? 'danger' : 'success' ?> fw-bold px-4 shadow-sm">
                    <?= $is_open ? 'STOP GAME' : 'START GAME' ?>
                </button>
                <button type="submit" name="reset_scores" class="btn btn-outline-danger btn-sm" onclick="return confirm('Wipe all team data?')">RESET SCORES</button>
            </form>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card p-3 shadow-sm border-0">
                <h5 class="fw-bold text-primary mb-3">Live Team Status & Share Breakdown</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Team Name</th>
                                <th>Quiz Status</th>
                                <th>Port Status</th>
                                <?php foreach($categories as $c) echo "<th>".$c['category_name']."</th>"; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $ts = $conn->query("SELECT * FROM teams"); while($t = $ts->fetch_assoc()): $tid = $t['id']; ?>
                            <tr>
                                <td class="text-start fw-bold"><?= $t['team_name'] ?></td>
                                <td><?= ($conn->query("SELECT id FROM responses WHERE team_id=$tid LIMIT 1")->num_rows > 0) ? '✅' : '❌' ?></td>
                                <td><?= ($conn->query("SELECT id FROM team_portfolio WHERE team_id=$tid LIMIT 1")->num_rows > 0) ? '✅' : '❌' ?></td>
                                <?php foreach($categories as $cat): 
                                    $p = $conn->query("SELECT quantity FROM team_portfolio WHERE team_id=$tid AND category_id=".$cat['id'])->fetch_assoc();
                                    $qty = $p['quantity'] ?? 0;
                                ?>
                                <td class="<?= $qty > 0 ? 'fw-bold text-primary' : 'text-muted' ?>"><?= $qty > 0 ? $qty : '-' ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0">
                <h6 class="fw-bold mb-3">Add Question</h6>
                <form method="POST">
                    <input type="text" name="q" class="form-control mb-2 form-control-sm" placeholder="Question Text" required>
                    <input type="text" name="a" class="form-control mb-2 form-control-sm" placeholder="Option A">
                    <input type="text" name="b" class="form-control mb-2 form-control-sm" placeholder="Option B">
                    <input type="text" name="c" class="form-control mb-2 form-control-sm" placeholder="Option C">
                    <input type="text" name="d" class="form-control mb-2 form-control-sm" placeholder="Option D">
                    <select name="cor" class="form-select form-select-sm mb-3">
                        <option value="A">Correct: A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                    </select>
                    <button name="add_q" class="btn btn-primary btn-sm w-100 shadow-sm">Save Question</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-3 shadow-sm border-0 h-100">
                <h6 class="fw-bold mb-3 text-success">Quiz Management: Correct Answer Stats</h6>
                <table class="table table-sm align-middle text-center">
                    <thead class="table-light"><tr><th class="text-start">Question</th><th>Key</th><th>Correct Teams</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php $qs = $conn->query("SELECT * FROM questions"); while($q = $qs->fetch_assoc()): 
                            $cor_count = $conn->query("SELECT COUNT(*) as c FROM responses WHERE question_id={$q['id']} AND is_correct=1")->fetch_assoc()['c'];
                        ?>
                        <tr>
                            <td class="text-start"><?= $q['question_text'] ?></td>
                            <td><span class="badge bg-dark"><?= $q['correct_option'] ?></span></td>
                            <td class="fw-bold text-success" style="font-size: 1.1rem;"><?= $cor_count ?></td>
                            <td><a href="?del_q=<?= $q['id'] ?>" class="btn btn-link text-danger btn-sm p-0 fw-bold" onclick="return confirm('Delete?')">Delete</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-12 mt-2">
            <div class="card p-3 shadow-sm border-0 border-top border-primary border-4">
                <h5 class="fw-bold text-dark mb-1">TradeX Platform SQL Export</h5>
                <p class="text-muted small mb-3">Copy this script and run it in the <strong>tradex_db</strong> database to transfer all teams and their final share allocations.</p>
                <textarea class="form-control font-monospace bg-light border-secondary" rows="8" readonly style="font-size: 0.85rem;"><?= htmlspecialchars($sql_script) ?></textarea>
            </div>
        </div>

    </div>
</div>
</body>
</html>