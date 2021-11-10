<?php
require_once '../vendor/autoload.php';
// Creates a mysqldump and emails the resulting dump file
use PHPMailer\PHPMailer\PHPMailer;

// Edit the following values
$dbhost = "81.88.52.164"; // usually localhost
$dbuser = "uv49z9yx";
$dbpass = "t8a,y-657?q:";
$dbname = "uv49z9yx_reduc";
$sendTo = "proviv-pub@protonmail.com";
$from = "admin@promo-parrain.com";
$fromName = "Automated Backup";
$subject = "Sauvegarde de Promo-parrain du ";
$body = "Sauvegarde journalière de la BDD de promo-parrain du ".date("Y-m-d H:i:s");

// Remember to set the location of your backups dir and to set perms to 777
$backupfile = '../backup/mysql/' . $dbname . date("Y-m-d") . '.sql';

// Remember to use the proper location of mysqldump
system("/path/to/mysqldump -h $dbhost -u $dbuser -p$dbpass $dbname > $backupfile", $return);

gzencode($backupfile, 9);

// Mail the file

// Include and instantiate PHPMailer()

$mail = new PHPMailer();

$mail->AddAddress($sendTo);
$mail->From			= $from;
$mail->Subject	= $subject;
$mail->FromName = $fromName;
$mail->Body 		= $body;
$mail->AltBody 	= $body;
$mail->AddAttachment($backupfile);
$mail->WordWrap = 50; // Some old mail programs cut off email if word wrap is greater
$mail->IsSMTP(true);
$mail->SMTPAuth	= true;
$mail->Host			= "ssl://authsmtp.securemail.pro:465"; // Use Sendgrid SSL here
$mail->Username = "proviv-pub@protonmail.com";
$mail->Password = "Kckk2k5k69.";

$mail->Send();