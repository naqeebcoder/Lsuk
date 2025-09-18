<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (file_exists('phpmailer/vendor/autoload.php')) {
	require_once('phpmailer/vendor/autoload.php');
} else {
	require_once('/home/customer/www/lsuk.org/public_html/lsuk_system/phpmailer/vendor/autoload.php');
}

$mail = new PHPMailer(true);

class setupEmail{
	const FROM_NAME = 'LSUK';
	const LSUK_GMAIL = 'imran.lsukltd@gmail.com';
	const EMAIL_HOST = 'smtp.office365.com';
	const INFO_EMAIL = 'info@lsuk.org';
	const INFO_PASSWORD = 'xtxwzcvtdbjpftdj';
	const HR_EMAIL = 'hr@lsuk.org';
	const HR_PASSWORD = 'hgchxlfvymqjfkvg';
	const PAYROLL_EMAIL = 'payroll@lsuk.org';
	const PAYROLL_PASSWORD = 'vwwfyvhhtfjfpgnl';
	const TRANSLATION_EMAIL = 'translationservice@lsuk.org';
	const TRANSLATION_PASSWORD = 'tvqxsqsmkchzsclc';
	const ACCOUNTS_EMAIL = 'accounts@lsuk.org';
	const ACCOUNTS_PASSWORD = 'bfgdpkbrlfvkvfcr';
	const SECURE_TYPE = 'tls';
	const SENDING_PORT = 587;
}