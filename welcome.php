<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/db.php'; // PDO i $pdo

$sid = session_id();

// Hämta användarnamn om user_id finns i session
$username = '';
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare('SELECT username FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $username = $stmt->fetchColumn() ?: '';
    } catch (Exception $e) {
        // ignorera fel här (eller logga)
        $username = '';
    }
}

// Läs in uppgifter från DB
$tasks = [];
try {
    $stmt = $pdo->prepare('SELECT id, text, done, created FROM tasks WHERE session_id = :sid ORDER BY created DESC');
    $stmt->execute([':sid' => $sid]);
    $tasks = $stmt->fetchAll();
} catch (Exception $e) {
    // Vid fel, visa tom lista (eller logga)
    $tasks = [];
}

// Hantera POST för att lägga till uppgift
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $text = isset($_POST['task']) ? trim($_POST['task']) : '';
    $text = strip_tags($text);
    if ($text !== '') {
        $id = bin2hex(random_bytes(8));
        $created = time();
        $stmt = $pdo->prepare('INSERT INTO tasks (id, session_id, text, done, created) VALUES (:id, :sid, :text, 0, :created)');
        $stmt->execute([
            ':id' => $id,
            ':sid' => $sid,
            ':text' => mb_substr($text, 0, 500),
            ':created' => $created,
        ]);
    }
    header('Location: welcome.php');
    exit;
}

// Hantera toggle (markera som gjord/ogjord)
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare('UPDATE tasks SET done = 1 - done WHERE id = :id AND session_id = :sid');
    $stmt->execute([':id' => $id, ':sid' => $sid]);
    header('Location: welcome.php');
    exit;
}

// Hantera radering
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = :id AND session_id = :sid');
    $stmt->execute([':id' => $id, ':sid' => $sid]);
    header('Location: welcome.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Välkommen</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 0 auto; padding: 0 1rem; }
        .main-nav { background: #333; color: white; width: 100%; margin-bottom: 2rem; }
        .nav-container { max-width: 720px; margin: 0 auto; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; gap: 1rem; align-items: center; }
        .logout-btn { color: white; text-decoration: none; padding: 0.5rem 1rem; background: #666; border-radius: 4px; }
        .logout-btn:hover { background: #777; }
        .brand { font-weight: bold; }
        .task { display:flex; align-items:center; gap:0.5rem; padding:0.5rem 0; border-bottom:1px solid #eee;}
        .task .text { flex:1; }
        .done { text-decoration:line-through; color:#888; }
        form.inline { display:inline; margin:0; }
        .controls { margin-top:1rem; }
        .task-list { margin-top: 2rem; }
        .task-item { padding: 1rem; background: #f5f5f5; margin-bottom: 0.5rem; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
        .task-item.done { text-decoration: line-through; opacity: 0.6; }
        .task-actions a { margin-left: 1rem; color: #d9534f; text-decoration: none; }
        .task-actions a:hover { text-decoration: underline; }
        form { margin: 2rem 0; }
        input[type="text"] { width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; }
        button { padding: 0.5rem 1rem; background: #333; color: white; border: none; cursor: pointer; border-radius: 4px; }
        button:hover { background: #555; }
    </style>
</head>
<body>
    <?php require 'header.php'; ?>

    <h1>Hej<?php echo $username ? ', ' . htmlspecialchars($username, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : ''; ?>!</h1>
    <p>Du är nu inloggad.</p>

    <!-- Lägg till ny uppgift -->
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="task" placeholder="Lägg till ny uppgift..." required>
        <button type="submit">Lägg till</button>
    </form>

    <!-- Uppgiftslista -->
    <div class="task-list">
        <h2>Dina uppgifter</h2>
        <?php if (count($tasks) > 0): ?>
            <?php foreach ($tasks as $task): ?>
                <div class="task-item <?php echo $task['done'] ? 'done' : ''; ?>">
                    <span><?php echo htmlspecialchars($task['text']); ?></span>
                    <div class="task-actions">
                        <a href="?action=toggle&id=<?php echo $task['id']; ?>">
                            <?php echo $task['done'] ? 'Ångra' : 'Markera klar'; ?>
                        </a>
                        <a href="?action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Radera denna uppgift?');">Radera</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Inga uppgifter än. Lägg till en ny!</p>
        <?php endif; ?>
    </div>

</body>
</html>
