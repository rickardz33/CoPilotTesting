<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
    exit;
}

// Enkel per-session lagring i JSON-fil
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}
$tasksFile = $dataDir . '/tasks_' . session_id() . '.json';

// Läs in uppgifter
$tasks = [];
if (file_exists($tasksFile)) {
    $raw = file_get_contents($tasksFile);
    $tasks = json_decode($raw, true) ?: [];
}

// Hantera POST för att lägga till uppgift
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $text = isset($_POST['task']) ? trim($_POST['task']) : '';
    $text = strip_tags($text);
    if ($text !== '') {
        $tasks[] = [
            'id' => bin2hex(random_bytes(8)),
            'text' => mb_substr($text, 0, 500),
            'done' => false,
            'created' => time()
        ];
        file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }
    header('Location: welcome.php');
    exit;
}

// Hantera toggle (markera som gjord/ogjord)
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = $_GET['id'];
    foreach ($tasks as &$t) {
        if (isset($t['id']) && $t['id'] === $id) {
            $t['done'] = !$t['done'];
            break;
        }
    }
    unset($t);
    file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    header('Location: welcome.php');
    exit;
}

// Hantera radering (valfritt, lägger till enkelt sätt att ta bort)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $tasks = array_values(array_filter($tasks, fn($t) => ($t['id'] ?? '') !== $id));
    file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
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
    <h1>Hej Elgiganten!</h1>
    <p>Du är nu inloggad.</p>
    <a href="https://elgiganten.eu.qlikcloud.com/sense/app/376a5db8-f215-458a-b29f-0f9b54fd220e/sheet/10937a23-edbb-4916-ac1d-ca36d20b46ec/state/analysis">LÄNK</a>

    <section>
        <h2>Att göra</h2>

        <!-- Formulär för att lägga till ny uppgift -->
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

                        <!-- Toggle via GET för enkel implementation -->
                        <form method="get" action="welcome.php" class="inline">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($t['id']); ?>">
                            <button type="submit"><?php echo !empty($t['done']) ? 'Ångra' : 'Markera klar'; ?></button>
                        </form>

                        <!-- Ta bort -->
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
