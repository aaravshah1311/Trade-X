<?php 
// SESSION GUARD: Prevents the "session already active" notice
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <link rel="shortcut icon" href="logo.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@500;800&display=swap" rel="stylesheet">
    <title>Trade-X 2.0 | Team Login</title>

    <style>
        :root{
            --primary:#2563eb;
            --bg:#020617;
            --glass:rgba(15,23,42,.75);
            --border:rgba(255,255,255,.12);
            --text:#f8fafc;
            --green:#00ff00;
            --red:#ff0000;
        }

        html,body{
            height:100%;
            margin:0;
            overflow:hidden;
            font-family:Inter,sans-serif;
            color:var(--text);
            background: var(--bg);
        }

        /* ===== VIDEO BACKGROUND ===== */
        #bg-video-container{
            position:fixed;
            inset:0;
            z-index:-100;
        }
        #bg-video{
            position:absolute;
            inset:0;
            width:100%;
            height:100%;
            object-fit:cover;
        }
        .video-overlay{
            position:fixed;
            inset:0;
            background:rgba(2,6,23,.65);
            z-index:-90;
        }

        #main-wrapper {
            position: relative;
            z-index: 10;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== HEADER ===== */
        .header {
            height: 8vh;
            padding: 0 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--glass);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            border-radius: 0 0 20px 20px;
        }

        .header-logo {
            font-size: clamp(1.2rem, 4vw, 2.3rem);
            font-weight: 900;
            background: linear-gradient(170deg, var(--green) 50%, var(--red) 50%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: 'Times New Roman', Times, serif;
        }

        /* ===== RESPONSIVE LOGIN CARD ===== */
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: var(--glass);
            border-radius: 30px;
            padding: clamp(25px, 5vh, 50px) clamp(20px, 5vw, 45px);
            border: 1px solid var(--border);
            backdrop-filter: blur(20px);
            width: 100%;
            max-width: 450px; /* Desktop width */
            box-shadow: 0 40px 100px rgba(0,0,0,.5);
        }

        .login-title {
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-label {
            font-family: 'JetBrains Mono', monospace;
            color: var(--primary);
            font-weight: 800;
            font-size: 0.85rem;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.07) !important;
            border: 1px solid var(--border) !important;
            border-radius: 12px !important;
            color: white !important;
            padding: 12px 15px !important;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.12) !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.3) !important;
        }

        .btn-access {
            background: var(--primary);
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-access:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }

        /* ===== QUOTE TICKER ===== */
        .ticker-wrap{
            height: 7vh;
            background:#000;
            display:flex;
            align-items:center;
            overflow:hidden;
            border-top:1px solid var(--border);
        }
        .ticker-move{
            display:flex;
            white-space:nowrap;
            animation:ticker 40s linear infinite; 
        }
        .ticker-item{
            padding: 0 3rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            font-size: clamp(0.9rem, 2vh, 1.2rem);
            display: flex;
            align-items: center;
            text-transform: uppercase;
            font-style: italic;
            letter-spacing: 1px;
            background: linear-gradient(165deg, var(--green) 50%, var(--red) 50%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes ticker{
            0%{transform:translateX(0)}
            100%{transform:translateX(-50%)}
        }

        /* Error Message Style */
        .error-msg {
            color: var(--red);
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
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

<div id="main-wrapper">
    <div class="header">
        <div class="header-logo">TRADE-X 2.0</div>
        <span class="badge bg-primary px-3 py-2" style="font-size: 0.7rem;">SECURE TERMINAL</span>
    </div>

    <div class="main">
        <div class="login-card">
            <h1 class="login-title">TEAM LOGIN</h1>
            
            <form method="POST">
                <div class="mb-2">
                    <label class="form-label">IDENTIFICATION (TEAM ID)</label>
                    <input type="text" name="team" class="form-control" placeholder="Enter Team Name" required autocomplete="off">
                </div>

                <div class="mb-2">
                    <label class="form-label">ACCESS KEY (PASSWORD)</label>
                    <input type="password" name="pass" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" name="login" class="btn btn-access w-100">Initialize Session</button>
            </form>

            <?php
            if(isset($_POST['login'])) {
                // Securely query the database
                $stmt = $conn->prepare("SELECT id FROM teams WHERE team_name=? AND password=?");
                $stmt->bind_param("ss", $_POST['team'], $_POST['pass']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if($res = $result->fetch_assoc()){
                    $_SESSION['team_id'] = $res['id'];
                    // Redirect to quiz page
                    echo "<script>window.location.href='quiz.php';</script>";
                } else { 
                    echo "<p class='error-msg'>[ ACCESS DENIED: INVALID CREDENTIALS ]</p>"; 
                }
            }
            ?>
        </div>
    </div>

    <div class="ticker-wrap">
        <div id="ticker" class="ticker-move"></div>
    </div>
</div>

<script>
// Handle Video Playback
const bgVideo = document.getElementById("bg-video");
window.addEventListener('load', () => {
    bgVideo.play().catch(() => {
        window.addEventListener('click', () => bgVideo.play(), { once: true });
    });
});

// Trading Dialogues for Ticker
const dialogues = [
    "“Risk hai toh ishq hai.”",
    "“Sabse bada risk hai… risk na lena.”",
    "“Profit booking is never wrong.”",
    "“Market sabko mauka deta hai.”",
    "“Bulls make money, bears make money, pigs get slaughtered.”"
];

function initTicker() {
    const tickerEl = document.getElementById("ticker");
    const quoteHtml = dialogues.map(q => `
        <div class="ticker-item">
            ${q}
        </div>
    `).join("");

    // Duplicate content for seamless infinite scroll
    tickerEl.innerHTML = quoteHtml + quoteHtml;
}

initTicker();
</script>

</body>
</html>