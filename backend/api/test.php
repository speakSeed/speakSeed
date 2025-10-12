<?php

header('Content-Type: application/json');

echo json_encode([
    'status' => 'ok',
    'message' => 'PHP is working!',
    'php_version' => phpversion(),
    'time' => date('Y-m-d H:i:s')
]);

