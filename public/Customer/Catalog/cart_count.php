
<?php
require_once '../../../config/session_Detils.php';
require_once '../../../config/database.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
$count = 0;
if ($user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $conn->close();
}
echo json_encode(['count' => $count]);
