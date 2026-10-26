<!-- section 7 -->

<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Shared email settings + functions (password wahin hai)
require_once __DIR__ . '/mail-helper.php';

$conErrorsX91 = [];

$conNameX91    = '';
$conPhoneX91   = '';
$conAreaX91    = '';
$conPincodeX91 = '';

// Process contact form
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['submit_contact_x91'])
) {

    // Get form values
    $conNameX91    = trim($_POST['name'] ?? '');
    $conPhoneX91   = trim($_POST['phone'] ?? '');
    $conAreaX91    = trim($_POST['area'] ?? '');
    $conPincodeX91 = trim($_POST['pincode'] ?? '');

    // 10 minute cooldown check (cookie)
    if (enqCooldownActiveX91()) {
        $conErrorsX91[] = 'You have already submitted a form. Please wait 10 minutes before sending another request.';
    }

    // Name validation
    if (!preg_match("/^[\p{L}][\p{L}\s.'-]{1,79}$/u", $conNameX91)) {
        $conErrorsX91[] = 'Please enter a valid name.';
    }

    // Indian mobile number validation
    if (!preg_match('/^[6-9][0-9]{9}$/', $conPhoneX91)) {
        $conErrorsX91[] = 'Please enter a valid 10-digit mobile number.';
    }

    // Area validation
    if ($conAreaX91 !== '' && mb_strlen($conAreaX91) > 100) {
        $conErrorsX91[] = 'Please enter a valid area or city.';
    }

    // Pincode validation
    if ($conPincodeX91 !== '' && !preg_match('/^[1-9][0-9]{5}$/', $conPincodeX91)) {
        $conErrorsX91[] = 'Please enter a valid 6-digit pincode.';
    }

    // Send email only if validation passes
    if (empty($conErrorsX91)) {

        // Subject alag hai taaki inbox mein pata chale kaunsa form hai
        $conSubjectX91 = 'New Contact Request - Vedayur Healthcare';

        // Indian Standard Time
        date_default_timezone_set('Asia/Kolkata');
        $conTimeX91 = date('d-m-Y h:i:s A');

        // Email message
        $conMessageX91  = "New Contact Request (Contact Section) - Vedayur Healthcare\n\n";
        $conMessageX91 .= "Date & Time: " . $conTimeX91 . " IST\n";
        $conMessageX91 .= "Name: " . $conNameX91 . "\n";
        $conMessageX91 .= "Phone: " . $conPhoneX91 . "\n";
        $conMessageX91 .= "Area: " . ($conAreaX91 !== '' ? $conAreaX91 : 'Not provided') . "\n";
        $conMessageX91 .= "Pincode: " . ($conPincodeX91 !== '' ? $conPincodeX91 : 'Not provided') . "\n";

        try {

            enqSendMailX91(ENQ_TO_EMAIL_X91, $conSubjectX91, $conMessageX91);

            echo enqSuccessScriptX91('/index.php');

            exit;

        } catch (\Throwable $conErrorX91) {

            // Technical error sirf server log mein jayega
            error_log(
                'Vedayur contact email error: ' .
                $conErrorX91->getMessage()
            );

            $conErrorsX91[] =
                'Email could not be sent. Please try again later.';
        }
    }
}
?>

 <style>
     /* Contact Form Validation - X91 */
     .contact-form .field input {
         width: 100%;
         box-sizing: border-box;
         border-radius: 8px;
     }

     .contact-form .contact-error {
         display: block;
         margin-top: 5px;
         color: #dc2626;
         font-size: 13px;
         line-height: 1.5;
         font-weight: 500;
     }

     .contact-form .contact-error:empty {
         display: none;
         margin: 0;
     }

     .contact-form .required-star {
         color: #dc2626;
     }

     .contact-form .contact-server-errors-x91 {
         color: #dc2626;
         font-size: 14px;
         font-weight: 500;
         margin-bottom: 12px;
     }

     .contact-form .contact-server-errors-x91 p {
         margin: 0 0 4px;
     }
 </style>

 <section class="contact" id="contact">
     <div class="contact-copy">
         <h2>Ready to Begin Your<br />Wellness Journey?</h2>
         <p>Get in touch with us for product details, guidance or treatment information.</p>
         <p>We have been serving wellness needs since 2009.</p>
         <div class="since">✦ Since 2009<br /><small style="color:#ddd">Trusted for a Healthier Tomorrow</small>
         </div>
     </div>

     <!-- Section 7: Contact Form -->
     <form class="contact-form" id="contactForm" method="POST" action="#contact" novalidate>
         <h3>Contact Vedayur Healthcare</h3>
         <p>Fill in your details and our team will contact you.</p>

         <?php if (!empty($conErrorsX91)): ?>
             <div class="contact-server-errors-x91" role="alert">
                 <?php foreach ($conErrorsX91 as $conErrX91): ?>
                     <p><?= htmlspecialchars($conErrX91, ENT_QUOTES, 'UTF-8') ?></p>
                 <?php endforeach; ?>
             </div>
         <?php endif; ?>

         <div class="contact-fields">

             <div class="field">
                 <label for="contactName">Name <span class="required-star">*</span></label>
                 <input type="text" id="contactName" name="name" placeholder="Your Name" autocomplete="name"
                     maxlength="80" required
                     value="<?= htmlspecialchars($conNameX91, ENT_QUOTES, 'UTF-8') ?>">
                 <small class="contact-error" id="contactNameError"></small>
             </div>

             <div class="field">
                 <label for="contactPhone">Number <span class="required-star">*</span></label>
                 <input type="tel" id="contactPhone" name="phone" inputmode="numeric" maxlength="10"
                     placeholder="Your Phone Number" autocomplete="tel" required
                     value="<?= htmlspecialchars($conPhoneX91, ENT_QUOTES, 'UTF-8') ?>">
                 <small class="contact-error" id="contactPhoneError"></small>
             </div>

             <div class="field">
                 <label for="contactArea">Area <span style="font-weight:400;opacity:.7;font-size:.85em">(Optional)</span></label>
                 <input type="text" id="contactArea" name="area" placeholder="Your Area"
                     autocomplete="address-level2" maxlength="100"
                     value="<?= htmlspecialchars($conAreaX91, ENT_QUOTES, 'UTF-8') ?>">
                 <small class="contact-error" id="contactAreaError"></small>
             </div>

             <div class="field">
                 <label for="contactPincode">Pincode <span style="font-weight:400;opacity:.7;font-size:.85em">(Optional)</span></label>
                 <input type="text" id="contactPincode" name="pincode" inputmode="numeric" maxlength="6"
                     placeholder="6-digit Pincode" autocomplete="postal-code"
                     value="<?= htmlspecialchars($conPincodeX91, ENT_QUOTES, 'UTF-8') ?>">
                 <small class="contact-error" id="contactPincodeError"></small>
             </div>

         </div>

         <button class="submit" type="submit" id="contactSubmit" name="submit_contact_x91">
             ➤ &nbsp; Send Details
         </button>

         <div class="safe">🔒 Your information is safe with us.</div>
     </form>

 </section>

 <?php enqLockScriptX91(); ?>

 <script>
     document.addEventListener("DOMContentLoaded", function() {

         const contactFormX91 = document.getElementById("contactForm");

         if (!contactFormX91) return;

         const cNameX91 = document.getElementById("contactName");
         const cPhoneX91 = document.getElementById("contactPhone");
        //  const cAreaX91 = document.getElementById("contactArea");
        //  const cPincodeX91 = document.getElementById("contactPincode");

         const cNameErrX91 = document.getElementById("contactNameError");
         const cPhoneErrX91 = document.getElementById("contactPhoneError");
        //  const cAreaErrX91 = document.getElementById("contactAreaError");
        //  const cPincodeErrX91 = document.getElementById("contactPincodeError");

         function validateContactX91() {
             let isValidX91 = true;

             cNameErrX91.textContent = "";
             cPhoneErrX91.textContent = "";
            // //  cAreaErrX91.textContent = "";
            //  cPincodeErrX91.textContent = "";

             [cNameX91, cPhoneX91].forEach(function(fieldX91) {
                 fieldX91.style.borderColor = "";
             });

             if (!/^[\p{L}][\p{L}\s.'-]{1,79}$/u.test(cNameX91.value.trim())) {
                 cNameErrX91.textContent = "Please enter a valid name (minimum 2 characters).";
                 cNameX91.style.borderColor = "#dc2626";
                 isValidX91 = false;
             }

             if (!/^[6-9]\d{9}$/.test(cPhoneX91.value.trim())) {
                 cPhoneErrX91.textContent = "Enter a valid 10-digit Indian mobile number.";
                 cPhoneX91.style.borderColor = "#dc2626";
                 isValidX91 = false;
             }

            //  if (cAreaX91.value.trim() !== "" && !/^[\p{L}\p{N}][\p{L}\p{N}\s,.'()/-]{1,99}$/u.test(cAreaX91.value.trim())) {
            //      cAreaErrX91.textContent = "Please enter a valid area or city (minimum 2 characters).";
            //      cAreaX91.style.borderColor = "#dc2626";
            //      isValidX91 = false;
            //  }

            //  if (cPincodeX91.value.trim() !== "" && !/^[1-9][0-9]{5}$/.test(cPincodeX91.value.trim())) {
            //      cPincodeErrX91.textContent = "Enter a valid 6-digit Indian pincode.";
            //      cPincodeX91.style.borderColor = "#dc2626";
            //      isValidX91 = false;
            //  }

             return isValidX91;
         }

         contactFormX91.addEventListener("submit", function(eventX91) {
             if (!validateContactX91()) {
                 eventX91.preventDefault();
             }
         });

         [cNameX91, cPhoneX91, cAreaX91, cPincodeX91].forEach(function(fieldX91) {
             fieldX91.addEventListener("input", function() {
                 validateContactX91();
             });
         });

         // Sirf numbers type ho sakein (alphabet/symbol block)
         [cPhoneX91, cPincodeX91].forEach(function(fieldX91) {
             fieldX91.addEventListener("keypress", function(eventX91) {
                 if (eventX91.key.length === 1 && !eventX91.ctrlKey && !eventX91.metaKey && !/\d/.test(eventX91.key)) {
                     eventX91.preventDefault();
                 }
             });
         });

         cPhoneX91.addEventListener("input", function() {
             cPhoneX91.value = cPhoneX91.value.replace(/\D/g, "").slice(0, 10);
         });

         cPincodeX91.addEventListener("input", function() {
             cPincodeX91.value = cPincodeX91.value.replace(/\D/g, "").slice(0, 6);
         });

     });
 </script>