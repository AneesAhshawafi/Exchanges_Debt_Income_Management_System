<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'dbconn.php';
include 'total_ammounts_calc.php';
include 'csrf_token.php';
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <!-- تحديد ترميز النص -->
    <meta charset="utf-8" />
    <!-- ضبط التوافق مع إنترنت إكسبلورر -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- عنوان الصفحة مع دعم الترجمة -->
    <title data-lang="page-title">الرئيسية</title>
    <!-- جعل التصميم متجاوباً مع مختلف الشاشات -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="manifest" href="manifest.json" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="shortcut icon" href="/favicon.ico">


    <!-- استيراد التنسيقات العامة للموقع -->
    <link rel="stylesheet" href="CSS/GlobalRulesStyle.css" />
    <!-- تنسيقات الوضع الليلي -->
    <!--  <link rel="stylesheet" href="../CSS/darkMode.css" />-->

    <!-- إعادة ضبط تنسيقات المتصفح الافتراضية -->
    <link rel="stylesheet" type="text/css" media="screen" href="CSS/normalize.css" />
    <!-- مكتبة أيقونات Font Awesome -->
    <link rel="stylesheet" href="CSS/all.min.css" />
    <!-- تنسيقات خاصة بالصفحة الرئيسية -->
    <link rel="stylesheet" href="CSS/indexxStyle.css?v=<?= filemtime('CSS/indexxStyle.css') ?>">

    <!-- إعدادات خطوط Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />
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

                    <li><a class="active" href="index.php"> قسم الحوالات</a></li>
                    <li><a href="debt.php">قسم الديون</a></li>
                    <li><a href="income_list.php">قسم الدخل</a></li>

                    <!-- زر تسجيل الدخول -->
                    <li id="login-li">
                        <a href="logout.php" class="login-buttn">تسجيل الخروج</a>
                    </li>
                </ul>

                <h1>بن عبود للصرافة والتحويلات</h1>
                <!-- شعار الموقع -->
                <a href="#" class="logo">
                    <img src="images/logo2.jpg" alt="logo" />
                </a>
            </nav>
        </section>
    </header>

    <div class="exchanges" id="exchanges">
        <div class="container">
            <!-- عنوان القسم -->
            <h2 class="special-heading">الحوالات والايداع</h2>


            <!-- Start Clients List -->

            <div class="clients-list">
                <div class="clients-list-title">
                    <h2 class="clients-list-title">قائمة العملاء</h2>
                </div>
                <input type="text" id="searchInput" placeholder="🔍 ابحث عن اسم عميل..." class="search-input" />
                <div class="clients-list-container">

                    <div class="clients-list-header">
                        <h3 class="name" id="name">الاسم</h3>
                        <h3 class="phone">الرقم</h3>
                        <h3 class="no-exchanges">الرصيد قعيطي</h3>
                        <h3 class="total-for">الرصيد قديم</h3>
                        <h3 class="total-on">الرصيد سعودي له</h3>
                    </div>

                    <div id="clients-list"></div> <!-- سنملأ هذا بواسطة JavaScript -->

                    <p id="loading-message" style="display:none;">جارٍ التحميل...</p>
                </div>

                <button class="plus-icon open-modal-btn" id="addClientBtn">
                    <i class="fas fa-plus "></i>
                </button>
            </div>

            <!-- End Clients List -->

            <!-- start add-client-form -->

            <div class="add-client-overlay hidden" id="add-client-overlay">
                <div class="add-client">
                    <form action="" method="POST" class="add-client-form" id="add-client-form">
                        <span class="close-modal close-modal-form" id="closeAddClientBtn">&rarr;</span>
                        <div class="add-client-form-title">
                            <h3>اضافة عميل</h3>
                        </div>
                        <!-- إضافة حقل CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="input-group">
                            <label for="client-name">اسم العميل</label>
                            <input type="text" id="client-name" name="client_name" placeholder=" اسم العميل" required />


                        </div>
                        <div class="input-goup">
                            <label for="phone">رقم الجوال</label>
                            <input type="text" id="phone" name="phone" placeholder="رقم الجوال" required />

                            <small id="phoneError" style="color:red; display:none;">
                                رقم الجوال غير صحيح، يجب أن يبدأ بـ 70 أو 71 أو 73 أو 77 ويتكون من 9 أرقام
                            </small>
                        </div>
                        <button class="btn" type="submit" name="submit_client">حفظ</button>
                    </form>
                </div>
            </div>

            <!-- End add-client-form -->


            <!--Start Share Modal-->
            <!--                <div id="shareModal" class="modal hidden">
                                    <div class="modal-content">
                                        <textarea id="shareText" ></textarea>
                                        <button id="share-btn" onclick="navigator.share ? navigator.share({text: document.getElementById('shareText').value}) : alert('المشاركة غير مدعومة');">مشاركة</button>
                                        <button onclick="closeModal('shareModal')">إغلاق</button>
                                    </div>
                                </div>-->
            <!-- Modal تأكيد المشاركة -->
            <div id="confirmShareModal" class="modal hidden">
                <div class="modal-content">
                    <p>هل تريد تحميل ملف PDF لجميع حوالات هذا العميل؟</p>
                    <button id="confirmShareBtn">نعم</button>
                    <button onclick="closeModal('confirmShareModal')">إلغاء</button>
                </div>
            </div>

            <!--End Share Modal-->

            <!-- Start Edit-Client-Form -->

            <div class="edit-client-overlay hidden" id="edit-client-overlay">
                <div class="edit-client">
                    <form action="update_client.php" method="POST" class="edit-client-form">
                        <span class="close-modal close-modal-form" id="closeEditClientBtn">&rarr;</span>
                        <div class="edit-client-form-title">
                            <h3>تعديل بيانات العميل</h3>
                        </div>
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="client-id" id="client-id" />
                        <div class="input-group">
                            <label for="client-name-e">اسم العميل</label>
                            <input type="text" id="client-name-e" name="client-name" placeholder=" اسم العميل"
                                required />
                        </div>
                        <div class="input-group">
                            <label for="phone-e">رقم الجوال</label>
                            <input type="text" id="phone-e" name="phone" placeholder="رقم الجوال" required />
                        </div>
                        <button class="btn" type="submit" name="submit-edit-client">حفظ</button>
                    </form>
                </div>
            </div>

            <!-- End Edit-Client-Form -->


            <!--Start Delete Modal-->
            <div id="deleteModal" class="modal hidden">
                <div class="modal-content">
                    <p>هل حقاً تريد الحذف؟</p>
                    <button id="confirmDeleteBtn">نعم</button>
                    <button onclick="closeModal('deleteModal')">إلغاء</button>
                </div>
            </div>
            <!--End Delete Modal-->

        </div>
    </div>

    <!-- ربط ملفات JavaScript -->
    <script src="JS/navbar.js?v=<?= filemtime('JS/navbar.js') ?>"></script>
    <script src="JS/add_client_modal.js?v=<?= filemtime('JS/add_client_modal.js') ?>"></script>
    <script src="JS/set_current_client_id.js?v=<?= filemtime('JS/set_current_client_id.js') ?>"></script>
    <script src="JS/operations_on_client.js?v=<?= filemtime('JS/operations_on_client.js') ?>"></script>
    <script src="JS/lazy_loading_clients.js?v=<?= filemtime('JS/lazy_loading_clients.js') ?>"></script>
    <script src="JS/add_client_handeler.js?v=<?= filemtime('JS/add_client_handeler.js') ?>"></script>

    <script>
        const searchInput = document.getElementById("searchInput");
        const clientsListDiv = document.getElementById("clients-list");

        searchInput.addEventListener("input", function () {
            const searchValue = this.value.trim();

            fetch("get_clients_search.php?search=" + encodeURIComponent(searchValue))
                .then(res => res.text())
                .then(data => {
                    clientsListDiv.innerHTML = data;
                })
                .catch(err => {
                    console.error("فشل البحث عن العملاء:", err);
                });
        });
    </script>
    <script>
        function openShareClientModal(clientId) {
            document.getElementById("confirmShareModal").classList.remove("hidden");

            const confirmBtn = document.getElementById("confirmShareBtn");

            // إزالة أي حدث سابق لمنع التكرار
            const newBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

            newBtn.addEventListener("click", function () {
                window.open("generate_client_pdf.php?client_id=" + encodeURIComponent(clientId), "_blank");
                closeModal("confirmShareModal");
            });
        }
    </script>
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
    </script>

    //التحقق من رقم الهاتف
    <script>
        const phoneInput = document.getElementById("phone");
        const phoneError = document.getElementById("phoneError");

        const phonePattern = /^(77|73|71|70)[0-9]{7}$/;

        // منع إدخال أي شيء غير الأرقام
        phoneInput.addEventListener("input", () => {
            // حذف أي حرف غير رقمي
            phoneInput.value = phoneInput.value.replace(/[^0-9]/g, "");

            // التحقق من الرقم
            if (phoneInput.value.length === 0) {
                phoneError.style.display = "none";
            } else if (!phonePattern.test(phoneInput.value)) {
                phoneError.style.display = "block";
            } else {
                phoneError.style.display = "none";
            }
        });
    </script>



</body>

</html>