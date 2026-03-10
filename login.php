<?php
require_once 'dbconn.php';
session_start();
include 'csrf_token.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "يرجى تعبئة جميع الحقول.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE USER_NAME = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['PASSWORD'])) {
                $_SESSION['user_id'] = $row['USER_ID'];
                $_SESSION['username'] = $row['USER_NAME'];
                header("Location: index.php");
                exit;
            } else {
                $error = "كلمة المرور غير صحيحة.";
            }
        } else {
            $error = "اسم المستخدم غير موجود.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/all.min.css" />
    <link rel="stylesheet" href="CSS/login.css" />
    <link rel="manifest" href="manifest.json" />


</head>

<body>
    <div class="login-container">
        <h2>تسجيل الدخول</h2>

        <?php if (!empty($error))
            echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>
        <form method="POST" action="login.php">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <label for="username">اسم المستخدم:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">كلمة المرور:</label>
            <div style="position: relative;">
                <input type="password" name="password" id="password" required style="padding-left: 40px;">
                <span id="togglePassword"
                    style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b; display: flex; align-items: center; justify-content: center; z-index: 10;">
                    <i id="eyeIcon" class="fas fa-eye"></i>
                </span>
            </div>
            <a href="change_password.php">تغيير كلمةالمرور</a>
            <button type="submit">دخول</button>
        </form>
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('pwabuilder-sw.js')
                .then(function (registration) {
                    console.log('Service Worker registered successfully');
                })
                .catch(function (error) {
                    console.log('Service Worker registration failed:', error);
                });
        }

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            if (type === 'text') {
                eyeIcon.className = 'fas fa-eye-slash';
            } else {
                eyeIcon.className = 'fas fa-eye';
            }
        });
    </script>

</body>

</html>