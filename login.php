<?php
// login.php — email + password + csrf. Logic lives in classes/User.php.
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/classes/User.php';

if (!empty($_SESSION['user_id'])) {                                // already logged in
    header('Location: ' . homeFor($_SESSION['role'] ?? ''));
    exit;
}

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $email    = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = t('login.err.empty');
    } else {
        $result = User::login($pdo, $email, $password);
        if ($result['ok']) {
            // TODO v2.0 (Recipe 2.4 step 4): if TWO_STEP is on, set $_SESSION['pending_user'] and go to verify-code.php.
            loginUser($result['user']);                            // new session id + session values
            header('Location: ' . homeFor($result['user']['role']));
            exit;
        }
        $error = match ($result['code']) {
            'LOCKED'  => t('login.err.locked') . ' (' . (int)$result['minutes'] . ' min)',
            'PENDING' => t('login.err.pending'),
            default   => t('login.err.bad'),
        };
    }
}

$pageTitle = t('login.title');
$bodyClass = '';
require __DIR__ . '/includes/header.php';
?>
<main class="container">
    <h1><?= e(t('login.title')) ?></h1>

    <?php if (!empty($_GET['expired'])): ?>
        <p class="banner" role="status"><?= e(t('login.expired')) ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="banner banner--error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post" action="/bedlink/login.php" autocomplete="on">
        <?= csrfField() ?>
        <label for="email"><?= e(t('login.email')) ?></label>
        <input id="email" name="email" type="email" value="<?= e($email) ?>" required autocomplete="username">

        <label for="password"><?= e(t('login.password')) ?></label>
        <input id="password" name="password" type="password" required autocomplete="current-password">

        <button type="submit" class="btn btn--primary"><?= e(t('login.submit')) ?></button>
    </form>

    <p><a href="/bedlink/register.php"><?= e(t('login.register')) ?></a></p>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
