/**
 * التحميل الكسول (Lazy Loading) للحوالات العامة
 * Scroll-triggered lazy loading for Public Exchanges
 * يحمل 20 سجل مع كل تمرير (scroll)
 * النوع ثابت: حوالة فقط (لا يوجد حقل type)
 */

// تنسيق الأرقام مع فواصل الآلاف
function peNumberFormat(value, maxDecimals = 2) {
    let num = Number(value);
    if (isNaN(num)) return value;

    let factor = Math.pow(10, maxDecimals);
    let truncated = Math.trunc(num * factor) / factor;
    let parts = truncated.toString().split(".");
    parts[0] = Number(parts[0]).toLocaleString('en-US');
    return parts.length > 1 ? parts[0] + "." + parts[1] : parts[0];
}

// تحويل رمز العملة إلى اسم
function peCurrencyLabel(currency) {
    if (currency === "new") return "ري قعيطي";
    if (currency === "old") return "ري قديم";
    if (currency === "sa")  return "ريال سعودي";
    return currency;
}

// مصفوفة بيانات الحوالات المحملة
let publicExchangesData = [];

// متغيرات التحكم بالتحميل الكسول
let peOffset = 0;
const peLimit = 20;
let peIsLoading = false;
let peNoMoreData = false;

/**
 * تحميل الحوالات العامة من الخادم
 */
function loadPublicExchanges() {
    if (peIsLoading || peNoMoreData) return;

    peIsLoading = true;
    const loadingMsg = document.getElementById("pe-loading-message");
    loadingMsg.style.display = "block";
    loadingMsg.innerText = "جارٍ التحميل...";

    const formData = new FormData();
    formData.append("limit", peLimit);
    formData.append("offset", peOffset);

    const peListBody = document.getElementById("pe-list-body");

    fetch("get_public_exchanges.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                peListBody.innerHTML = `<p class="pe-empty-state">${data.error}</p>`;
                peIsLoading = false;
                loadingMsg.style.display = "none";
                return;
            }

            if (data.length === 0) {
                if (publicExchangesData.length === 0) {
                    peListBody.innerHTML = '<p class="pe-empty-state">لا توجد حوالات عامة حتى الآن.</p>';
                }
                peNoMoreData = true;
                loadingMsg.innerText = "تم تحميل جميع الحوالات.";
                peIsLoading = false;
                return;
            }

            // إضافة البيانات الجديدة إلى المصفوفة
            publicExchangesData.push(...data);

            // عرض كل صف (بدون TYPE — النوع ثابت حوالة)
            data.forEach(row => {
                const currencyLabel = peCurrencyLabel(row.CURRENCY);
                const statusClass = row.STATUS === 'استلمت' ? 'pe-status-received' : 'pe-status-pending';

                let rowHtml = `
                    <div class="pe-data-container">
                        <div class="pe-oper">
                            <i class="fas fa-trash-alt pe-operation" data-id="trash${row.PE_ID}"></i>
                            <i class="fas fa-edit pe-operation" data-id="edit${row.PE_ID}"></i>
                            <i class="fas fa-share-alt pe-operation" data-id="share${row.PE_ID}"></i>
                        </div>
                        <div class="pe-data" data-id="pe-data-${row.PE_ID}">
                            <h3>${currencyLabel || '-'}</h3>
                            <h3><span class="pe-status-badge ${statusClass}">${row.STATUS || '-'}</span></h3>
                            <h3>${peNumberFormat(row.AMMOUNT)} ${currencyLabel}</h3>
                            <h3 class="pe-date">${row.TRA_DATE}</h3>
                            <h3>${row.SENDER_NAME || '-'}</h3>
                            <h3>${row.SENDER_PHONE || '-'}</h3>
                            <h3>${row.RECEIVER_NAME || '-'}</h3>
                            <h3>${row.RECEIVER_PHONE || '-'}</h3>
                            <h3>${row.TRANSFER_NO || '-'}</h3>
                            <h3>${peNumberFormat(row.TRA_FEES)}</h3>
                            <h3>${peNumberFormat(row.FEES_INCOME)}</h3>
                            <h3>${row.ATM || '-'}</h3>
                            <textarea class="pe-note" readonly>${row.NOTE || ''}</textarea>
                        </div>
                    </div>`;

                peListBody.innerHTML += rowHtml;
            });

            peOffset += peLimit;
            loadingMsg.style.display = "none";
            peIsLoading = false;
        })
        .catch(err => {
            peListBody.innerHTML = `<p class="pe-empty-state">حدث خطأ أثناء تحميل البيانات: ${err}</p>`;
            peIsLoading = false;
            loadingMsg.style.display = "none";
        });
}

// التحميل الأولي عند جهوزية الصفحة
document.addEventListener("DOMContentLoaded", () => {
    loadPublicExchanges();
});

// التحميل عند التمرير (Scroll)
window.addEventListener("scroll", () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
        loadPublicExchanges();
    }
});
