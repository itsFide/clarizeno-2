<?php



// header('Content-Type: application/json; charset=UTF-8');

// function respond($data, $code = 200) {
//     http_response_code($code);
//     echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//     exit;
// }
// function clean($s){ return preg_replace('/[^\P{C}\t\r\n]+/u', '', trim((string)$s)); }

// // ===== 1) Чтение и валидация входных данных =====
// $name  = isset($_POST['name'])  ? trim($_POST['name'])  : '';
// $phone = isset($_POST['phone']) ? preg_replace('/\D+/', '', $_POST['phone']) : ''; // ожидаем 63XXXXXXXXXX (12 цифр)
// $email = isset($_POST['email']) ? trim($_POST['email']) : '';

// $errors = [];
// if ($name === '' || preg_match('/\s/', $name)) {
//     $errors['name'] = 'Please enter your first name (one word).';
// }
// if (!preg_match('/^63\d{10}$/', $phone)) {
//     $errors['phone'] = 'Phone must be 12 digits starting with 63 (e.g., 639123456789).';
// }
// if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     $errors['email'] = 'Please enter a valid email address.';
// }
// if (!empty($errors)) {
//     respond(['success' => false, 'errors' => $errors], 422);
// }

// // ===== 2) Конфиг: Infobip + MailerLite =====
// // (У вас Infobip уже работает, оставляю так же)
// $INFOBIP_BASE_URL = 'z3n566.api.infobip.com';
// $INFOBIP_API_KEY  = 'App 5e11812108442833417e8afbd1d289e4-67663258-34d8-479b-8dd3-9183b91dd683';
// $INFOBIP_FROM     = 'Test';
// if ($INFOBIP_API_KEY && stripos($INFOBIP_API_KEY, 'App ') !== 0) {
//     $INFOBIP_API_KEY = 'App ' . $INFOBIP_API_KEY; // подстраховка
// }



// if (!$INFOBIP_BASE_URL || !$INFOBIP_API_KEY || !$INFOBIP_FROM) {
//     respond(['success'=>false,'message'=>'Infobip not configured'], 500);
// }
// if (!$MAILERLITE_API_KEY) {
//     respond(['success'=>false,'message'=>'MailerLite not configured'], 500);
// }

// // ===== 3) Текст SMS (ваш вариант А) =====
// $BOT_LINK = clean(getenv('BOT_LINK') ?: 'https://t.me/first_victory_bot');
// $smsText = "{$name}, Kuya Dima here. That story about the leaking bangka? It ends for you today. Your first mission on the Path of Control is waiting. Click the link, press START, and receive your first command. The Bayan is waiting for you. {$BOT_LINK}";

// // ===== 4) Отправляем SMS через Infobip =====
// $infobipPayload = [
//     'messages' => [[
//         'destinations' => [['to' => $phone]], // 63XXXXXXXXXX
//         'from' => $INFOBIP_FROM,
//         'text' => $smsText
//     ]]
// ];

// $ch1 = curl_init();
// curl_setopt_array($ch1, [
//     CURLOPT_URL => "https://{$INFOBIP_BASE_URL}/sms/2/text/advanced",
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_POST => true,
//     CURLOPT_HTTPHEADER => [
//         "Authorization: {$INFOBIP_API_KEY}",
//         "Content-Type: application/json",
//         "Accept: application/json"
//     ],
//     CURLOPT_POSTFIELDS => json_encode($infobipPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
//     CURLOPT_TIMEOUT => 20
// ]);
// $respBody1 = curl_exec($ch1);
// $respErr1  = curl_error($ch1);
// $respCode1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
// curl_close($ch1);

// if ($respErr1 || $respCode1 >= 400) {
//     respond([
//         'success' => false,
//         'message' => 'Infobip SMS send failed',
//         'debug'   => ['http_code'=>$respCode1,'error'=>$respErr1,'body'=>$respBody1]
//     ], 502);
// }


// // ===== 6) Успех =====
// respond([
//     'success'  => true,
//     'infobip'  => ['http_code' => $respCode1, 'body' => json_decode($respBody1, true)],
// ]);

header('Content-Type: application/json; charset=UTF-8');

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function clean($s){ return preg_replace('/[^\P{C}\t\r\n]+/u', '', trim((string)$s)); }

// ===== 1) INPUT VALIDATION =====
$name  = isset($_POST['name'])  ? trim($_POST['name'])  : '';
$phone = isset($_POST['phone']) ? preg_replace('/\D+/', '', $_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

$errors = [];
if ($name === '' || preg_match('/\s/', $name)) {
    $errors['name'] = 'Please enter your first name (one word).';
}
if (!preg_match('/^63\d{10}$/', $phone)) {
    $errors['phone'] = 'Phone must be 12 digits starting with 63 (e.g., 639123456789).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}
if (!empty($errors)) {
    respond(['success' => false, 'errors' => $errors], 422);
}

// ===== 2) CONFIGURATION =====
$INFOBIP_BASE_URL = 'z3n566.api.infobip.com';
$INFOBIP_API_KEY  = 'App 5e11812108442833417e8afbd1d289e4-67663258-34d8-479b-8dd3-9183b91dd683';
$INFOBIP_FROM     = 'Test';

$SENDPULSE_API_ID     = '6c43b3a795308d8f5a641b5dcebc4968';
$SENDPULSE_API_SECRET = 'baedfc96b0e9c19c98793f80d18c6a22';
$SENDPULSE_BOOK_ID    = '450435'; // ID адресной книги (integer)

if (!$INFOBIP_BASE_URL || !$INFOBIP_API_KEY || !$INFOBIP_FROM) {
    respond(['success'=>false,'message'=>'Infobip not configured'], 500);
}
if (!$SENDPULSE_API_ID || !$SENDPULSE_API_SECRET || !$SENDPULSE_BOOK_ID) {
    respond(['success'=>false,'message'=>'SendPulse not configured'], 500);
}

// ===== 3) SEND SMS VIA INFOBIP =====
$BOT_LINK = clean(getenv('BOT_LINK') ?: 'https://t.me/first_victory_bot');
$smsText = "{$name}, Kuya Dima here. That story about the leaking bangka? It ends for you today. "
         . "Your first mission on the Path of Control is waiting. Click the link, press START, and receive your first command. "
         . "The Bayan is waiting for you. {$BOT_LINK}";

$infobipPayload = [
    'messages' => [[
        'destinations' => [['to' => $phone]],
        'from' => $INFOBIP_FROM,
        'text' => $smsText
    ]]
];

$ch1 = curl_init();
curl_setopt_array($ch1, [
    CURLOPT_URL => "https://{$INFOBIP_BASE_URL}/sms/2/text/advanced",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: {$INFOBIP_API_KEY}",
        "Content-Type: application/json",
        "Accept: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($infobipPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    CURLOPT_TIMEOUT => 20
]);
$respBody1 = curl_exec($ch1);
$respErr1  = curl_error($ch1);
$respCode1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
curl_close($ch1);

if ($respErr1 || $respCode1 >= 400) {
    respond([
        'success' => false,
        'message' => 'Infobip SMS send failed',
        'debug'   => ['http_code'=>$respCode1,'error'=>$respErr1,'body'=>$respBody1]
    ], 502);
}

// ===== 4) AUTHENTICATE TO SENDPULSE =====
$chAuth = curl_init();
curl_setopt_array($chAuth, [
    CURLOPT_URL => "https://api.sendpulse.com/oauth/access_token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS => json_encode([
        'grant_type'    => 'client_credentials',
        'client_id'     => $SENDPULSE_API_ID,
        'client_secret' => $SENDPULSE_API_SECRET
    ]),
    CURLOPT_TIMEOUT => 20
]);
$authResp = curl_exec($chAuth);
$authErr  = curl_error($chAuth);
curl_close($chAuth);

if ($authErr) {
    respond(['success'=>false,'message'=>'SendPulse auth failed','error'=>$authErr],502);
}
$authData = json_decode($authResp, true);
if (empty($authData['access_token'])) {
    respond(['success'=>false,'message'=>'No SendPulse token','debug'=>$authData],502);
}
$token = $authData['access_token'];

// ===== 5) ADD SUBSCRIBER TO SENDPULSE =====
$payload = [
    'emails' => [[
        'email'  => $email,
        'variables' => [
            'name'  => $name,
            'phone' => $phone
        ]
    ]]
];

$ch2 = curl_init();
curl_setopt_array($ch2, [
    CURLOPT_URL => "https://api.sendpulse.com/addressbooks/{$SENDPULSE_BOOK_ID}/emails",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$token}",
        "Content-Type: application/json",
        "Accept: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    CURLOPT_TIMEOUT => 20
]);
$respBody2 = curl_exec($ch2);
$respErr2  = curl_error($ch2);
$respCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

if ($respErr2 || $respCode2 >= 400) {
    respond([
        'success' => false,
        'message' => 'SendPulse contact create failed',
        'debug'   => ['http_code'=>$respCode2,'error'=>$respErr2,'body'=>$respBody2]
    ], 502);
}

// ===== 6) SUCCESS RESPONSE =====
respond([
    'success' => true,
    'infobip' => ['http_code'=>$respCode1,'body'=>json_decode($respBody1, true)],
    'sendpulse' => ['http_code'=>$respCode2,'body'=>json_decode($respBody2, true)]
]);
