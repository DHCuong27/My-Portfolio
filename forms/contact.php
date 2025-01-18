<?php
// Đường dẫn đến thư viện PHP Email Form
$php_email_form_path = 'assets/vendor/php-email-form/php-email-form.php';

// Địa chỉ email nhận thư
$receiving_email_address = 'hungcuong.fort.it@gmail.com';

// Kiểm tra địa chỉ email nhận có hợp lệ không
if (!filter_var($receiving_email_address, FILTER_VALIDATE_EMAIL)) {
    die('Invalid receiving email address.');
}

// Kiểm tra và nạp thư viện nếu tồn tại
if (file_exists($php_email_form_path)) {
    include($php_email_form_path);
} else {
    die('Unable to load the "PHP Email Form" Library!');
}

// Tạo đối tượng PHP_Email_Form
$contact = new PHP_Email_Form();
$contact->ajax = true;

// Thiết lập thông tin email nhận
$contact->to = $receiving_email_address;

// Kiểm tra và xử lý form
if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['subject']) && !empty($_POST['message'])) {
    // Lấy dữ liệu từ form và xử lý
    $contact->from_name = htmlspecialchars(trim($_POST['name']));
    $contact->from_email = htmlspecialchars(trim($_POST['email']));
    $contact->subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Kiểm tra email gửi đi có hợp lệ không
    if (!filter_var($contact->from_email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid sender email address.');
    }

    // Thêm nội dung tin nhắn
    $contact->add_message($contact->from_name, 'From');
    $contact->add_message($contact->from_email, 'Email');
    $contact->add_message($message, 'Message', 10);

    // Cấu hình SMTP
    $contact->smtp = array(
        'host' => 'smtp.gmail.com',             // SMTP server của Gmail
        'username' => 'hungcuong.fort.it@gmail.com',  // Email gửi (thay bằng email thực tế)
        'password' => 'ppjd kjfm vmlo vteo',     // Mật khẩu ứng dụng Gmail (App Password)
        'port' => '587',                       // TLS: 587, SSL: 465
        'encryption' => 'tls'                  // TLS hoặc SSL
    );

    // Gửi email
    if ($contact->send()) {
        echo 'Message sent successfully!';
    } else {
        echo 'Failed to send the message. Please try again.';
    }
} else {
    // Trường hợp không đủ dữ liệu từ form
    die('All fields are required.');
}
?>
