<?php

function ensure_json_content_type(): void
{
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
    }
}

// رؤوس CORS + JSON؛ $allowed_methods تُمرَّر لكل مسار (GET / POST / DELETE)
function send_json_api_headers(string $allowed_methods): void
{
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: ' . $allowed_methods);
    ensure_json_content_type();
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
}