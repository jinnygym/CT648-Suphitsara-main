<?php
// รหัสนักศึกษาเจ้าของเว็บ
$student_id = '65130695';

// ดึง IP Address ของผู้เข้าชมเว็บ
$ip_address = $_SERVER['REMOTE_ADDR'];

// ดึงวันเวลาที่เข้าชมเว็บ
$visit_time = date('Y-m-d H:i:s');

// ฟังก์ชันสำหรับดึงข้อมูลประเทศและหน่วยงานจาก IP Address
function get_ip_info($ip) {
    $url = "http://ip-api.com/json/{$ip}";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

$ip_info = get_ip_info($ip_address);
$country = $ip_info['country'] ?? 'Unknown';
$organization = $ip_info['org'] ?? 'Unknown';

// สร้างข้อความแจ้งเตือน
$message = "รหัสนักศึกษา: {$student_id}\nวันเวลา: {$visit_time}\nIP Address: {$ip_address}\nประเทศ: {$country}\nหน่วยงาน: {$organization}";

// Token สำหรับการแจ้งผ่าน Line Notify
$token = 'MeHN6VNE4a3m4CnB2IPJAvly7hNvlMdCCVi9pyzDaGh';

// ฟังก์ชันสำหรับส่งข้อความไปยัง Line Notify
function send_line_notify($message, $token) {
    $line_api = 'https://notify-api.line.me/api/notify';
    $data = array('message' => $message);
    $headers = array(
        'Content-Type: multipart/form-data',
        'Authorization: Bearer ' . $token,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $line_api);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

// ส่งข้อความแจ้งเตือน
$result = send_line_notify($message, $token);

// แสดงผลการทำงาน
if ($result) {
    echo 'Notification sent!';
} else {
    echo 'Failed to send notification.';
}
?>
