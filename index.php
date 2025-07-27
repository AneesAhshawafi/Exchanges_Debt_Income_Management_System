<?php
include 'dbconn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //add client
    $client_name = $_POST["client_name"];
    $sql_add_client = "INSERT INTO CLIENT (CLIENT_NAME, DEPT_NO, USER_ID) VALUES ('$client_name', 1, 1)";
    mysqli_query($conn, $sql_add_client);
    header("Location: index.php");
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
        <!-- رأس الصفحة ويحتوي على الشعار والقائمة -->
        <header>
            <section class="container">
                <nav>
                    <!-- أيقونة القائمة المنسدلة (للهواتف) -->
                    <i class="fas fa-bars toggle-menu" id="fa-bar"></i>
                    <!-- قائمة الروابط -->
                    <ul id="menu">

                        <li><a class="active" href="index.html"> قسم الحوالات</a></li>
                        <li><a href="debt.html">قسم الديون</a></li>
                        <li><a href="income.html">قسم الدخل</a></li>

                        <!-- زر تسجيل الدخول -->
                        <li id="login-li">
                            <a href="login.html" data-lang="login-btn" class="login-buttn" id="login-link">تسجيل الدخول</a>
                        </li>
                    </ul>
                </nav>
                <!-- شعار الموقع -->
                <a href="#" class="logo">
                    <img src="images/logo.png" alt="logo" />
                </a>
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

                    <div class="clients-list-header">
                        <h3 class="name" id="name">الاسم</h3>
                        <h3 class="no-exchanges">الإجمالي قعيطي له</h3>
                        <h3 class="total-for">الإجمالي قديم له</h3>
                        <h3 class="total-on">الإجمالي سعودي له</h3>
                    </div>


                    <?php
                    // جلب بيانات العملاء
                    $sql_client_list = "SELECT CLIENT_ID, CLIENT_NAME FROM CLIENT";

                    $result = $conn->query($sql_client_list);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {

                            $resualt_sum_ammount_new = $conn->query("SELECT SUM(AMMOUNT) as total FROM TRANSACTION WHERE CURRENCY ='new'and CLIENT_ID= " . $row['CLIENT_ID']);
                            $resualt_sum_ammount_old = $conn->query("SELECT SUM(AMMOUNT) as total FROM TRANSACTION WHERE CURRENCY ='old' and CLIENT_ID= " . $row['CLIENT_ID']);
                            $resualt_sum_ammount_sa = $conn->query("SELECT SUM(AMMOUNT) as total FROM TRANSACTION WHERE CURRENCY ='sa' and CLIENT_ID= " . $row['CLIENT_ID']);
                            $row_new = $resualt_sum_ammount_new->fetch_assoc();
                            $sum_ammount_new = is_null($row_new['total']) ? 0 : $row_new['total'];

                            $row_old = $resualt_sum_ammount_old->fetch_assoc();
                            $sum_ammount_old = is_null($row_old['total']) ? 0 : $row_old['total'];

                            $row_sa = $resualt_sum_ammount_sa->fetch_assoc();
                            $sum_ammount_sa = is_null($row_sa['total']) ? 0 : $row_sa['total'];

                            echo '<div class="clients-data-container" data-id="clientData';
                            echo htmlspecialchars($row["CLIENT_ID"]) . '">';
                            ?>
                            <div class="oper">
                                <i class="fas fa-trash-alt fa-1x"></i>
                                <i class="fas fa-edit fa-1x"> </i>
                            </div>
                            <div class="clients-data" >
                                <h3 class="name" id="name">            
                                    <?php
                                    echo htmlspecialchars($row["CLIENT_NAME"]);
                                    echo "</h3> <h3 >$sum_ammount_new</h3><h3 >$sum_ammount_old</h3><h3 >$sum_ammount_sa</h3> </div> </div>";
                                }
                            } else {
                                echo '<p>لا يوجد عملاء.</p>';
                            }

                            $conn->close();
                            ?>


                            <button class="plus-icon open-modal-btn" id="addClientBtn">
                                <i class="fas fa-plus fa-2x"></i>
                            </button>
                    </div>

                    <!-- End Clients List -->

                    <!-- start add-client-form -->

                    <div class="add-client-overlay hidden" id="add-client-overlay">
                        <div class="add-client">
                            <form action="" method="POST" class="add-client-form">
                                <span class="close-modal" id="closeAddClientBtn">&times;</span>
                                <div class="add-client-form-title">
                                    <h3>اضافة عميل</h3>
                                </div>
                                <div class="input-group">
                                    <label for="client">اسم العميل</label>
                                    <input type="text" id="client" name="client_name" placeholder=" اسم العميل" required />
                                </div>

                                <button class="btn" type="submit" name="submit_client">حفظ</button>
                            </form>
                        </div>
                    </div>

                    <!-- End add-client-form -->

                    <!-- Start exchanges List -->

                    <div class="exchanges-list-overlay hidden" id="exchanges-list-overlay">
                        <div class="exchanges-list" id="exchanges-list">
                            <span class="close-modal" id="closeExchangeListBtn">&times;</span>
                            <div class="exchanges-list-title">
                                <h2>قائمة العمليات (حوالات/ايداع)</h2>
                            </div>
                            <div class="exchanges-list-container" >


                                <div class="exchanges-list-header">
                                    <h3 >اسم المرسل/المودع</h3>
                                    <h3>نوع العملية</h3>
                                    <h3 class="no-exchanges">رقم الحوالة</h3>
                                    <h3>المبلغ قعيطي</h3>
                                    <h3>المبلغ قديم</h3>
                                    <h3>المبلغ سعودي</h3>
                                    <h3 >له/عليه</h3>
                                    <h3>التاريخ</h3>
                                    <h3>الصراف</h3>
                                    <h3>الاجمالي قعيطي</h3>
                                    <h3>الاجمالي قديم</h3>
                                    <h3>الإجمالي سعودي</h3>
                                    <h3>ملاحظة</h3>
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
                                <span class="close-modal" id="closeAddExchangeBtn">&times;</span>
                                <div class="add-exchange-title">
                                    <h3>اضافة عملية حوالة/ايداع</h3>
                                </div>
                                <select name="type" required>
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
                                <div class="input-group">
                                    <label for="sender">المرسل</label>
                                    <input type="text" id="sender" name="sender-name" placeholder=" اسم المرسل او المودع" required />
                                </div>
                                <div class="input-group">
                                    <label for="id-exchange">رقم الحوالة</label>
                                    <input type="text" id="id-exchange" name="transfer-no" placeholder="رقم الحوالة " />
                                </div>
                                <div class="input-group">
                                    <label for="ammount">المبلغ</label>
                                    <input type="text" id="ammount" name="ammount" placeholder="المبلغ" required />
                                </div>

                                <div class="input-group">
                                    <label for="fees">الرسوم</label>
                                    <input type="text" id="fees" name="fees" placeholder="الرسوم" required>
                                </div>
                                <div class="input-group">
                                    <label for="date">التاريخ والوقت</label>
                                    <input type="datetime-local" id="date" name="tra-date" placeholder="التاريخ والوقت" />
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

                    <!-- Start Exchange information  -->

                    <div class="exchange-overlay hidden" id="exchangeOverlay">
                        <div class="exchange-info">
                            <span class="close-modal" id="closeExchangeBtn">&times;</span>
                            <div class="exchange-info-title">
                                <h2>بيانات العملية</h2>
                            </div>
                            <div class="output-group">
                                <p class="label">اسم المرسل</p>
                                <p class="sender-out">انيس عامر محمد علي</p>
                            </div>
                            <div class="output-group">
                                <p class="label">رقم الحوالة</p>
                                <p class="id-exchange-out"></p>
                            </div>
                            <div class="output-group">
                                <p class="label">المبلغ</p>
                                <p class="ammount-out"></p>
                            </div>
                            <div class="output-group">
                                <p class="label">له/عليه</p>
                                <p class="for-or-on-out"></p>
                            </div>
                            <div class="output-group">
                                <p class="label">التاريخ</p>
                                <p class="date-out"></p>
                            </div>
                            <div class="output-group">
                                <p class="label">الصراف</p>
                                <p class="atm-out"></p>
                            </div>
                            <div class="output-group">
                                <p class="label">ملاحظة</p>
                                <p class="note-out"></p>
                            </div>
                            <div class="output-group icons">
                                <button><i class="fas fa-image fa-2x"></i></button>
                                <button><i class="fas fa-share fa-2x"></i></button>
                            </div>
                        </div>
                        <!-- <div class="icons">
                        <!-- <div class="image-icon">
    
                        </div> -->
        <!-- <i class="fas fa-image "></i> -->
        <!-- <i class="fas fa-share "></i> -->
                        <!-- <div class="share-icon">
                
                                        </div> -->
                        <!-- </div> -->
                    </div>

                    <!-- End Exchange information  -->
                </div>
            </div>

            <!-- ربط ملفات JavaScript -->

            <script src="JS/navbar.js"></script>
            <!-- تحكم في فتح/إغلاق القائمة -->
            <!--<script src="JS/language.js"></script>-->
            <script src="JS/loginLogoutBtn.js"></script>
            <!-- زر تسجيل الدخول / تسجيل الخروج -->
<!--            <script src="JS/variables.js"></script>-->
            <script src="JS/add_client_modal.js"></script>
            <script src="JS/exchanges_list_modal.js"></script>
            <script src="JS/add_exchange.js"></script>
            <script>
//
//                const exchange = document.getElementById("exchangeOverlay");
//                const closeExchangeBtn = document.getElementById("closeExchangeBtn");
////                const exchangeData = document.getElementById("exchangeData");
//
////                const openExchanesList = document.getElementById("clientsData");
//                const exchangesList = document.getElementById("exchanges-list-overlay");
//                const closeExchangesList = document.getElementById("closeExchangeListBtn");
//
                const openAddExchangeForm = document.getElementById("addExchangeBtn");
                const addExchangeForm = document.getElementById("addExchangeForm");
                const closeAddExchangeForm = document.getElementById("closeAddExchangeBtn");
//
////                exchangeData.addEventListener("click", () => {
////                    exchange.classList.remove("hidden");
////                });
//
//                closeExchangeBtn.addEventListener("click", () => {
//                    exchange.classList.add("hidden");
//                });
////                openExchanesList.addEventListener("click", () => {
////                    exchangesList.classList.remove("hidden");
////                });
//                closeExchangesList.addEventListener("click", () => {
//                    exchangesList.classList.add("hidden");
//                });
                openAddExchangeForm.addEventListener("click", () => {
                    addExchangeForm.classList.remove("hidden");
                });
                closeAddExchangeForm.addEventListener("click", () => {
                    addExchangeForm.classList.add("hidden");
                });

            </script>
    </body>

</html>