<?php
// classes/User.php — login, lockout and password rehash (Recipe 2.4)
// Needs setting() from includes/functions.php. Limits come from config/settings.json.
class User
{
    // Lockout columns on the users table. If your schema.sql names them differently,
    // change these two lines and nothing else.
    private const COL_FAILED = 'failed_logins';
    private const COL_LOCKED = 'locked_until';

    // A real bcrypt hash, only used to make "no such email" take as long as "wrong password".
    private const DUMMY_HASH = '$2y$10$76G3C44oJDJUzQOI7PrZCO/hhFGzDe8Kq/iLEyg/HO5iJT1xkuJq2';

    /**
     * @return array ['ok' => true, 'user' => row]
     *            or ['ok' => false, 'code' => 'BAD_LOGIN' | 'LOCKED' | 'PENDING', 'minutes' => int]
     * Same answer for a wrong email and a wrong password, so nobody can discover which emails exist.
     */
    public static function login(PDO $pdo, string $email, string $password): array
    {
        $maxFails = max(1, (int)setting('login_max_failures', 5));
        $lockMins = max(1, (int)setting('login_lockout_minutes', 15));
        $failed   = self::COL_FAILED;
        $locked   = self::COL_LOCKED;

        // Column names above are constants (not user input), so building the SQL string is safe.
        $stmt = $pdo->prepare(
            "SELECT *,
                    ($locked IS NOT NULL AND $locked > NOW()) AS is_locked,
                    CEIL(TIMESTAMPDIFF(SECOND, NOW(), $locked) / 60) AS lock_minutes
             FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if (!$u) {
            password_verify($password, self::DUMMY_HASH);          // burn the same time as a real check
            return ['ok' => false, 'code' => 'BAD_LOGIN'];
        }

        // Locked: refuse even if the password is right.
        if ((int)$u['is_locked'] === 1) {
            return ['ok' => false, 'code' => 'LOCKED', 'minutes' => max(1, (int)$u['lock_minutes'])];
        }

        // An old lock has run out: start counting from zero again.
        $fails = (int)$u[$failed];
        if ($u[$locked] !== null) {
            $pdo->prepare("UPDATE users SET $failed = 0, $locked = NULL WHERE user_id = ?")
                ->execute([$u['user_id']]);
            $fails = 0;
        }

        // Wrong password: count it, and lock on the 5th miss.
        if (!password_verify($password, $u['password_hash'])) {
            $fails++;
            if ($fails >= $maxFails) {
                $pdo->prepare("UPDATE users SET $failed = ?, $locked = NOW() + INTERVAL ? MINUTE WHERE user_id = ?")
                    ->execute([$fails, $lockMins, $u['user_id']]);
                return ['ok' => false, 'code' => 'LOCKED', 'minutes' => $lockMins];
            }
            $pdo->prepare("UPDATE users SET $failed = ? WHERE user_id = ?")->execute([$fails, $u['user_id']]);
            return ['ok' => false, 'code' => 'BAD_LOGIN'];
        }

        // Password is right: clear the counter, and upgrade the hash if PHP's default got stronger.
        $pdo->prepare("UPDATE users SET $failed = 0, $locked = NULL WHERE user_id = ?")->execute([$u['user_id']]);
        if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE user_id = ?')
                ->execute([password_hash($password, PASSWORD_DEFAULT), $u['user_id']]);
        }

        // Work accounts wait for an admin (is_approved = 0). Only said AFTER the password is right.
        if (!(int)$u['is_approved']) {
            return ['ok' => false, 'code' => 'PENDING'];
        }

        unset($u['password_hash'], $u['otp_hash'], $u['is_locked'], $u['lock_minutes']);   // never pass secrets around
        return ['ok' => true, 'user' => $u];
    }
}
