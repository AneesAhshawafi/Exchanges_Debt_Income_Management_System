<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
include '../csrf_token.php';
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>الحوالات العامة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- إعادة ضبط تنسيقات المتصفح الافتراضية -->
    <link rel="stylesheet" type="text/css" media="screen" href="../CSS/normalize.css" />
    <!-- مكتبة أيقونات Font Awesome -->
    <link rel="stylesheet" href="../CSS/all.min.css" />
    <!-- استيراد التنسيقات العامة للموقع -->
    <link rel="stylesheet" href="../CSS/GlobalRulesStyle.css" />
    <!-- تنسيقات الصفحة الرئيسية -->
    <link rel="stylesheet" href="../CSS/indexxStyle.css?v=<?= filemtime('../CSS/indexxStyle.css') ?>">
    <!-- تنسيقات الحوالات العامة -->
    <link rel="stylesheet" href="../CSS/publicExchangeStyle.css?v=<?= filemtime('../CSS/publicExchangeStyle.css') ?>">

    <!-- إعدادات خطوط Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- رأس الصفحة ويحتوي على الشعار والقائمة -->
    <header>
        <section class="container">
            <nav>
                <!-- أيقونة القائمة المنسدلة (للهواتف) -->
                <i class="fas fa-bars toggle-menu" id="fa-bar"></i>
                <!-- قائمة الروابط -->
                <ul id="menu">
                    <li><a href="../index.php">قسم الحوالات</a></li>
                    <li><a href="../debt.php">قسم الديون</a></li>
                    <li><a href="../income_list.php">قسم الدخل</a></li>
                    <li><a href="../search_transfer.php">بحث حوالة</a></li>
                    <li><a class="active" href="public_exchanges_page.php">الحوالات العامة</a></li>

                    <!-- زر تسجيل الخروج -->
                    <li id="login-li">
                        <a href="../logout.php" class="login-buttn">تسجيل الخروج</a>
                    </li>
                </ul>

                <h1>بن عبود للصرافة والتحويلات</h1>
                <!-- شعار الموقع -->
                <a href="#" class="logo">
                    <img src="../images/logo2.jpg" alt="logo" />
                </a>
            </nav>
        </section>
    </header>

    <!-- قسم الحوالات العامة -->
    <div class="pe-page-overlay">

        <!-- العنوان -->
        <div class="pe-list-title">
            <h2>الحوالات العامة</h2>
        </div>

        <!-- حقل البحث -->
        <input type="text" id="peSearchInput"
            placeholder="🔍 ابحث عن مرسل، مستلم، رقم حوالة، التاريخ..." class="pe-search-input" />

        <!-- رمز CSRF مخفي لاستخدامه في JavaScript -->
        <input type="hidden" id="pe-csrf-token-meta" value="<?php echo generate_csrf_token(); ?>">

        <!-- جدول البيانات -->
        <div class="pe-list-container">

            <!-- رأس الجدول (13 عمود — بدون نوع العملية و له/عليه) -->
            <div class="pe-list-header">
                <h3>العملة</h3>
                <h3>الحالة</h3>
                <h3>المبلغ</h3>
                <h3>التاريخ</h3>
                <h3>المرسل / المودع</h3>
                <h3>رقم المرسل</h3>
                <h3>المستلم</h3>
                <h3>رقم المستلم</h3>
                <h3>رقم الحوالة</h3>
                <h3>الرسوم</h3>
                <h3>ربح الرسوم</h3>
                <h3>الصراف</h3>
                <h3>ملاحظة</h3>
            </div>

            <!-- جسم البيانات (يملأ بواسطة JavaScript) -->
            <div class="pe-list-body" id="pe-list-body">
            </div>

            <!-- رسالة التحميل -->
            <p id="pe-loading-message" class="pe-loading-message" style="display:none;">جارٍ التحميل...</p>
        </div>

        <!-- زر إضافة حوالة عامة (عائم) -->
        <button class="pe-add-btn" id="addPeBtn">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <!-- تضمين نموذج الإضافة (ملف منفصل) -->
    <?php include 'add.php'; ?>

    <!-- تضمين نموذج التعديل (ملف منفصل) -->
    <?php include 'update.php'; ?>

    <!--Start Choose Client Modal (مشاركة للمرسل أو المستلم)-->
    <div id="peChooseClientModal" class="pe-modal-overlay hidden" style="z-index:10">
        <div class="pe-share-choose-container">
            <span class="pe-close-btn" onclick="closePeModal('peChooseClientModal')">&rarr;</span>
            <div class="pe-share-choose-header">
                <i class="fas fa-share-alt pe-share-choose-icon"></i>
                <h3>اختر طرف المشاركة</h3>
                <p>حدد الطرف الذي تريد إرسال تفاصيل الحوالة إليه عبر واتساب</p>
            </div>
            <div class="pe-share-choose-body">
                <button id="peSenderClientBtn" class="pe-share-choose-btn pe-share-sender-btn">
                    <i class="fas fa-paper-plane"></i>
                    <span>مشاركة للمرسل</span>
                </button>
                <button id="peReceiverClientBtn" class="pe-share-choose-btn pe-share-receiver-btn">
                    <i class="fas fa-inbox"></i>
                    <span>مشاركة للمستلم</span>
                </button>
                <button onclick="closePeModal('peChooseClientModal')" class="pe-share-choose-btn pe-share-close-btn">
                    <i class="fas fa-times"></i>
                    <span>إغلاق</span>
                </button>
            </div>
        </div>
    </div>
    <!--End Choose Client Modal-->

    <!--Start Share Modal-->
    <div id="peShareModal" class="pe-modal-overlay hidden" style="z-index:11">
        <div class="pe-share-text-container">
            <span class="pe-close-btn" onclick="closePeModal('peShareModal')">&rarr;</span>
            <div class="pe-share-text-header">
                <i class="fab fa-whatsapp pe-share-wa-icon"></i>
                <h3>نص المشاركة</h3>
            </div>
            <div class="pe-share-text-body">
                <textarea id="peShareText" class="pe-share-textarea" readonly></textarea>
                <div class="pe-share-text-actions">
                    <button id="peShareBtn" class="pe-share-wa-btn">
                        <i class="fab fa-whatsapp"></i>
                        <span>إرسال عبر واتساب</span>
                    </button>
                    <button onclick="closePeModal('peShareModal')" class="pe-share-text-close-btn">
                        <i class="fas fa-arrow-right"></i>
                        <span>رجوع</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--End Share Modal-->

    <!-- ملفات JavaScript -->
    <script src="../JS/navbar.js?v=<?= filemtime('../JS/navbar.js') ?>"></script>
    <script src="../JS/public_exchange_lazy_loading.js?v=<?= filemtime('../JS/public_exchange_lazy_loading.js') ?>"></script>
    <script src="../JS/public_exchange_add.js?v=<?= filemtime('../JS/public_exchange_add.js') ?>"></script>
    <script src="../JS/public_exchange_operations.js?v=<?= filemtime('../JS/public_exchange_operations.js') ?>"></script>

    <!-- البحث المحلي في البيانات المعروضة -->
    <script>
        document.getElementById("peSearchInput").addEventListener("input", function () {
            const searchText = this.value.toLowerCase();
            const items = document.querySelectorAll(".pe-data-container");

            items.forEach(item => {
                const textContent = item.innerText.toLowerCase();
                item.style.display = textContent.includes(searchText) ? "block" : "none";
            });
        });
    </script>

</body>

</html>