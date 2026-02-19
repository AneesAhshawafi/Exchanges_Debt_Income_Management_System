<?php
include 'dbconn.php';
// لا نحتاج لملف الدالة الآن لأننا سندمجها في الاستعلام
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
session_start();
$user_id = $_SESSION['user_id'];

// استعلام يجلب العملاء مع آخر رصيد تراكمي لكل منهم
$sql = "SELECT c.CLIENT_ID, c.CLIENT_NAME, c.PHONE,
        (SELECT t.sum_ammount_new FROM transaction t WHERE t.CLIENT_ID = c.CLIENT_ID ORDER BY t.TRA_ID DESC LIMIT 1) as last_new,
        (SELECT t.sum_ammount_old FROM transaction t WHERE t.CLIENT_ID = c.CLIENT_ID ORDER BY t.TRA_ID DESC LIMIT 1) as last_old,
        (SELECT t.sum_ammount_sa FROM transaction t WHERE t.CLIENT_ID = c.CLIENT_ID ORDER BY t.TRA_ID DESC LIMIT 1) as last_sa
        FROM client c 
        WHERE c.DEPT_NO = 1 AND c.USER_ID = ? 
        ORDER BY c.CLIENT_ID DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // معالجة القيم الفارغة (إذا كان العميل ليس لديه أي معاملة بعد)
        $sum_ammount_new = $row['last_new'] ?? 0;
        $sum_ammount_old = $row['last_old'] ?? 0;
        $sum_ammount_sa = $row['last_sa'] ?? 0;
        ?>
        <div class="clients-data-container">
            <div class="oper">
                <i class="fas fa-trash-alt oper-client" data-id="trash-client<?= htmlspecialchars($row["CLIENT_ID"]); ?>"></i>
                <i class="fas fa-edit oper-client" data-id="edit-client<?= htmlspecialchars($row["CLIENT_ID"]); ?>"></i>
                <i class="fas fa-share-alt oper-client" data-id="share-client<?= htmlspecialchars($row["CLIENT_ID"]); ?>"></i>
            </div>
            <div class="clients-data" data-id="clients-data<?= htmlspecialchars($row["CLIENT_ID"]); ?>">
                <h3 class="name" id="client-name<?= htmlspecialchars($row["CLIENT_ID"]); ?>">
                    <?= htmlspecialchars($row["CLIENT_NAME"]); ?>
                </h3>
                <h3 class="phone" id="phone<?= htmlspecialchars($row["CLIENT_ID"]); ?>">
                    <?= htmlspecialchars($row["PHONE"]); ?>
                </h3>
                <h3><?= number_format($sum_ammount_new, 2); ?></h3>
                <h3><?= number_format($sum_ammount_old, 2); ?></h3>
                <h3><?= number_format($sum_ammount_sa, 2); ?></h3>
            </div>
        </div>
        <?php
    }
}
$stmt->close();
$conn->close();
?>