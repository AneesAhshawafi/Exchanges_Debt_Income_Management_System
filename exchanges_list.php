<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
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

        <!-- إعادة ضبط تنسيقات المتصفح الافتراضية -->
        <link rel="stylesheet" type="text/css" media="screen" href="CSS/normalize.css" />
        <!-- مكتبة أيقونات Font Awesome -->
        <link rel="stylesheet" href="CSS/all.min.css" />
        <!-- استيراد التنسيقات العامة للموقع -->
        <link rel="stylesheet" href="CSS/GlobalRulesStyle.css" />
        <!-- تنسيقات الوضع الليلي -->
        <!--  <link rel="stylesheet" href="../CSS/darkMode.css" />-->
        <!-- تنسيقات خاصة بالصفحة الرئيسية -->
        <link rel="stylesheet" href="CSS/indexxStyle.css" />

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
                        <h3>المبلغ</h3>
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
                <i class="fas fa-plus   "></i>
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

                    <select class="input-add-exchange" name="type" id="oper-type-input" >
                        <option value="" disabled selected>اختر نوع العملية</option>
                        <option value="حوالة">حوالة</option>
                        <option value="إيداع">إيداع</option>
                        <option value="تحويل">تحويل بين الحسابات</option>
                    </select>
                    <select class="input-add-exchange hidden"  name="currency" id="currency" >
                        <option value="" disabled selected>اختر العملة</option>
                        <option value="new">قعيطي</option>
                        <option value="old">قديم</option>
                        <option value="sa">سعودي</option>
                    </select>
                    <select class="input-add-exchange hidden" name="for-or-on" id="for-or-on" >
                        <option value="" disabled selected>له / عليه</option>
                        <option value="له">له</option>
                        <option value="عليه">عليه</option>
                    </select>
                    <select  class="input-add-exchange hidden" name="status" id="status">
                        <option value="" disabled selected>حالة الحوالة</option>

                        <option value="استلمت">استلمت</option>

                        <option value="لم تستلم">لم تستلم</option>

                    </select>
                    <div class="input-group" id="ammount-input-group">
                        <input class="input-add-exchange" type="text" id="ammount" name="ammount" placeholder="المبلغ" required />
                    </div>
                    <div class="input-group transfer-input-group hidden" id="transfer-input-group">  
                        <select class="input-add-exchange " name="select-from" id="select-from">
                            <option value="" disabled selected >التحويل من العملة</option>
                            <option value="new">القعيطي</option>
                            <option value="old">القديم</option>
                            <option value="sa">السعودي</option>
                        </select>
                        <div class="input-group" id="price-input-group">
                            <input class="input-add-exchange" id="price" name="price" placeholder="السعر" >
                        </div>
                        <select class="input-add-exchange" name="select-to" id="select-to">
                            <option value="" disabled selected >إلى العملة</option>
                            <option value="new">القعيطي</option>
                            <option value="old">القديم</option>
                            <option value="sa">السعودي</option>
                        </select>

                    </div>
                    <div class="input-group hidden" id="sender-input-group" >
                        <input type="text" class="input-add-exchange " id="sender" name="sender-name" placeholder=" المودع"  />
                    </div>
                    <div class="input-group hiddens" id="receiver-input-group" >
                        <input type="text" class="input-add-exchange" id="reciver-input" name="receiver-name" placeholder=" المستلم "  />
                    </div>

                    <div class="input-group "  id="transfer-no-input-group">
                        <input type="text" class="input-add-exchange" id="transfer-no" name="transfer-no" placeholder="رقم الحوالة " />
                    </div>


                    <div class="input-group ">
                        <input type="text" class="input-add-exchange " id="fees" name="fees" placeholder="الرسوم" >
                    </div>
                    <div class="input-group">
                        <label for="date">التاربخ</label>
                        <input type="date" class="input-add-exchange " id="date" name="tra-date" placeholder="التاريخ والوقت" />
                    </div>
                    <div class="input-group">
                        <input type="text" class="input-add-exchange" id="atm" name="atm" placeholder="الصراف" required />
                    </div>

                    <div class="input-group">
                        <input class="input-add-exchange" type="text" id="note" name="note" placeholder=" ملاحظة" />
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
                    <span class="close-modal close-modal-form" id="closeEditExchangeListBtn">&rarr;</span>
                    <div class="edit-exchange-title">
                        <h3>تعديل بيانات العملية</h3>
                    </div>
                    <input type="hidden" name="exchange_id" id="edit-exchange-id" />
                    <div class="edit-exchange-form-body">
                        <div class="input-group" >
                            <label for="edit-type">اختر نوع العملية</label>
                            <select name="type"  id="edit-type"  >
                                <option value="" disabled selected>اختر نوع العملية</option>
                                <option value="حوالة">حوالة</option>
                                <option value="إيداع">إيداع</option>
                                <option value="تحويل">تحويل بين الحسابات</option>
                            </select>
                        </div>
                        <div class="input-group hidden" id="edit-currency-input-grp">
                            <label for="edit-currency">اختر العملة</label>
                            <select  name="currency" id="edit-currency" >
                                <option value="" disabled selected>اختر العملة</option>
                                <option value="new">قعيطي</option>
                                <option value="old">قديم</option>
                                <option value="sa">سعودي</option>
                            </select>
                        </div>
                        <div class="input-group hidden" id="edit-for-or-on-input-grp">
                            <label for="edit-for-or-on">له / عليه</label>
                            <select  name="for-or-on" id="edit-for-or-on" >
                                <option value="" disabled selected>له / عليه</option>
                                <option value="له">له</option>
                                <option value="عليه">عليه</option>
                            </select>
                        </div>
                        <div class="input-group" id="edit-ammount-input-group">
                            <label for="edit-ammount">المبلغ</label>
                            <input  type="number" name="ammount" id="edit-ammount" placeholder="المبلغ"  />
                        </div>


                        <div class="input-group hidden" id="edit-status-input-grp" >
                            <label for="edit-status">حالة الحوالة</label>
                            <select  name="status" id="edit-status">
                                <option value="" disabled selected>حالة الحوالة</option>
                                <option value="استلمت">استلمت</option>
                                <option value="لم تستلم">لم تستلم</option>
                            </select>
                        </div>


                        <!--<div class="input-group transfer-input-group" id="edit-transfer-input-group">-->

                        <div class="input-group edit-transfer-input-group hidden" >
                            <label for="edit-select-from">التحويل من العملة</label>
                            <select class="" id="edit-select-from" readonly>
                                <option value="" disabled selected>التحويل من العملة</option>
                                <option value="new">القعيطي</option>
                                <option value="old">القديم</option>
                                <option value="sa">السعودي</option>
                            </select>
                        </div>
                        <div class="input-group edit-transfer-input-group hidden" id="edit-price-input-group">
                            <label for="price">السعر</label>
                            <input id="edit-price" name="price" placeholder="السعر" readonly >
                        </div>

                        <div class="input-group edit-transfer-input-group hidden" >
                            <label for="edit-select-to">الى العملة</label>
                            <select class="" name="select-to" id="edit-select-to" readonly>
                                <option value="" disabled selected>الى العملة</option>
                                <option value="new">القعيطي</option>
                                <option value="old">القديم</option>
                                <option value="sa">السعودي</option>
                            </select>
                        </div>
                        <!--</div>-->
                        <div class="input-group hidden" id="edit-sender-input-group">
                            <label for="edit-sender" id="label-edit-sender">المودع</label>
                            <input type="text" name="sender" id="edit-sender" placeholder="المودع"  />
                        </div>
                        <div class="input-group hidden" id="edit-receiver-input-group" >
                            <label for="reciver">المستلم</label>
                            <input type="text" id="reciver" name="receiver-name" placeholder="المستلم"  />
                        </div>

                        <div class="input-group"  id="edit-transfer-no-input-group">
                            <label for="edit-transfer-no" id="label-edit-transfer-no">رقم الحوالة </label>
                            <input type="text" id="edit-transfer-no" name="transfer-no" placeholder="رقم الحوالة " readonly/>
                        </div>
                        <div class="input-group">
                            <label for="edit-date">التاربخ</label>
                            <input class="date" type="date" name="date" id="edit-date" placeholder="التاريخ والوقت" />
                        </div>
                        <div class="input-group" id="edit-fees-input-grp">
                            <label for="edit-fees">الرسوم</label>
                            <input type="number" name="fees" id="edit-fees" placeholder="الرسوم" readonly >
                        </div>
                        <div class="input-group">
                            <label for="edit-atm">الصراف</label>
                            <input type="text" name="atm" id="edit-atm" placeholder="الصراف" required />
                        </div>
                        <div class="input-group">
                            <label for="edit-note">ملاحظة</label>
                            <input type="text" id="edit-note" name="note" placeholder=" ملاحظة" />
                        </div>


                        <button class="btn" type="submit" name="submit-edit-exchange">تحديث</button>
                    </div>
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


        <script src="JS/switch_withdraw_exchange.js"></script>
        <script src="JS/exchanges_list_modal.js"></script>
        <script src="JS/add_exchange.js"></script>
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