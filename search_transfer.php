<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'csrf_token.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>بحث عن حوالة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="بحث عن حوالة برقمها ومعرفة حالتها وتاريخ الإرسال والاستلام" />

    <link rel="stylesheet" type="text/css" media="screen" href="CSS/normalize.css" />
    <link rel="stylesheet" href="CSS/all.min.css" />
    <link rel="stylesheet" href="CSS/GlobalRulesStyle.css" />
    <link rel="stylesheet" href="CSS/searchStyle.css?v=<?= filemtime('CSS/searchStyle.css') ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />
    <link rel="manifest" href="manifest.json" />
    <link rel="shortcut icon" href="/favicon.ico" />
</head>

<body>
    <!-- Header -->
    <header>
        <section class="container">
            <nav>
                <i class="fas fa-bars toggle-menu" id="fa-bar"></i>
                <ul id="menu">
                    <li><a href="index.php">قسم الحوالات</a></li>
                    <li><a href="debt.php">قسم الديون</a></li>
                    <li><a href="income_list.php">قسم الدخل</a></li>
                    <li><a class="active" href="search_transfer.php">بحث حوالة</a></li>
                    <li id="login-li">
                        <a href="logout.php" class="login-buttn">تسجيل الخروج</a>
                    </li>
                </ul>
                <h1>بن عبود للصرافة والتحويلات</h1>
                <a href="#" class="logo">
                    <img src="images/logo2.jpg" alt="logo" />
                </a>
            </nav>
        </section>
    </header>

    <!-- Search Section -->
    <div class="search-section">
        <div class="search-wrapper">
            <div class="search-card">
                <div class="search-card-title">
                    <i class="fas fa-search search-icon"></i>
                    <h2>البحث عن حوالة</h2>
                    <p>أدخل رقم الحوالة للاستعلام عن حالتها</p>
                </div>

                <!-- Search Form -->
                <form class="search-form" id="searchForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="text" class="search-input-field" id="transferNoInput"
                        name="transfer_no" placeholder="أدخل رقم الحوالة..." autocomplete="off" required />
                    <button type="submit" class="search-btn" id="searchBtn">
                        <i class="fas fa-search"></i>
                        <span>بحث</span>
                    </button>
                </form>

                <!-- Loading Spinner -->
                <div class="search-spinner" id="searchSpinner">
                    <div class="spinner-circle"></div>
                    <p>جارٍ البحث...</p>
                </div>

                <!-- Result Card -->
                <div class="result-card" id="resultCard">
                    <!-- Status Banner -->
                    <div class="status-banner" id="statusBanner">
                        <i id="statusIcon"></i>
                        <span id="statusText"></span>
                    </div>

                    <!-- Details -->
                    <div class="result-details" id="resultDetails">
                        <!-- Filled by JS -->
                    </div>

                    <!-- Mark Received Button (hidden by default) -->
                    <button class="mark-received-btn hidden" id="markReceivedBtn">
                        <i class="fas fa-check-circle"></i>
                        <span>تأكيد الاستلام</span>
                    </button>
                </div>

                <!-- Not Found -->
                <div class="not-found" id="notFound">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p id="notFoundText">لم يتم العثور على حوالة بهذا الرقم</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="JS/navbar.js?v=<?= filemtime('JS/navbar.js') ?>"></script>
    <script>
        const searchForm = document.getElementById('searchForm');
        const transferNoInput = document.getElementById('transferNoInput');
        const searchBtn = document.getElementById('searchBtn');
        const searchSpinner = document.getElementById('searchSpinner');
        const resultCard = document.getElementById('resultCard');
        const statusBanner = document.getElementById('statusBanner');
        const statusIcon = document.getElementById('statusIcon');
        const statusText = document.getElementById('statusText');
        const resultDetails = document.getElementById('resultDetails');
        const markReceivedBtn = document.getElementById('markReceivedBtn');
        const notFound = document.getElementById('notFound');
        const notFoundText = document.getElementById('notFoundText');
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;

        let currentTraId = null;

        // تنسيق الأرقام
        function formatNumber(num) {
            return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // ترجمة العملة
        function getCurrencyName(code) {
            const map = { 'new': 'ريال قعيطي', 'old': 'ريال قديم', 'sa': 'ريال سعودي' };
            return map[code] || code;
        }

        function getCurrencyClass(code) {
            return code || '';
        }

        // إخفاء الكل
        function resetResults() {
            resultCard.classList.remove('active');
            notFound.classList.remove('active');
            searchSpinner.classList.remove('active');
            markReceivedBtn.classList.add('hidden');
        }

        // البحث
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const transferNo = transferNoInput.value.trim();
            if (!transferNo) return;

            resetResults();
            searchSpinner.classList.add('active');
            searchBtn.disabled = true;

            const formData = new FormData();
            formData.append('transfer_no', transferNo);

            fetch('search_transfer_api.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(response => {
                    searchSpinner.classList.remove('active');
                    searchBtn.disabled = false;

                    if (response.error) {
                        notFoundText.textContent = response.error;
                        notFound.classList.add('active');
                        return;
                    }

                    if (response.success && response.data) {
                        showResult(response.data);
                    } else {
                        notFoundText.textContent = response.message || 'لم يتم العثور على حوالة بهذا الرقم';
                        notFound.classList.add('active');
                    }
                })
                .catch(err => {
                    searchSpinner.classList.remove('active');
                    searchBtn.disabled = false;
                    notFoundText.textContent = 'حدث خطأ في الاتصال بالسيرفر';
                    notFound.classList.add('active');
                    console.error('Search error:', err);
                });
        });

        // عرض النتيجة
        function showResult(data) {
            currentTraId = data.TRA_ID;

            // Status banner
            const isReceived = data.STATUS === 'استلمت';
            statusBanner.className = 'status-banner ' + (isReceived ? 'received' : 'pending');
            statusIcon.className = isReceived ? 'fas fa-check-circle' : 'fas fa-clock';

            if (isReceived) {
                const receivedDate = data.RECEIVED_AT || data.TRA_DATE;
                statusText.textContent = 'تم استلام الحوالة بتاريخ ' + receivedDate;
            } else {
                statusText.textContent = 'الحوالة لم تُستلم بعد';
            }

            // Details
            let currencyBadge = `<span class="currency-badge ${getCurrencyClass(data.CURRENCY)}">${getCurrencyName(data.CURRENCY)}</span>`;

            let detailsHTML = `
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-hashtag"></i> رقم الحوالة</span>
                    <span class="detail-value">${data.TRANSFER_NO}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-tag"></i> نوع العملية</span>
                    <span class="detail-value">${data.TYPE}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-money-bill-wave"></i> المبلغ</span>
                    <span class="detail-value amount">${formatNumber(data.AMMOUNT)} ${currencyBadge}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-calendar-alt"></i> تاريخ الإرسال</span>
                    <span class="detail-value">${data.TRA_DATE}</span>
                </div>`;

            if (isReceived && data.RECEIVED_AT) {
                detailsHTML += `
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-calendar-check"></i> تاريخ الاستلام</span>
                    <span class="detail-value">${data.RECEIVED_AT}</span>
                </div>`;
            }

            if (data.SENDER_NAME) {
                detailsHTML += `
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-user"></i> المرسل</span>
                    <span class="detail-value">${data.SENDER_NAME}</span>
                </div>`;
            }

            if (data.RECEIVER_NAME) {
                detailsHTML += `
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-user-check"></i> المستلم</span>
                    <span class="detail-value">${data.RECEIVER_NAME}</span>
                </div>`;
            }

            if (data.ATM) {
                detailsHTML += `
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-university"></i> الصراف</span>
                    <span class="detail-value">${data.ATM}</span>
                </div>`;
            }

            if (data.CLIENT_NAME) {
                detailsHTML += `
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-user-tie"></i> العميل</span>
                    <span class="detail-value">${data.CLIENT_NAME}</span>
                </div>`;
            }

            if (data.NOTE) {
                detailsHTML += `
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-sticky-note"></i> ملاحظة</span>
                    <span class="detail-value">${data.NOTE}</span>
                </div>`;
            }

            resultDetails.innerHTML = detailsHTML;

            // Show/hide mark received button
            if (!isReceived) {
                markReceivedBtn.classList.remove('hidden');
            } else {
                markReceivedBtn.classList.add('hidden');
            }

            resultCard.classList.add('active');
        }

        // تأكيد الاستلام
        markReceivedBtn.addEventListener('click', function () {
            if (!currentTraId) return;

            markReceivedBtn.disabled = true;
            markReceivedBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>جارٍ التأكيد...</span>';

            const formData = new FormData();
            formData.append('tra_id', currentTraId);
            formData.append('csrf_token', csrfToken);

            fetch('mark_received.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        // تحديث الواجهة
                        statusBanner.className = 'status-banner received';
                        statusIcon.className = 'fas fa-check-circle';
                        statusText.textContent = 'تم استلام الحوالة بتاريخ ' + response.received_at;
                        markReceivedBtn.classList.add('hidden');

                        // إضافة صف تاريخ الاستلام
                        const receivedRow = document.createElement('div');
                        receivedRow.className = 'detail-row';
                        receivedRow.innerHTML = `
                            <span class="detail-label"><i class="fas fa-calendar-check"></i> تاريخ الاستلام</span>
                            <span class="detail-value">${response.received_at}</span>
                        `;
                        // إدراج بعد تاريخ الإرسال
                        const rows = resultDetails.querySelectorAll('.detail-row');
                        if (rows.length >= 4) {
                            rows[3].after(receivedRow);
                        } else {
                            resultDetails.appendChild(receivedRow);
                        }
                    } else {
                        alert(response.error || 'حدث خطأ');
                        markReceivedBtn.disabled = false;
                        markReceivedBtn.innerHTML = '<i class="fas fa-check-circle"></i> <span>تأكيد الاستلام</span>';
                    }
                })
                .catch(err => {
                    alert('حدث خطأ في الاتصال');
                    markReceivedBtn.disabled = false;
                    markReceivedBtn.innerHTML = '<i class="fas fa-check-circle"></i> <span>تأكيد الاستلام</span>';
                    console.error('Mark received error:', err);
                });
        });
    </script>
</body>

</html>
