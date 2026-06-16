<?php
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function bindParams($stmt, $types, &$params) {
    if ($types === '' || empty($params)) {
        return;
    }

    $bind_values = [$types];
    foreach ($params as $key => &$value) {
        $bind_values[] = &$value;
    }

    call_user_func_array([$stmt, 'bind_param'], $bind_values);
}

function redirectWithStatus($location, $type, $message) {
    $separator = strpos($location, '?') === false ? '?' : '&';
    header('Location: ' . $location . $separator . http_build_query([
        'type' => $type,
        'message' => $message,
    ]));
    exit();
}

function displayStatusMessage() {
    if (!isset($_GET['message'])) {
        return;
    }

    $type = $_GET['type'] ?? 'info';
    $allowed_types = ['success', 'danger', 'warning', 'info'];
    if (!in_array($type, $allowed_types, true)) {
        $type = 'info';
    }

    echo "<div class='alert alert-" . e($type) . "'>" . e($_GET['message']) . "</div>";
}
?>
