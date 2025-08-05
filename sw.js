// اسم ذاكرة التخزين المؤقت (Cache) والإصدار
const CACHE_NAME = 'my-pwa-cache-v1';
// قائمة الملفات التي سيتم تخزينها مؤقتًا
const urlsToCache = [
    '/',
    '/index.php',
    '/debt.php',
    '/debts_list.php',
    '/debt_insert_debt.php',
    '/debt_update_debt.php',
    '/debt_update_sum_ammounts.php',
    '/debt_total_ammounts_calc.php',
    '/income_get_income_list.php',
    '/income_insert_income.php',
    '/income_update_income.php',
    '/income_update_sum_ammounts.php',
    '/income_total_ammounts_calc.php',
    '/total_ammounts_calc.php',
    '/update_client.php',
    '/update_exchange.php',
    '/update_sum_ammounts.php',
    '/insert_transaction.php',
    '/share_exchange.php',
    '/debt_share_debt.php',
    '/income_share_income.php',
    '/generate_client_pdf.php',
    '/debt_generate_client_pdf.php',
    'CSS/indexxStyle.css',
    "/JS/add_client_modal.js",
    "/JS/add_debt.js",
    "/JS/add_exchange.js",
    "/JS/add_income.js",
    "/JS/authGuard.js",
    "/JS/debt_lazy_loading_clients.js",
    "/JS/debt_list_modal.js",
    "/JS/debt_set_current_client.js",
    "/JS/exchanges_list_modal.js",
    "/JS/income_list_modal.js",
    "/JS/lazy_loading_clients.js",
    "/JS/loginForm.js",
    "/JS/loginLogoutBtn.js",
    "/JS/navbar.js",
    "/JS/operations_on_client.js",
    "/JS/set_current_client_id.js",
    "/JS/switch_withdraw_exchange.js",
    '/icon-192.png',
    '/icon-512.png',
];

// 1. تثبيت عامل الخدمة وتخزين الملفات
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// 2. جلب الملفات من ذاكرة التخزين المؤقت أو من الشبكة
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // إذا وجد الملف في الكاش، قم بإرجاعه
                if (response) {
                    return response;
                }
                // إذا لم يوجد، اطلبه من الشبكة
                return fetch(event.request);
            })
    );
});

// 3. تحديث ذاكرة التخزين المؤقت عند تفعيل عامل خدمة جديد
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        // حذف الكاش القديم
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
