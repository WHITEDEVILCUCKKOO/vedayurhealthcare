<!-- Section 3: Enquiry -->

<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ------------------------------------------------------------------
   EMAIL SETTINGS
   Sirf password bharna hai (cPanel > Email Accounts > info@ > Manage
   mein jo naya password set karoge wahi yahan daalna hai)
------------------------------------------------------------------- */
const ENQ_SMTP_USER_X91 = 'info@vedayurhealthcare.com';
const ENQ_SMTP_PASS_X91 = '8FPR~c~Z&iBIWSBw';
const ENQ_FROM_NAME_X91 = 'Vedayur Healthcare';
const ENQ_TO_EMAIL_X91  = 'abhiweb.69@gmail.com';   // enquiry yahan aayegi

// Pehle localhost try hoga, na chale to mail.domain
const ENQ_SMTP_HOSTS_X91 = [
    'ssl://localhost:465',
    'ssl://mail.vedayurhealthcare.com:465',
];

/* ------------------------------------------------------------------
   Chhota SMTP sender (kisi library ki zaroorat nahi)
------------------------------------------------------------------- */
function enqSmtpReadX91($fp)
{
    $data = '';
    while (($line = fgets($fp, 515)) !== false) {
        $data .= $line;
        // "250-..." multi-line reply, "250 ..." par khatam
        if (strlen($line) < 4 || $line[3] === ' ') {
            break;
        }
    }
    return $data;
}

function enqSmtpCmdX91($fp, $command, array $okCodes)
{
    if ($command !== null) {
        fwrite($fp, $command . "\r\n");
    }
    $reply = enqSmtpReadX91($fp);
    $code = (int) substr($reply, 0, 3);
    if (!in_array($code, $okCodes, true)) {
        throw new RuntimeException('SMTP error: ' . trim($reply));
    }
    return $reply;
}

function enqConnectX91()
{
    // Same server par certificate name match nahi hota, isliye verify off
    $ctx = stream_context_create([
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ]);

    $lastError = 'No SMTP host configured';

    foreach (ENQ_SMTP_HOSTS_X91 as $host) {
        $fp = @stream_socket_client(
            $host,
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            $ctx
        );

        if ($fp) {
            stream_set_timeout($fp, 15);
            return $fp;
        }

        $lastError = $host . ' -> ' . $errstr . ' (' . $errno . ')';
    }

    throw new RuntimeException('Cannot connect to SMTP: ' . $lastError);
}

function enqSendMailX91($toEmail, $subject, $body)
{
    $user = ENQ_SMTP_USER_X91;
    $pass = ENQ_SMTP_PASS_X91;

    $fp = enqConnectX91();

    try {
        enqSmtpCmdX91($fp, null, [220]);
        enqSmtpCmdX91($fp, 'EHLO vedayurhealthcare.com', [250]);
        enqSmtpCmdX91($fp, 'AUTH LOGIN', [334]);
        enqSmtpCmdX91($fp, base64_encode($user), [334]);
        enqSmtpCmdX91($fp, base64_encode($pass), [235]);
        enqSmtpCmdX91($fp, 'MAIL FROM:<' . $user . '>', [250]);
        enqSmtpCmdX91($fp, 'RCPT TO:<' . $toEmail . '>', [250, 251]);
        enqSmtpCmdX91($fp, 'DATA', [354]);

        $fromName   = '=?UTF-8?B?' . base64_encode(ENQ_FROM_NAME_X91) . '?=';
        $subjectEnc = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        $headers  = 'Date: ' . date('r') . "\r\n";
        $headers .= 'From: ' . $fromName . ' <' . $user . ">\r\n";
        $headers .= 'To: <' . $toEmail . ">\r\n";
        $headers .= 'Subject: ' . $subjectEnc . "\r\n";
        $headers .= 'Message-ID: <' . bin2hex(random_bytes(8)) . '@vedayurhealthcare.com>' . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n";

        $encodedBody = chunk_split(base64_encode($body), 76, "\r\n");

        fwrite($fp, $headers . "\r\n" . $encodedBody . "\r\n.\r\n");
        enqSmtpCmdX91($fp, null, [250]);

        enqSmtpCmdX91($fp, 'QUIT', [221]);
    } finally {
        fclose($fp);
    }

    return true;
}

$enqErrorsX91 = [];
$enqSuccessX91 = '';

$enqNameX91    = '';
$enqPhoneX91   = '';
$enqAreaX91    = '';
$enqPincodeX91 = '';

// Process enquiry form
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['submit_enquiry_x91'])
) {

    // Get form values
    $enqNameX91    = trim($_POST['name'] ?? '');
    $enqPhoneX91   = trim($_POST['phone'] ?? '');
    $enqAreaX91    = trim($_POST['area'] ?? '');
    $enqPincodeX91 = trim($_POST['pincode'] ?? '');

    // Name validation
    if (!preg_match("/^[\p{L}][\p{L}\s.'-]{1,79}$/u", $enqNameX91)) {
        $enqErrorsX91[] = 'Please enter a valid name.';
    }

    // Indian mobile number validation
    if (!preg_match('/^[6-9][0-9]{9}$/', $enqPhoneX91)) {
        $enqErrorsX91[] = 'Please enter a valid 10-digit mobile number.';
    }

    // Area validation
    if ($enqAreaX91 === '' || mb_strlen($enqAreaX91) > 100) {
        $enqErrorsX91[] = 'Please enter a valid area or city.';
    }

    // Pincode validation
    if (!preg_match('/^[1-9][0-9]{5}$/', $enqPincodeX91)) {
        $enqErrorsX91[] = 'Please enter a valid 6-digit pincode.';
    }

    // Send email only if validation passes
    if (empty($enqErrorsX91)) {

        $subjectX91 = 'New Enquiry - Vedayur Healthcare';

        // Indian Standard Time
        date_default_timezone_set('Asia/Kolkata');
        $timeX91 = date('d-m-Y h:i:s A');

        // Email message
        $messageX91  = "New Enquiry Received - Vedayur Healthcare\n\n";
        $messageX91 .= "Date & Time: " . $timeX91 . " IST\n";
        $messageX91 .= "Name: " . $enqNameX91 . "\n";
        $messageX91 .= "Phone: " . $enqPhoneX91 . "\n";
        $messageX91 .= "Area / City: " . $enqAreaX91 . "\n";
        $messageX91 .= "Pincode: " . $enqPincodeX91 . "\n";

        try {

            enqSendMailX91(ENQ_TO_EMAIL_X91, $subjectX91, $messageX91);

            echo "<script>
                alert('Thank you! Your enquiry has been sent successfully.');
                window.location.href = window.location.pathname + '#enquiry';
            </script>";

            exit;

        } catch (\Throwable $errorX91) {

            // Technical error sirf server log mein jayega
            error_log(
                'Vedayur enquiry email error: ' .
                $errorX91->getMessage()
            );

            $enqErrorsX91[] =
                'Email could not be sent. Please try again later.';
        }
    }
}
?>


 <style>
     /* Enquiry Form Validation - X91 */
     .enquiry-form .field {
         position: relative;
     }

     .enquiry-form .field input {
         width: 100%;
         box-sizing: border-box;
         transition: none;
     }

     .enquiry-form .field input:focus {
         outline: none;
         border-color: #1B3D7B;
     }

     .enquiry-form .field input[style*="220, 38, 38"] {
         border-color: #dc2626 !important;
     }

     .enquiry-form .error-message {
         display: block;
         min-height: 0;
         margin-top: 5px;
         color: #dc2626;
         font-size: 13px;
         line-height: 1.5;
         font-weight: 500;
     }

     .enquiry-form .error-message:empty {
         display: none;
         margin: 0;
     }

     .enquiry-form .required-star {
         color: #dc2626;
     }

     .enquiry-form .field input {
         border-radius: 8px;
     }

     .enquiry-form .field input:focus-visible {
         outline: 2px solid rgba(27, 61, 123, 0.15);
         outline-offset: 2px;
     }
 </style>
 <section class="enquiry-highlight" id="enquiry">
     <div class="enquiry-inner">

         <div class="enquiry-copy">
             <span class="enquiry-kicker">QUICK ENQUIRY</span>
             <h2>Get in Touch With Vedayur Healthcare</h2>
             <p>Share your details and our team will contact you for product information and guidance.</p>
         </div>

         <?php if (!empty($enqErrorsX91)): ?>
             <div class="enquiry-server-errors-x91" role="alert">
                 <?php foreach ($enqErrorsX91 as $errorX91): ?>
                     <p><?= htmlspecialchars($errorX91, ENT_QUOTES, 'UTF-8') ?></p>
                 <?php endforeach; ?>
             </div>
         <?php endif; ?>

         <form class="enquiry-form" id="enquiryForm" method="POST" novalidate>

             <div class="field">
                 <label for="enqName">Name <span class="required-star">*</span></label>
                 <input type="text" id="enqName" name="name" placeholder="Your Name" autocomplete="name"
                     maxlength="80" required
                     value="<?= htmlspecialchars($enqNameX91, ENT_QUOTES, 'UTF-8') ?>">
                 <small class="error-message" id="nameError"></small>
             </div>

             <div class="field">
                 <label for="enqPhone">Phone Number <span class="required-star">*</span></label>
                 <input type="tel" id="enqPhone" name="phone" placeholder="10-digit Phone Number"
                     autocomplete="tel" inputmode="numeric" maxlength="10" required
                     value="<?= htmlspecialchars($enqPhoneX91, ENT_QUOTES, 'UTF-8') ?>">
                 <small class="error-message" id="phoneError"></small>
             </div>

             <div class="field">
                 <label for="enqArea">Area <span class="required-star">*</span></label>
                 <input type="text" id="enqArea" name="area" placeholder="Your Area / City"
                     autocomplete="address-level2" maxlength="100" required
                     value="<?= htmlspecialchars($enqAreaX91, ENT_QUOTES, 'UTF-8') ?>">
                 <small class="error-message" id="areaError"></small>
             </div>

             <div class="field">
                 <label for="enqPincode">Pincode <span class="required-star">*</span></label>
                 <input type="text" id="enqPincode" name="pincode" placeholder="6-digit Pincode"
                     autocomplete="postal-code" inputmode="numeric" maxlength="6" required
                     value="<?= htmlspecialchars($enqPincodeX91, ENT_QUOTES, 'UTF-8') ?>">
                 <small class="error-message" id="pincodeError"></small>
             </div>

             <button class="submit" id="enquirySubmit"
                 type="submit" name="submit_enquiry_x91">
                 ➤ Submit Enquiry
             </button>

             <div class="safe">
                 🔒 Your information is safe with us.
             </div>

         </form>
     </div>
 </section>



 <script>
     document.addEventListener("DOMContentLoaded", function() {

         const enquiryFormX91 = document.getElementById("enquiryForm");

         if (!enquiryFormX91) return;

         const nameX91 = document.getElementById("enqName");
         const phoneX91 = document.getElementById("enqPhone");
         const areaX91 = document.getElementById("enqArea");
         const pincodeX91 = document.getElementById("enqPincode");

         const nameErrorX91 = document.getElementById("nameError");
         const phoneErrorX91 = document.getElementById("phoneError");
         const areaErrorX91 = document.getElementById("areaError");
         const pincodeErrorX91 = document.getElementById("pincodeError");

         function validateEnquiryX91() {
             let isValidX91 = true;

             nameErrorX91.textContent = "";
             phoneErrorX91.textContent = "";
             areaErrorX91.textContent = "";
             pincodeErrorX91.textContent = "";

             [nameX91, phoneX91, areaX91, pincodeX91].forEach(function(fieldX91) {
                 fieldX91.style.borderColor = "";
             });

             const nameValueX91 = nameX91.value.trim();
             const phoneValueX91 = phoneX91.value.trim();
             const areaValueX91 = areaX91.value.trim();
             const pincodeValueX91 = pincodeX91.value.trim();

             if (!/^[\p{L}][\p{L}\s.'-]{1,79}$/u.test(nameValueX91)) {
                 nameErrorX91.textContent = "Please enter a valid name (minimum 2 characters).";
                 nameX91.style.borderColor = "#dc2626";
                 isValidX91 = false;
             }

             if (!/^[6-9]\d{9}$/.test(phoneValueX91)) {
                 phoneErrorX91.textContent = "Enter a valid 10-digit Indian mobile number.";
                 phoneX91.style.borderColor = "#dc2626";
                 isValidX91 = false;
             }

             if (!/^[\p{L}\p{N}][\p{L}\p{N}\s,.'()/-]{1,99}$/u.test(areaValueX91)) {
                 areaErrorX91.textContent = "Please enter a valid area or city (minimum 2 characters).";
                 areaX91.style.borderColor = "#dc2626";
                 isValidX91 = false;
             }

             if (!/^[1-9][0-9]{5}$/.test(pincodeValueX91)) {
                 pincodeErrorX91.textContent = "Enter a valid 6-digit Indian pincode.";
                 pincodeX91.style.borderColor = "#dc2626";
                 isValidX91 = false;
             }

             return isValidX91;
         }

         enquiryFormX91.addEventListener("submit", function(eventX91) {
             if (!validateEnquiryX91()) {
                 eventX91.preventDefault();
             }
         });

         [nameX91, phoneX91, areaX91, pincodeX91].forEach(function(fieldX91) {
             fieldX91.addEventListener("input", function() {
                 validateEnquiryX91();
             });
         });

         phoneX91.addEventListener("input", function() {
             phoneX91.value = phoneX91.value.replace(/\D/g, "").slice(0, 10);
         });

         pincodeX91.addEventListener("input", function() {
             pincodeX91.value = pincodeX91.value.replace(/\D/g, "").slice(0, 6);
         });

     });
 </script>