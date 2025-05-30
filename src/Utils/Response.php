<?php

function sendJson($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sendError($message, $status = 400, $details = null)
{
    $response = ['error' => $message];
    if ($details) {
        $response['details'] = $details;
    }
    sendJson($response, $status);
}
