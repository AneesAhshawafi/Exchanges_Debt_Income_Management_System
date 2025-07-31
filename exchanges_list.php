<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
?>
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

        <!-- استيراد التنسيقات العامة للموقع -->
        <link rel="stylesheet" href="CSS/GlobalRulesStyle.css" />
        <!-- تنسيقات الوضع الليلي -->
        <!--  <link rel="stylesheet" href="../CSS/darkMode.css" />-->
        <!-- تنسيقات خاصة بالصفحة الرئيسية -->
        <link rel="stylesheet" href="CSS/indexxStyle.css" />
        <!-- إعادة ضبط تنسيقات المتصفح الافتراضية -->
        <link rel="stylesheet" type="text/css" media="screen" href="CSS/normalize.css" />
        <!-- مكتبة أيقونات Font Awesome -->
        <link rel="stylesheet" href="CSS/all.min.css" />

        <!-- إعدادات خطوط Google -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
              rel="stylesheet" />
    </head>

    <body>


        <!-- Start exchanges List -->

        <div class="exchanges-list-overlay " id="exchanges-list-overlay">
            <div class="exchanges-list" id="exchanges-list">
                <a href="index.php" ><span class="close-modal" id="closeExchangeListBtn">&rarr;</span></a> 
                <div class="exchanges-list-title">
                    <h2>قائمة العمليات (حوالات/ايداع)</h2>
                </div>
                <input type="text" id="exchangeSearchInput" placeholder="🔍 ابحث عن مرسل، مستلم، رقم حوالة، نوع العملية، التاريخ..." class="search-input" />

                <div class="exchanges-list-container" >

                    <div class="exchanges-list-header">
                        <h3 >اسم المرسل/المودع</h3>
                        <h3>المستلم</h3>
                        <h3>نوع العملية</h3>
                        <h3 class="no-exchanges">رقم الحوالة</h3>
                        <h3>المبلغ قعيطي</h3>
                        <h3>المبلغ قديم</h3>
                        <h3>المبلغ سعودي</h3>
                        <h3 >له/عليه</h3>
                        <h3>التاريخ</h3>
                        <h3>الصراف</h3>
                        <h3>الرسوم</h3>
                        <h3>الاجمالي قعيطي له</h3>
                        <h3>الاجمالي قديم له</h3>
                        <h3>الإجمالي سعودي له</h3>
                        <h3>ملاحظة</h3>
                        <h3>حالة الحوالة</h3>
                    </div>
                    <div class="exchanges-list-body" id="exchanges-list-body">

                    </div>
                </div>
            </div>
            <button class="plus-icon open-modal-btn" id="addExchangeBtn">
                <i class="fas fa-plus fa-2x"></i>
            </button>
        </div>

        <!-- End exchanges List -->

        <!-- Start Add Exchange Form -->

        <div id="addExchangeForm" class="modal-overlay hidden">
            <div class="add-exchange">
                <form class="add-exchange-form" id="add-exchange-form" action="" method="POST">
                    <span class="close-modal close-modal-form" id="closeAddExchangeBtn">&rarr;</span>
                    <div class="add-exchange-title">
                        <h3>اضافة عملية حوالة/ايداع</h3>
                    </div>

                    <select name="type" id="oper-type-input" required>
                        <option value="" disabled selected>اختر نوع العملية</option>
                        <option value="حوالة">حوالة</option>
                        <option value="إيداع">إيداع</option>
                        <!--<option value="transfer_btwn_accounts">تحويل بين الحسابات</option>-->
                    </select>
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
                    <select class="hidden" name="status" id="status">
                        <option value="" disabled selected>حالة الحوالة</option>

                        <option value="استلمت">استلمت</option>

                        <option value="لم تستلم">لم تستلم</option>

                    </select>
                    <div class="input-group" id="sender-input-group" >
                        <label for="sender" id="sender-input-label">المودع</label>
                        <input type="text" id="sender" name="sender-name" placeholder=" المودع" required />
                    </div>
                    <div class="input-group " id="reciver-input-group" >
                        <label for="reciver"> المستلم</label>
                        <input type="text" id="reciver-input" name="receiver-name" placeholder=" المستلم "  />
                    </div>

                    <div class="input-group hidden"  id="transfer-no-input-group">
                        <label for="transfer-no">رقم الحوالة</label>
                        <input type="text" id="transfer-no" name="transfer-no" placeholder="رقم الحوالة " />
                    </div>
                    <div class="input-group">
                        <label for="ammount">المبلغ</label>
                        <input type="text" id="ammount" name="ammount" placeholder="المبلغ" required />
                    </div>

                    <div class="input-group">
                        <label for="fees">الرسوم</label>
                        <input type="text" id="fees" name="fees" placeholder="الرسوم" >
                    </div>
                    <div class="input-group">
                        <label for="date">التاريخ والوقت</label>
                        <input type="date" id="date" name="tra-date" placeholder="التاريخ والوقت" />
                    </div>
                    <div class="input-group">
                        <label for="atm">الصراف</label>
                        <input type="text" id="atm" name="atm" placeholder="الصراف" required />
                    </div>

                    <div class="input-group">
                        <label for="note">ملاحظة</label>
                        <input type="text" id="note" name="note" placeholder=" ملاحظة" />
                    </div>

                    <button class="btn" type="submit" name="submit-exchange">حفظ</button>
                </form>
            </div>
        </div>

        <!-- End Add Exchange Form -->



        <!--Start Edit Exchange Form-->
        <div id="editExchangeModal" class="modal-overlay    hidden">

            <div class="edit-exchangef">

                <form class="edit-exchange-form" id="edit-exchange-form" action="update_exchange.php" method="POST">

                    <span class="close-modal" id="closeEditExchangeListBtn">&rarr;</span>

                    <div class="edit-exchange-title">



                        <h3>تعديل بيانات عملية إيداع/حوالة</h3>

                    </div>

                    <input type="hidden" name="exchange_id" id="edit-exchange-id" />



                    <!-- باقي الحقول -->



                    <select name="type"  id="edit-type" required>

                        <option value="" disabled selected>اختر نوع العملية</option>

                        <option value="حوالة">حوالة</option>

                        <option value="إيداع">إيداع</option>

                        <!--<option value="transfer_btwn_accounts">تحويل بين الحسابات</option>-->

                    </select>

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
                    <select class="hidden" name="status" id="edit-status">
                        <option value="" disabled selected>حالة الحوالة</option>

                        <option value="استلمت">استلمت</option>

                        <option value="لم تستلم">لم تستلم</option>

                    </select>

                    <div class="input-group" id="edit-sender-input-group">

                        <label for="sender" id="edit-sender-input-label">المودع</label>

                        <input type="text" name="sender" id="edit-sender" placeholder="المودع" required />
                    </div>
                    <div class="input-group " id="edit-reciver-input-group" >
                        <label for="reciver"> المستلم</label>
                        <input type="text" id="reciver" name="receiver-name" placeholder=" المستلم "  />
                    </div>


                    <div class="input-group hidden"  id="edit-transfer-no-input-group">
                        <label for="transfer-no">رقم الحوالة</label>
                        <input type="text" id="edit-transfer-no" name="transfer-no" placeholder="رقم الحوالة " />
                    </div>


                    <div class="input-group">

                        <label for="ammount">المبلغ</label>

                        <input  type="number" name="ammount" id="edit-ammount" placeholder="المبلغ" required />

                    </div>



                    <div class="input-group">

                        <label for="fees">الرسوم</label>

                        <input type="number" name="fees" id="edit-fees" placeholder="الرسوم" required>

                    </div>

                    <div class="input-group">

                        <label for="date">التاريخ والوقت</label>

                        <input class="date" type="date" name="date" id="edit-date" placeholder="التاريخ والوقت" />

                    </div>

                    <div class="input-group">

                        <label for="atm">الصراف</label>

                        <input type="text" name="atm" id="edit-atm" placeholder="الصراف" required />

                    </div>



                    <div class="input-group">

                        <label for="note">ملاحظة</label>

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
                <p>هل أنت متأكد من حذف العملية؟</p>
                <button id="confirmDeleteBtn">نعم</button>
                <button onclick="closeModal('deleteModal')">إلغاء</button>
            </div>
        </div>
        <!--End Delete Modal-->


        <script src="JS/exchanges_list_modal.js"></script>
        <script src="JS/add_exchange.js"></script>
        <script src="JS/switch_withdraw_exchange.js"></script>
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