<?php

// Get email credentials from environment variables
define("SEND_OTP_EMAIL", getenv('SMTP_EMAIL'));
define("SEND_OTP_PASSWORD", getenv('SMTP_PASSWORD'));


?>
