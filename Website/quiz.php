<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php'; 

if(!isset($_SESSION['team_id'])) header("Location: index.php"); 

$tid = $_SESSION['team_id'];
$q_locked = $conn->query("SELECT id FROM responses WHERE team_id=$tid LIMIT 1")->num_rows > 0;
$p_check = $conn->query("SELECT p.quantity, c.category_name FROM team_portfolio p JOIN portfolio_categories c ON p.category_id=c.id WHERE p.team_id=$tid");
$p_locked = ($p_check->num_rows > 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <link rel="shortcut icon" href="logo.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@500;800&display=swap" rel="stylesheet">
    <title>TRADE-X 2.0 | Terminal</title>
    <style>
:root{
    --primary:#2563eb;
    --bg:#020617;
    --glass:rgba(15,23,42,.75);
    --border:rgba(255,255,255,.08);
    --text:#f8fafc;
    --green:#10b981;
    --red:#ef4444;
}

/* ===========================
   BASE
=========================== */
html,body{
    margin:0;
    height:100%;
    background:var(--bg);
    font-family:'Inter',sans-serif;
    color:var(--text);
    overflow-x:hidden;
}

.portal-wrapper{
    max-width:1100px;
    margin:auto;
    padding:30px 20px 110px;
}

/* ===========================
   VIDEO BACKGROUND
=========================== */
#bg-video-container{
    position:fixed;
    inset:0;
    z-index:-100;
}
#bg-video{
    width:100%;
    height:100%;
    object-fit:cover;
}
.video-overlay{
    position:fixed;
    inset:0;
    background:linear-gradient(to bottom, rgba(2,6,23,.85), rgba(2,6,23,.95));
    z-index:-90;
}

/* ===========================
   LOGO
=========================== */
.header-logo{
    font-size:clamp(1.6rem,4vw,2.5rem);
    font-weight:900;
    text-align:center;
    margin-bottom:30px;
    background:linear-gradient(170deg,#00ff00 50%,#ff0000 50%);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* ===========================
   GLASS PANEL
=========================== */
.glass-panel{
    background:var(--glass);
    backdrop-filter:blur(18px);
    border:1px solid var(--border);
    border-radius:26px;
    padding:30px;
    margin-bottom:25px;
    box-shadow:0 25px 60px rgba(0,0,0,.45);
    transition:.3s ease;
}
.glass-panel:hover{
    transform:translateY(-3px);
}

/* ===========================
   NAVIGATION TABS
=========================== */
.nav-pills{
    background:rgba(255,255,255,.05);
    border:1px solid var(--border);
    padding:6px;
    border-radius:16px;
}
.nav-pills .nav-link{
    font-family:'JetBrains Mono',monospace;
    font-weight:700;
    color:rgba(255,255,255,.6);
    border-radius:12px;
    transition:.25s ease;
}
.nav-pills .nav-link.active{
    background:var(--primary)!important;
    color:#fff!important;
    box-shadow:0 6px 18px rgba(37,99,235,.4);
}

/* ===========================
   QUIZ BUTTONS
=========================== */
.btn-outline-primary{
    border-radius:50px;
    transition:.25s ease;
}
.btn-outline-primary:hover{
    background:var(--primary);
    color:#fff;
}

/* ===========================
   PORTFOLIO TABLE
=========================== */
.portfolio-card{
    border-radius:20px;
    padding:15px;
}

.portfolio-table{
    width:100%;
    border-collapse:collapse;
    font-size:15px;
}
.portfolio-table thead th{
    font-size:13px;
    letter-spacing:1px;
    color:#60a5fa;
    border-bottom:1px solid var(--border);
    padding:14px 8px;
}
.portfolio-table td{
    padding:16px 8px;
    border-bottom:1px solid rgba(255,255,255,.05);
}
.portfolio-table tbody tr:hover{
    background:rgba(255,255,255,.05);
}

/* Quantity Input */
.qty-input{
    width:65px;
    text-align:center;
    background:rgba(255,255,255,.07)!important;
    border:1px solid var(--border)!important;
    color:#fff!important;
    border-radius:10px!important;
}

/* ===========================
   PURSE BOX
=========================== */
.purse-box{
    background:rgba(37,99,235,.1);
    border:1px dashed var(--primary);
    border-radius:18px;
    padding:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* ===========================
   MAIN ACTION BUTTON
=========================== */
.btn-trade{
    background:var(--primary);
    border:none;
    padding:16px;
    border-radius:14px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:1px;
    transition:.25s ease;
}
.btn-trade:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(37,99,235,.45);
    background:#1d4ed8;
}

/* ===========================
   STICKY ACTION BAR (MOBILE SAFE)
=========================== */
.action-bar{
    margin-top:25px;
}
@media(max-width:768px){
    .action-bar{
        position:sticky;
        bottom:0;
        padding:15px;
        background:linear-gradient(to top, rgba(2,6,23,.98), rgba(2,6,23,.85));
        backdrop-filter:blur(10px);
        border-top:1px solid rgba(255,255,255,.05);
        z-index:40;
    }
    .action-bar .btn-trade{
        width:100%;
    }
}

/* ===========================
   STATUS PANELS
=========================== */
.status-panel{
    position:relative;
    text-align:center;
    padding:60px 30px;
    border-radius:30px;
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,.08);
    background:rgba(15,23,42,.75);
    box-shadow:0 30px 80px rgba(0,0,0,.5);
    overflow:hidden;
}

/* Animated glow border */
.status-panel::before{
    content:"";
    position:absolute;
    inset:-2px;
    border-radius:32px;
    background:linear-gradient(120deg,#2563eb,#10b981,#ef4444,#2563eb);
    background-size:400% 400%;
    z-index:-1;
    animation:glowMove 8s linear infinite;
    opacity:.25;
}

@keyframes glowMove{
    0%{background-position:0% 50%;}
    100%{background-position:400% 50%;}
}

/* Icon Circle */
.status-icon{
    width:90px;
    height:90px;
    margin:0 auto 25px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:38px;
    font-weight:bold;
    color:#fff;
}

/* Quiz Submitted */
.status-success .status-icon{
    background:linear-gradient(135deg,#10b981,#059669);
    box-shadow:0 12px 35px rgba(16,185,129,.5);
}
.status-success h2{
    color:var(--green);
    font-weight:800;
    margin-bottom:12px;
}

/* Session Closed */
.status-closed .status-icon{
    background:linear-gradient(135deg,#ef4444,#b91c1c);
    box-shadow:0 12px 35px rgba(239,68,68,.5);
}
.status-closed h2{
    color:var(--red);
    font-weight:800;
    margin-bottom:12px;
}

/* ===========================
   RESPONSIVE
=========================== */
@media(max-width:768px){
    .portal-wrapper{
        padding:20px 15px 130px;
    }
    .glass-panel{
        padding:20px;
    }
    .portfolio-table td{
        font-size:14px;
    }
    .status-panel{
        padding:40px 20px;
    }
    .status-icon{
        width:70px;
        height:70px;
        font-size:28px;
    }
}
    </style>
</head>
<body>

<div id="bg-video-container">
    <video id="bg-video" autoplay muted loop playsinline preload="auto">
        <source src="vid.mp4" type="video/mp4">
    </video>
</div>
<div class="video-overlay"></div>

<div class="portal-wrapper">
    <div class="header-logo">TRADE-X 2.0</div>
<div id="xxxx">
<div class="status-panel status-closed">
    <div class="status-icon">🔒</div>
    <h2>SESSION CLOSED</h2>
    <p>Waiting for Team to Start</p>
</div>
</div>
    <div id="active-ui">
        <ul class="nav nav-pills nav-justified mb-4">
            <li class="nav-item"><button class="nav-link active" id="tab-q" onclick="switchTab('q')">QUIZ</button></li>
            <li class="nav-item"><button class="nav-link" id="tab-p" onclick="switchTab('p')">PORTFOLIO</button></li>
        </ul>

        <div id="sec-q">
            <?php if($q_locked): ?>
                <div class="status-panel status-success">
    <div class="status-icon">✓</div>
    <h2>QUIZ SUBMITTED</h2>
    <p>Your answers are securely encrypted and locked.</p>
</div>
            <?php else: ?>
                <div id="q-render" class="row gy-3"></div>
                <button onclick="saveQuiz()" class="btn btn-trade w-100 mt-4">FINAL SUBMIT</button>
            <?php endif; ?>
        </div>

        <div id="sec-p" class="d-none">
            <?php if($p_locked): ?>
                <div class="glass-panel">
                    <h4 class="text-primary fw-bold mb-4 text-center">PORTFOLIO LOCKED</h4>
                    <div class="portfolio-card">
                        <table class="portfolio-table">
                            <thead><tr><th>Company</th><th>Shares</th><th class="text-end">Value</th></tr></thead>
                            <tbody>
                                <?php $inv=0; while($row = $p_check->fetch_assoc()): $v=$row['quantity']*100; $inv+=$v; ?>
                                <tr>
                                    <td class="fw-bold"><?= $row['category_name'] ?></td>
                                    <td><span class="badge bg-primary"><?= $row['quantity'] ?></span></td>
                                    <td class="text-end fw-bold text-primary">₹<?= $v ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr><td colspan="2" class="text-light">Total Invested:</td><td class="text-end text-success">₹<?= $inv ?></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="purse-box mb-4">
                    <span class="fw-bold small opacity-75">AVAILABLE PURSE:</span>
                    <h3 class="fw-bold text-primary mb-0">₹<span id="bal">10000</span></h3>
                </div>
                <div id="p-render"></div>
                <button onclick="savePort()" class="btn btn-trade w-100 mt-3">LOCK PORTFOLIO</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
let quizData = {}, isGameActive = false, renderedOnce = false;

// Sync and Video handling logic remains the same
const bgVideo = document.getElementById("bg-video");
window.addEventListener('load', () => {
    bgVideo.play().catch(e => {
        window.addEventListener('click', () => bgVideo.play(), { once: true });
    });
});

function sync() {
    fetch('api_v2.php').then(r => r.json()).then(data => {
        if(isGameActive && data.is_active == 0) autoSubmitAll();
        isGameActive = (data.is_active == 1);
        document.getElementById('active-ui').classList.toggle('d-none', !isGameActive);
        document.getElementById('xxxx').classList.toggle('d-none', isGameActive);
        if(isGameActive && !renderedOnce) { renderUI(data); renderedOnce = true; }
    });
}

function renderUI(data) {
    const qb = document.getElementById('q-render');
    if(qb) {
        let h = '';
        data.questions.forEach(q => {
            h += `<div class="col-12"><div class="glass-panel"><p class="fw-bold mb-3">${q.question_text}</p><div class="d-flex flex-wrap gap-2">` + 
                 ['A','B','C','D'].map(o => q['option_'+o.toLowerCase()] ? `<button id="bq_${q.id}_${o}" onclick="selQ(${q.id},'${o}')" class="btn btn-outline-primary btn-sm rounded-pill">${o}: ${q['option_'+o.toLowerCase()]}</button>` : '').join('') +
                 `</div></div></div>`;
        });
        qb.innerHTML = h;
    }
    const pb = document.getElementById('p-render');
    if(pb) {
        let h = `<div class="glass-panel"><div class="portfolio-card"><table class="portfolio-table" style="background-color: transparent;"><thead><tr><th>Company</th><th class="text-center">Qty</th><th class="text-end">Total</th></tr></thead><tbody>`;
        data.categories.forEach(c => {
            h += `<tr><td><small class="fw-bold">${c.category_name}</small></td><td><input type="number" class="form-control form-control-sm qty-input mx-auto" data-id="${c.id}" value="3" min="0" oninput="math()"></td><td class="fw-bold text-primary text-end">₹<span id="s_${c.id}">300</span></td></tr>`;
        });
        h += `</tbody></table></div></div>`;
        pb.innerHTML = h;
        math();
    }
}

function selQ(qid, o) {
    quizData[qid] = o;
    document.querySelectorAll(`[id^="bq_${qid}_"]`).forEach(b => b.classList.remove('btn-primary', 'text-white'));
    document.getElementById(`bq_${qid}_${o}`).classList.add('btn-primary', 'text-white');
}

function math() {
    let tot = 0;
    document.querySelectorAll('.qty-input').forEach(i => { let v = i.value * 100; tot += v; document.getElementById('s_'+i.dataset.id).innerText = v; });
    document.getElementById('bal').innerText = 10000 - tot;
}

function switchTab(t) {
    document.getElementById('sec-q').classList.toggle('d-none', t!=='q');
    document.getElementById('sec-p').classList.toggle('d-none', t!=='p');
    document.getElementById('tab-q').classList.toggle('active', t==='q');
    document.getElementById('tab-p').classList.toggle('active', t==='p');
}

function autoSubmitAll() {
    let fd = new FormData();
    for(let qid in quizData) fd.append(`answers[${qid}]`, quizData[qid]);
    fetch('submit_bulk.php', { method: 'POST', body: fd });
}

function savePort() {
    let fd = new FormData();
    document.querySelectorAll('.qty-input').forEach(i => fd.append(`shares[${i.dataset.id}]`, i.value));
    fetch('submit_portfolio.php', { method: 'POST', body: fd }).then(r => r.text()).then(m => { alert(m); location.reload(); });
}

function saveQuiz() {
    if(!confirm("Submit and lock results?")) return;
    autoSubmitAll(); alert("Quiz Submitted!"); location.reload();
}

setInterval(sync, 4000);
sync();
</script>
</body>
</html>