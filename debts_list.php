<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
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
        <link rel="stylesheet" href="CSS/indexxStyle.css?v=<?= filemtime('CSS/indexxStyle.css') ?>">

        <!-- إعدادات خطوط Google -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
              rel="stylesheet" />
    </head>

    <body>


        <!-- Start debt List -->

        <div class="exchanges-list-overlay " id="exchanges-list-overlay">
            <div class="exchanges-list" id="exchanges-list">
                <a href="debt.php" ><span class="close-modal" id="closeExchangeListBtn">&rarr;</span></a> 
                <div class="exchanges-list-title">
                    <h2>قائمة الديون </h2>
                </div>
                <input type="text" id="exchangeSearchInput" placeholder="🔍 ابحث بإسم الغرض..." class="search-input" />

                <div class="exchanges-list-container" >

                    <div class="debt-list-header">
                        <h3 >الغرض</h3>
                        <h3>المبلغ</h3>
                        <h3 >له/عليه</h3>
                        <h3>التاريخ</h3>
                        <h3>الاجمالي قعيطي عليه</h3>
                        <h3>الاجمالي قديم عليه</h3>
                        <h3>الإجمالي سعودي عليه</h3>
                        <h3>ملاحظة</h3>
                    </div>
                    <div class="exchanges-list-body" id="exchanges-list-body">

                    </div>
                    <p id="loading-message" style="display:none;">جارٍ التحميل...</p>
                </div>
            </div>
            <button class="plus-icon open-modal-btn" id="addExchangeBtn">
                <i class="fas fa-plus "></i>
            </button>
        </div>

        <!-- End exchanges List -->

        <!-- Start Add Debt Form -->

        <div id="addExchangeForm" class="modal-overlay hidden">
            <div class="add-exchange">
                <form class="add-exchange-form" id="add-exchange-form" action="" method="POST">
                    <span class="close-modal close-modal-form" id="closeAddExchangeBtn">&rarr;</span>
                    <div class="add-exchange-title">
                        <h3>اضافة غرض</h3>
                    </div>
                    <select name="currency" required>
                        <option value="" disabled selected>اختر العملة</option>
                        <option value="new">قعيطي</option>
                        <option value="old">قديم</option>
                        <option value="sa">سعودي</option>
                    </select>
                    <select name="for-or-on" id="for-or-on" required>
                        <option value="" disabled selected>له / عليه</option>
                        <option value="له">له</option>
                        <option value="عليه">عليه</option>
                    </select>
                    <div class="input-group"  >
                        <input type="text" id="description" name="description" placeholder="الغرض">
                    </div>
                    <div class="input-group">
                        <input type="text" id="ammount" name="ammount" placeholder="المبلغ" required />
                    </div>
                    <div class="input-group">
                        <label for="date">التاربخ</label>
                        <input type="date" id="date" name="date" placeholder="التاريخ والوقت" />
                    </div>
                    <div class="input-group">
                        <input type="text" id="note" name="note" placeholder=" ملاحظة" />
                    </div>

                    <button class="btn" type="submit" name="submit-exchange">حفظ</button>
                </form>
            </div>
        </div>

        <!-- End Add Debt Form -->



        <!--Start Edit Exchange Form-->
        <div id="editExchangeModal" class="modal-overlay    hidden">

            <div class="edit-exchangef">

                <form class="edit-exchange-form" id="edit-exchange-form" action="update_exchange.php" method="POST">

                    <span class="close-modal" id="closeEditExchangeListBtn">&rarr;</span>

                    <div class="edit-exchange-title">



                        <h3>تعديل غرض</h3>

                    </div>

                    <input type="hidden" name="debt-id" id="edit-exchange-id" />



                    <!-- باقي الحقول -->



                    <select name="currency" id="edit-currency" required>

                        <option value="" disabled selected>اختر العملة</option>

                        <option value="new">قعيطي</option>

                        <option value="old">قديم</option>

                        <option value="sa">سعودي</option>

                    </select>

                    <select name="for-or-on" id="edit-for-or-on" required>

                        <option value="" disabled selected>له / عليه</option>

                        <option value="له">له</option>

                        <option value="عليه">عليه</option>

                    </select>
                    <div class="input-group"  >
                        <input type="text" id="edit-description" name="description" placeholder="الغرض">
                    </div>
                    <div class="input-group">


                        <input  type="number" name="ammount" id="edit-ammount" placeholder="المبلغ" required />

                    </div>



                    <div class="input-group">
                        <label for="edit-date">التاربخ</label>
                        <input class="date" type="date" name="date" id="edit-date" placeholder="التاريخ والوقت" />

                    </div>



                    <div class="input-group">


                        <input type="text" id="edit-note" name="note" placeholder=" ملاحظة" />

                    </div>

                    <button class="btn" type="submit" name="submit-edit-exchange">تحديث</button>

                </form>

            </div>

        </div>



        <!--End Edit Exchange Form-->




        <!--Start Share Modal-->
        <div id="shareModal" class="modal hidden">
            <div class="modal-content">
                <textarea id="shareText" ></textarea>
                <div class="shareModalBtns">     
                    <button id="shareBtn" >مشاركة بدون الإجمالي</button>
                    <button id="shareWithTotalBtn" >مشاركة مع الإجمالي</button>
                    <button onclick="closeModal('shareModal')">إغلاق</button>
                </div>
            </div>
        </div>
        <!--End Share Modal-->

        <!--Start Delete Modal-->
        <div id="deleteModal" class="modal hidden">
            <div class="modal-content">
                <p>هل أنت متأكد من حذف الغرض؟</p>
                <button id="confirmDeleteBtn">نعم</button>
                <button onclick="closeModal('deleteModal')">إلغاء</button>
            </div>
        </div>
        <!--End Delete Modal-->


        <script>
            let exchangesListData = new Array();
        </script>
        <script src="JS/operations_on_debts.js?v=<?= filemtime('JS/operations_on_debts.js') ?>"></script>
        <script src="JS/lazy_loading_debts.js?v=<?= filemtime('JS/lazy_loading_debts.js') ?>"></script>
        <script src="JS/add_debt.js?v=<?= filemtime('JS/add_debt.js') ?>"></script>

        <script>
            document.getElementById("exchangeSearchInput").addEventListener("input", function () {
                const searchText = this.value.toLowerCase();
                const exchangeItems = document.querySelectorAll(".exchanges-data-container");

                exchangeItems.forEach(item => {
                    const textContent = item.innerText.toLowerCase();
                    item.style.display = textContent.includes(searchText) ? "block" : "none";
                });
            });

        </script>

    </<body>

</html>