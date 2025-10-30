<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/db.php'; // PDO i $pdo

$sid = session_id();

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
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
        .task { display:flex; align-items:center; gap:0.5rem; padding:0.5rem 0; border-bottom:1px solid #eee;}
        .task .text { flex:1; }
        .done { text-decoration:line-through; color:#888; }
        form.inline { display:inline; margin:0; }
        .controls { margin-top:1rem; }
    </style>
</head>
<body>
    <h1>Hej!</h1>
    <p>Du är nu inloggad.</p>

    <section>
        <h2>Att göra</h2>

        <form method="post" action="welcome.php">
            <input type="hidden" name="action" value="add">
            <input type="text" name="task" placeholder="Ny uppgift..." required maxlength="500" style="width:70%;">
            <button type="submit">Lägg till</button>
        </form>

        <div class="controls">
            <?php if (empty($tasks)): ?>
                <p>Inga uppgifter än.</p>
            <?php else: ?>
                <?php foreach ($tasks as $t): ?>
                    <div class="task">
                        <div class="text <?php echo !empty($t['done']) ? 'done' : ''; ?>">
                            <?php echo htmlspecialchars($t['text'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                        </div>

                        <form method="get" action="welcome.php" class="inline">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($t['id']); ?>">
                            <button type="submit"><?php echo !empty($t['done']) ? 'Ångra' : 'Markera klar'; ?></button>
                        </form>

                        <form method="get" action="welcome.php" class="inline" onsubmit="return confirm('Ta bort uppgiften?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($t['id']); ?>">
                            <button type="submit">Ta bort</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
