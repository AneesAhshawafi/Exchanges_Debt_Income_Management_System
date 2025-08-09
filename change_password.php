<?php
require_once "dbconn.php";

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // جلب المستخدم من قاعدة البيانات
    $stmt = $conn->prepare("SELECT USER_ID, PASSWORD FROM users WHERE USER_NAME = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $error = "اسم المستخدم غير موجود.";
    } elseif (!password_verify($current_password, $row["PASSWORD"])) {
        $error = "كلمة المرور الحالية غير صحيحة.";
    } elseif ($new_password !== $confirm_password) {
        $error = "كلمة المرور الجديدة وتأكيدها غير متطابقتين.";
    } elseif (strlen($new_password) < 8) {
        $error = "كلمة المرور الجديدة ضعيفة جداً.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET PASSWORD = ? WHERE USER_ID = ?");
        $update_stmt->bind_param("si", $hashed_password, $row["USER_ID"]);
        if($update_stmt->execute()){    
        $success = "✅ تم تغيير كلمة المرور بنجاح.";
        header("Location: login.php");
        exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تغيير كلمة المرور</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="CSS/change_password.css">
</head>
<body>
<div class="container">
    <h2>تغيير كلمة المرور</h2>

    <?php if ($error): ?>
        <div class="msg error"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="msg success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" action="" onsubmit="return validateForm()">
        <label for="username">اسم المستخدم:</label>
        <input type="text" name="username" id="username" required>

        <label for="current_password">كلمة المرور الحالية:</label>
        <input type="password" name="current_password" id="current_password" required>

        <label for="new_password">كلمة المرور الجديدة:</label>
        <input type="password" name="new_password" id="new_password" required>
        <div id="password-strength" class="password-strength"></div>

        <label for="confirm_password">تأكيد كلمة المرور الجديدة:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit" class="btn" id="submitBtn">تغيير</button>
    </form>
</div>

<script>
    const newPassInput = document.getElementById("new_password");
    const strengthDiv = document.getElementById("password-strength");
    const submitBtn = document.getElementById("submitBtn");

    function checkStrength(pw) {
        let score = 0;
        if (pw.length >= 8) score++;
        if (/[a-z]/.test(pw)) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/\d/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return score;
    }

    newPassInput.addEventListener("input", function () {
        const val = newPassInput.value;
        const strength = checkStrength(val);

        if (strength <= 2) {
            strengthDiv.textContent = "كلمة المرور ضعيفة ❌";
            strengthDiv.className = "password-strength weak";
            submitBtn.disabled = true;
        } else if (strength <= 4) {
            strengthDiv.textContent = "كلمة المرور متوسطة ⚠️";
            strengthDiv.className = "password-strength medium";
            submitBtn.disabled = true;
        } else {
            strengthDiv.textContent = "كلمة المرور قوية ✅";
            strengthDiv.className = "password-strength strong";
            submitBtn.disabled = false;
        }
    });

    function validateForm() {
        return !submitBtn.disabled;
    }
</script>
</body>
</html>
