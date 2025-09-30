<?php
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = trim($_POST['signup-username']);
    $email = trim($_POST['signup-email']);
    $password = $_POST['signup-password'];
    $confirm_password = $_POST['signup-confirm-password'];

    if ($password !== $confirm_password) { $errors[] = "A két jelszó nem egyezik!"; }
    if (strlen($password) < 6) { $errors[] = "A jelszónak legalább 6 karakter hosszúnak kell lennie."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Érvénytelen email formátum."; }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) { $errors[] = "Ez a felhasználónév vagy email cím már foglalt!"; }
    $stmt->close();
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if ($stmt->execute()) { $success_message = "Sikeres regisztráció! Most már bejelentkezhetsz."; } 
        else { $errors[] = "Hiba a regisztráció során."; }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username_or_email = $_POST['login-username'];
    $password = $_POST['login-password'];
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: profile.php");
            exit();
        } else { $errors[] = "Hibás felhasználónév vagy jelszó."; }
    } else { $errors[] = "Hibás felhasználónév vagy jelszó."; }
    $stmt->close();
}
$page_title = "Bejelentkezés & Regisztráció";
include 'header.php';
?>
<main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
<div class="w-full max-w-md space-y-8">
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <p><?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>
    <div x-data="{ tab: 'login' }">
        <div class="border-b border-border-light dark:border-border-dark">
            <nav aria-label="Tabs" class="-mb-px flex space-x-8">
                <button :class="{ 'border-primary text-primary': tab === 'login', 'border-transparent text-muted-light dark:text-muted-dark hover:text-text-light dark:hover:text-text-dark': tab !== 'login' }" @click="tab = 'login'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">Bejelentkezés</button>
                <button :class="{ 'border-primary text-primary': tab === 'signup', 'border-transparent text-muted-light dark:text-muted-dark hover:text-text-light dark:hover:text-text-dark': tab !== 'signup' }" @click="tab = 'signup'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">Regisztráció</button>
            </nav>
        </div>
        <div class="pt-8 space-y-6" x-show="tab === 'login'">
            <form action="login_register.php" class="space-y-6" method="POST">
                <input type="hidden" name="login">
                <div>
                    <label class="block text-sm font-medium" for="login-username">Felhasználónév vagy Email</label>
                    <input class="form-input mt-1 block w-full rounded-lg" id="login-username" name="login-username" required type="text"/>
                </div>
                <div>
                    <label class="block text-sm font-medium" for="login-password">Jelszó</label>
                    <input class="form-input mt-1 block w-full rounded-lg" id="login-password" name="login-password" required type="password"/>
                </div>
                <div><button class="w-full flex justify-center py-3 px-4 rounded-lg font-bold text-white bg-primary hover:bg-opacity-90" type="submit">Bejelentkezés</button></div>
            </form>
        </div>
        <div class="pt-8 space-y-6" style="display: none;" x-show="tab === 'signup'">
            <form action="login_register.php" class="space-y-6" method="POST">
                <input type="hidden" name="register">
                <div>
                    <label class="block text-sm font-medium" for="signup-username">Felhasználónév</label>
                    <input class="form-input mt-1 block w-full rounded-lg" name="signup-username" required type="text"/>
                </div>
                <div>
                    <label class="block text-sm font-medium" for="signup-email">Email</label>
                    <input class="form-input mt-1 block w-full rounded-lg" name="signup-email" required type="email"/>
                </div>
                <div>
                    <label class="block text-sm font-medium" for="signup-password">Jelszó</p>
                    <input class="form-input mt-1 block w-full rounded-lg" name="signup-password" required type="password"/>
                </div>
                <div>
                    <label class="block text-sm font-medium" for="signup-confirm-password">Jelszó megerősítése</p>
                    <input class="form-input mt-1 block w-full rounded-lg" name="signup-confirm-password" required type="password"/>
                </div>
                <div><button class="w-full flex justify-center py-3 px-4 rounded-lg font-bold text-white bg-primary hover:bg-opacity-90" type="submit">Regisztráció</button></div>
            </form>
        </div>
    </div>
</div>
</main>
<script defer src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script>
<?php include 'footer.php'; ?>