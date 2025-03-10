<?php
include '../config.php';

// Redirect non-students
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

// Get game code
$game_code = $_POST['game_code'] ?? die("Invalid game code!");

try {
    // Fetch game session and quiz
    $stmt = $pdo->prepare("SELECT q.*, gs.id AS session_id 
                          FROM game_sessions gs 
                          JOIN quizzes q ON gs.quiz_id = q.id 
                          WHERE gs.code = ? AND gs.is_active = 1");
    $stmt->execute([$game_code]);
    $quiz = $stmt->fetch();

    if (!$quiz) die("Invalid or expired game code!");
    
    $questions = json_decode($quiz['questions'], true);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Store battle session in student's session
$_SESSION['current_battle'] = [
    'session_id' => $quiz['session_id'],
    'start_time' => time()
];
?>

<!-- Add to existing HTML -->
<div id="battle-interface">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4><?= htmlspecialchars($quiz['title']) ?></h4>
            <div class="float-end">
                XP: <span id="current-xp">0</span>
            </div>
        </div>
        <div class="card-body" id="question-container"></div>
    </div>
</div>

<script>
const questions = <?= json_encode($questions) ?>;
let currentQuestion = 0;
let xp = 0;

// Load question with timer
function loadQuestion() {
    if (currentQuestion >= questions.length) {
        alert("Battle complete! You earned " + xp + " XP!");
        window.location.href = "dashboard.php";
        return;
    }

    const q = questions[currentQuestion];
    const container = document.getElementById('question-container');
    container.innerHTML = `
        <h5>${q.question}</h5>
        <div class="list-group mb-3">
            ${q.options.map((opt, i) => `
                <button class="list-group-item list-group-item-action" 
                        onclick="submitAnswer('${opt.replace("'", "\\'")}')">
                    ${opt}
                </button>
            `).join('')}
        </div>
        <div class="text-end">
            Time Left: <span id="timer">${q.timer}</span>s
        </div>
    `;

    startTimer(q.timer);
}

// Answer submission
function submitAnswer(answer) {
    const correct = answer === questions[currentQuestion].answer;
    if (correct) {
        xp += 100; // Base XP
        // Add time bonus (example: 10 XP per remaining second)
        xp += parseInt(document.getElementById('timer').textContent) * 10;
        document.getElementById('current-xp').textContent = xp;
    }
    
    currentQuestion++;
    loadQuestion();
}

// Timer system
function startTimer(seconds) {
    let timeLeft = seconds;
    const timerElement = document.getElementById('timer');
    const timer = setInterval(() => {
        timeLeft--;
        timerElement.textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(timer);
            currentQuestion++;
            loadQuestion();
        }
    }, 1000);
}

// Start battle
loadQuestion();
</script>