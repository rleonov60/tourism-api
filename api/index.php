<?php
header('Content-Type: application/json; charset=utf-8');

// --- ПРОВЕРКА НА ОШИБКУ 400 (Некорректный запрос) ---
if (isset($_GET['limit']) && !is_numeric($_GET['limit'])) {
    http_response_code(400);
    echo json_encode(["error" => "Некорректный параметр limit"], JSON_UNESCAPED_UNICODE);
    exit;
}

// --- ТЕСТОВЫЙ ЭНДПОИНТ ДЛЯ ОШИБКИ 500 (Внутренняя ошибка) ---
if (isset($_GET['error-test'])) {
    http_response_code(500);
    echo json_encode(["error" => "Тестовая внутренняя ошибка сервера"], JSON_UNESCAPED_UNICODE);
    exit;
}

// --- ОСНОВНАЯ РАБОТА (200 OK) ---
$conn = new mysqli("localhost", "root", "", "tourism_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Ошибка подключения к базе"], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "SELECT n.id, n.title, n.content, u.login, n.created_at 
        FROM notes n 
        JOIN users u ON u.id = n.id_user 
        ORDER BY n.id";
$result = $conn->query($sql);

$arr = [];
while ($row = $result->fetch_assoc()) {
    $arr[] = [
        "id"             => str_pad($row["id"], 5, "0", STR_PAD_LEFT),
        "title_user"     => $row["title"] . " - " . $row["login"],
        "content"        => $row["content"],
        "formatted_date" => date("d.m.Y", strtotime($row["created_at"]))
    ];
}

echo json_encode($arr, JSON_UNESCAPED_UNICODE);
$conn->close();
?>