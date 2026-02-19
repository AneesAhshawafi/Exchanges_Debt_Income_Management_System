<?php
// إعدادات وقت حياة الجلسة (اختياري - هنا مضبوطة ليوم واحد)
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_lifetime', 86400);

// منع الجافاسكربت من الوصول للكوكي (حماية XSS)
ini_set('session.cookie_httponly', 1);

// استخدام الكوكيز فقط لنقل رقم الجلسة
ini_set('session.use_only_cookies', 1);

// منع تثبيت الجلسة (Session Fixation)
ini_set('session.use_strict_mode', 1);

// التحقق من بروتوكول الاتصال (HTTP أو HTTPS)
$secure = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $secure = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
    $secure = true;
}

// تفعيل Secure Cookies فقط إذا كان الموقع يعمل بـ HTTPS
ini_set('session.cookie_secure', $secure ? 1 : 0);

// ضبط SameSite ليكون Lax لضمان عمل الجلسة بشكل طبيعي مع الروابط
ini_set('session.cookie_samesite', 'Lax');

// تسمية الجلسة باسم مميز
session_name('MY_APP_SESSION');

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تجديد معرف الجلسة دورياً لمنع السرقة
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} else {
    $interval = 60 * 30; // تجديد كل 30 دقيقة
    if (time() - $_SESSION['last_regeneration'] >= $interval) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}