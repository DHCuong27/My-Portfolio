<?php
class PHP_Email_Form {
    public $to; // Địa chỉ nhận email
    public $from_name; // Tên người gửi
    public $from_email; // Email người gửi
    public $subject; // Tiêu đề email
    public $messages = []; // Mảng chứa nội dung email
    public $smtp = []; // Cấu hình SMTP
    public $ajax = false; // Xác định xem form có gửi qua AJAX không

    /**
     * Thêm tin nhắn vào email
     */
    public function add_message($content, $label = '', $priority = 0) {
        $this->messages[] = [
            'content' => htmlspecialchars($content), // Bảo mật nội dung
            'label' => htmlspecialchars($label),
            'priority' => intval($priority)
        ];
    }

    /**
     * Tạo nội dung email
     */
    private function compose_email() {
        $body = "";
        foreach ($this->messages as $message) {
            $body .= (!empty($message['label']) ? $message['label'] . ": " : "") . $message['content'] . "\n";
        }
        return $body;
    }

    /**
     * Gửi email
     */
    public function send() {
        // Kiểm tra các trường bắt buộc
        if (empty($this->to) || empty($this->from_email) || empty($this->from_name) || empty($this->subject)) {
            return false;
        }

        // Tạo tiêu đề email
        $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
        $headers .= "Reply-To: " . $this->from_email . "\r\n";

        // Nếu có cấu hình SMTP, sử dụng PHPMailer
        if (!empty($this->smtp)) {
            return $this->send_via_smtp();
        }

        // Gửi email qua mail() của PHP
        $body = $this->compose_email();
        return mail($this->to, $this->subject, $body, $headers);
    }

    /**
     * Gửi email bằng SMTP
     */
    private function send_via_smtp() {
        // Kiểm tra thư viện PHPMailer
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return false;
        }

        // Yêu cầu các file từ PHPMailer
        require 'PHPMailer/PHPMailer.php';
        require 'PHPMailer/SMTP.php';
        require 'PHPMailer/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->smtp['username'];
        $mail->Password = $this->smtp['password'];
        $mail->SMTPSecure = $this->smtp['encryption'];
        $mail->Port = $this->smtp['port'];

        $mail->setFrom($this->from_email, $this->from_name);
        $mail->addAddress($this->to);
        $mail->Subject = $this->subject;
        $mail->Body = $this->compose_email();

        return $mail->send();
    }
}
?>
