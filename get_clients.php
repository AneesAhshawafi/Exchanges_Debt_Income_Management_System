<?php
include 'dbconn.php';
include 'total_ammounts_calc.php';
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
session_start();
$user_id = $_SESSION['user_id'];
// استعلام العملاء بالحد والبدء
$sql = "SELECT CLIENT_ID, CLIENT_NAME FROM client WHERE DEPT_NO = 1 AND USER_ID = ? ORDER BY CLIENT_ID DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sum_ammounts = calc_total_ammounts($row['CLIENT_ID']);
        $sum_ammount_new = $sum_ammounts[0];
        $sum_ammount_old = $sum_ammounts[1];
        $sum_ammount_sa = $sum_ammounts[2];
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
