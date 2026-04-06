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
    <link rel="stylesheet" href="CSS/indexxStyle.css?v=<?= filemtime('CSS/indexxStyle.css') ?>">
    <!-- إعدادات خطوط Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />
    <style>
        /* تنسيق الـ Spinner */
        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            /* لون الجزء المتحرك */
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
            /* مسافة بسيطة عن النص */
            vertical-align: middle;
        }

        /* حركة الدوران */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* تحسين شكل الزر عند التعطيل */
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .client-name {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 20px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>


    <!-- Start exchanges List -->

    <div class="exchanges-list-overlay " id="exchanges-list-overlay">
        <div class="exchanges-list" id="exchanges-list">
            <a href="index.php"><span class="close-modal" id="closeExchangeListBtn">&rarr;</span></a>
            <div class="exchanges-list-title">
                <h2>قائمة العمليات (حوالات/ايداع)</h2>
                <h3 id="client-name" class="client-name"> </h3>
            </div>
            <input type="text" id="exchangeSearchInput"
                placeholder="🔍 ابحث عن مرسل، مستلم، رقم حوالة، نوع العملية، التاريخ..." class="search-input" />

            <div class="exchanges-list-container">

                <div class="exchanges-list-header">
                    <h3>نوع العملية</h3>
                    <h3>اسم المرسل/المودع</h3>
                    <h3>رقم المرسل</h3>
                    <h3>المستلم</h3>
                    <h3>رقم المستلم</h3>
                    <h3 class="no-exchanges">رقم الحوالة</h3>
                    <h3>المبلغ</h3>
                    <h3>له/عليه</h3>
                    <h3>التاريخ</h3>
                    <h3>الصراف</h3>
                    <h3>الرسوم</h3>
                    <h3>الرصيد قعيطي</h3>
                    <h3>الرصيد قديم</h3>
                    <h3>الرصيد سعودي</h3>
                    <h3>ملاحظة</h3>
                    <h3>حالة الحوالة</h3>
                </div>
                <div class="exchanges-list-body" id="exchanges-list-body">
                </div>
                <p id="loading-message" style="display:none;">جارٍ التحميل...</p>
            </div>
        </div>
        <button class="plus-icon open-modal-btn" id="addExchangeBtn">
            <i class="fas fa-plus   "></i>
        </button>
        <button class="plus-icon open-modal-btn" id="addSanadBtn" style="top: 20vh; background: linear-gradient(135deg, #ef4444, #dc2626);" title="سند قيد (سحب)">
            <i class="fas fa-minus"></i>
        </button>
    </div>

    <!-- End exchanges List -->

    <?php
    include 'csrf_token.php';
    ?>

    <!-- تضمين الفورمات المنفصلة -->
    <?php include 'forms/type_selector.php'; ?>
    <?php include 'forms/add_hawala_form.php'; ?>
    <?php include 'forms/edit_hawala_form.php'; ?>
    <?php include 'forms/add_deposit_form.php'; ?>
    <?php include 'forms/edit_deposit_form.php'; ?>
    <?php include 'forms/add_transfer_form.php'; ?>
    <?php include 'forms/edit_transfer_form.php'; ?>
    <?php include 'forms/add_sanad_form.php'; ?>
    <?php include 'forms/edit_sanad_form.php'; ?>


    <!--Start Choose Client Modal-->
    <div id="chooseClientModal" class="modal hidden" style="z-index:10">
        <div class="modal-content">
            <div class="shareModalBtns">
                <button id="senderClientBtn" class="btn">مشاركة لصاحب الحساب </button>
                <button id="receiverClientBtn" class="btn">مشاركة للمستلم </button>
                <button onclick="closeModal('chooseClientModal')" class="btn">إغلاق</button>
            </div>
        </div>
    </div>
    <!--End Choose Client Modal-->

    <!--Start Share Modal-->
    <div id="shareModal" class="modal hidden">
        <div class="modal-content">
            <textarea id="shareText"></textarea>
            <div class="shareModalBtns">
                <button id="shareBtn">مشاركة بدون الإجمالي</button>
                <button id="shareWithTotalBtn">مشاركة مع الإجمالي</button>
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

    <script>
        let exchangesListData = new Array();
    </script>
    <script src="JS/forms_handler.js?v=<?= filemtime('JS/forms_handler.js') ?>"></script>
    <script src="JS/operations_on_exchanges.js?v=<?= filemtime('JS/operations_on_exchanges.js') ?>"></script>
    <script src="JS/lazy_loading_exchanges.js?v=<?= filemtime('JS/lazy_loading_exchanges.js') ?>"></script>
    <script src="JS/add_exchange.js?v=<?= filemtime('JS/add_exchange.js') ?>"></script>
    <script src="JS/add_sanad.js?v=<?= filemtime('JS/add_sanad.js') ?>"></script>

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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const clientName = localStorage.getItem("currentClientName");
            if (clientName) {
                document.getElementById("client-name").innerText = "اسم العميل: " + clientName;
            }
        });
    </script>

    </body>

</html>