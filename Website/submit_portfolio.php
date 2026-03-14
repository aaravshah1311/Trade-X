<?php
include 'db.php';

// 1. Session Check
if (!isset($_SESSION['team_id'])) {
    die("Error: Session expired. Please log in again.");
}

$tid = $_SESSION['team_id'];

// 2. Prevent Overwriting if already locked
// We check if the team already has entries in the portfolio table
$lock_check = $conn->query("SELECT id FROM team_portfolio WHERE team_id = $tid LIMIT 1");
if ($lock_check->num_rows > 0) {
    die("Error: Portfolio is already locked and cannot be modified.");
}

// 3. Data Validation
if (isset($_POST['shares']) && is_array($_POST['shares'])) {
    $total_cost = 0;
    $min_shares_passed = true;
    $price_per_share = 100;
    $purse_limit = 10000;

    // First pass: Validate all rules
    foreach ($_POST['shares'] as $cid => $qty) {
        $qty = (int)$qty;
        if ($qty < 3) {
            $min_shares_passed = false;
            break;
        }
        $total_cost += ($qty * $price_per_share);
    }

    if (!$min_shares_passed) {
        die("Error: Minimum 3 shares per company is compulsory.");
    }

    if ($total_cost > $purse_limit) {
        die("Error: Total investment (₹$total_cost) exceeds budget (₹$purse_limit).");
    }

    // 4. Secure Insertion
    // Using a transaction-like approach: clear any unfinished drafts first
    $conn->query("DELETE FROM team_portfolio WHERE team_id = $tid");

    $stmt = $conn->prepare("INSERT INTO team_portfolio (team_id, category_id, quantity) VALUES (?, ?, ?)");
    
    foreach ($_POST['shares'] as $cid => $qty) {
        $cid = (int)$cid;
        $qty = (int)$qty;
        $stmt->bind_param("iii", $tid, $cid, $qty);
        $stmt->execute();
    }

    echo "Success: Portfolio locked successfully!";
} else {
    echo "Error: No share data received from the portal.";
}
?>