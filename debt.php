<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'dbconn.php';
include 'total_ammounts_calc.php';
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

        <!-- إعادة ضبط تنسيقات المتصفح الافتراضية -->
        <link rel="stylesheet" type="text/css" media="screen" href="CSS/normalize.css" />
        <!-- مكتبة أيقونات Font Awesome -->
        <link rel="stylesheet" href="CSS/all.min.css" />
        <!-- استيراد التنسيقات العامة للموقع -->
        <link rel="stylesheet" href="CSS/GlobalRulesStyle.css" />
        <!-- تنسيقات الوضع الليلي -->
        <!--  <link rel="stylesheet" href="../CSS/darkMode.css" />-->
        <!-- تنسيقات خاصة بالصفحة الرئيسية -->
 <link rel="stylesheet" href="CSS/indexxStyle.css?v=<?=filemtime('CSS/indexxStyle.css')?>">
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

                        <li><a  href="index.php"> قسم الحوالات</a></li>
                        <li><a class="active" href="debt.php">قسم الديون</a></li>
                        <li><a href="income_list.php">قسم الدخل</a></li>

                        <!-- زر تسجيل الدخول -->   <li id="login-li">
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

        <div class="debts" id="debts">
            <div class="container">
                <!-- عنوان القسم -->
                <h2 class="special-heading">قسم إدارة الديون</h2>


                <!-- Start Clients List -->

                <div class="clients-list">
                    <div class="clients-list-title">
                        <h2 class="clients-list-title">قائمة العملاء</h2>
                    </div>
                    <input type="text" id="searchInput" placeholder="🔍 ابحث عن اسم عميل..." class="search-input" />
                    <div class="clients-list-container" >
                        
                    <div class="clients-list-header">
                        <h3 class="name" id="name">الاسم</h3>
                        <h3 class="no-exchanges">الإجمالي قعيطي</h3>
                        <h3 class="total-for">الإجمالي قديم</h3>
                        <h3 class="total-on">الإجمالي سعودي</h3>
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
                        <form action="" method="POST" class="add-client-form" id="debt-add-client-form">
                            <span class="close-modal close-modal-form" id="closeAddClientBtn">&rarr;</span>
                            <div class="add-client-form-title">
                                <h3>اضافة عميل</h3>
                            </div>
                            <div class="input-group">
                                <label for="client-name-e">اسم العميل</label>
                                <input type="text" id="client-name" name="client_name" placeholder=" اسم العميل" required />
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
                        <p>هل تريد تحميل ملف PDF لسجل ديون هذا العميل؟</p>
                        <button id="confirmShareBtn">نعم</button>
                        <button onclick="closeModal('confirmShareModal')">إلغاء</button>
                    </div>
                </div>

                <!--End Share Modal-->

                <!-- Start Edit-Client-Form -->

                <div class="edit-client-overlay hidden" id="edit-client-overlay">
                    <div class="edit-client">
                        <form action="debt_update_client.php" method="POST" class="edit-client-form">
                            <span class="close-modal close-modal-form" id="closeEditClientBtn">&rarr;</span>
                            <div class="edit-client-form-title">
                                <h3>تعديل اسم العميل</h3>
                            </div>
                            <input type="hidden" name="client-id" id="client-id" />
                            <div class="input-group">
                                <label for="client-name-e">اسم العميل</label>
                                <input type="text" id="client-name-e" name="client-name" placeholder=" اسم العميل" required />
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

        <script src="JS/navbar.js"></script>
        <!-- تحكم في فتح/إغلاق القائمة -->
        <!--<script src="JS/language.js"></script>-->
        <!--<script src="JS/loginLogoutBtn.js"></script>-->
        <!-- زر تسجيل لدخول / تسجيل الخروج -->
        <!--            <script src="JS/variables.js"></script>-->
        <script src="JS/add_client_modal.js"></script>
        
        <script src="JS/debt_set_current_client.js"></script>
        <script src="JS/operations_on_client.js"></script>
        <script src="JS/debt_lazy_loading_clients.js"></script>
        <script src="JS/debt_add_client_handeler.js"></script>
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
                    window.open("debt_generate_client_pdf.php?client_id=" + encodeURIComponent(clientId), "_blank");
                    closeModal("confirmShareModal");
                });
            }


        </script>
    </body>

</html>
