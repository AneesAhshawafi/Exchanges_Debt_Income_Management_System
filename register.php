<?php
session_start();
require_once 'dbconn.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "يرجى تعبئة جميع الحقول.";
    } elseif ($password !== $confirm_password) {
        $error = "كلمتا المرور غير متطابقتين.";
    } else {
        $stmt = $conn->prepare("SELECT USER_ID FROM users WHERE USER_NAME = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "اسم المستخدم مستخدم بالفعل.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (USER_NAME, PASSWORD) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $hashed_password);

            if ($insert->execute()) {
                $success = "تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.";
            } else {
                $error = "حدث خطأ أثناء إنشاء الحساب.";
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>تسجيل مستخدم جديد</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="CSS/register.css" />
    </head>
    <body>
        <div class="register-container">
            <h2>تسجيل مستخدم جديد</h2>

            <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
            <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>

            <form method="POST" action="" id="register-form">
                <label for="username">اسم المستخدم:</label>
                <input type="text" name="username" id="username" required>

                <label for="password">كلمة المرور:</label>
                <input type="password" name="password" id="password" required>

                <ul class="requirements" id="password-requirements">
                    <li id="length" class="invalid">❌ 8 أحرف أو أكثر</li>
                    <li id="lower" class="invalid">❌ حرف صغير (a-z)</li>
                    <li id="upper" class="invalid">❌ حرف كبير (A-Z)</li>
                    <li id="number" class="invalid">❌ رقم (0-9)</li>
                    <li id="special" class="invalid">❌ رمز خاص (!@#$...)</li>
                </ul>

                <label for="confirm_password">تأكيد كلمة المرور:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>

                <button type="submit" id="submit-btn">إنشاء الحساب</button>
            </form>
        </div>

        <script>
            const passwordInput = document.getElementById("password");
            const form = document.getElementById("register-form");
            const submitBtn = document.getElementById("submit-btn");

            const lengthEl = document.getElementById("length");
            const lowerEl = document.getElementById("lower");
            const upperEl = document.getElementById("upper");
            const numberEl = document.getElementById("number");
            const specialEl = document.getElementById("special");

            function checkPasswordStrength(password) {
                let valid = true;

                // الطول
                if (password.length >= 8) {
                    lengthEl.className = "valid";
                    lengthEl.textContent = "✅ 8 أحرف أو أكثر";
                } else {
                    lengthEl.className = "invalid";
                    lengthEl.textContent = "❌ 8 أحرف أو أكثر";
                    valid = false;
                }

                // حرف صغير
                if (/[a-z]/.test(password)) {
                    lowerEl.className = "valid";
                    lowerEl.textContent = "✅ حرف صغير (a-z)";
                } else {
                    lowerEl.className = "invalid";
                    lowerEl.textContent = "❌ حرف صغير (a-z)";
                    valid = false;
                }

                // حرف كبير
                if (/[A-Z]/.test(password)) {
                    upperEl.className = "valid";
                    upperEl.textContent = "✅ حرف كبير (A-Z)";
                } else {
                    upperEl.className = "invalid";
                    upperEl.textContent = "❌ حرف كبير (A-Z)";
                    valid = false;
                }

                // رقم
                if (/\d/.test(password)) {
                    numberEl.className = "valid";
                    numberEl.textContent = "✅ رقم (0-9)";
                } else {
                    numberEl.className = "invalid";
                    numberEl.textContent = "❌ رقم (0-9)";
                    valid = false;
                }

                // رمز خاص
                if (/[^A-Za-z0-9]/.test(password)) {
                    specialEl.className = "valid";
                    specialEl.textContent = "✅ رمز خاص (!@#$...)";
                } else {
                    specialEl.className = "invalid";
                    specialEl.textContent = "❌ رمز خاص (!@#$...)";
                    valid = false;
                }

                return valid;
            }

            passwordInput.addEventListener("input", () => {
                checkPasswordStrength(passwordInput.value);
            });

            form.addEventListener("submit", (e) => {
                const isStrong = checkPasswordStrength(passwordInput.value);
                if (!isStrong) {
                    e.preventDefault();
                    alert("كلمة المرور غير قوية بما يكفي. يرجى تعديلها.");
                }
            });
        </script>
    </body>
</html>
