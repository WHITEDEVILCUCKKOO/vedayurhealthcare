 <!-- Section 3: Enquiry -->

 <?php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $enqErrorsX91 = [];
    $enqSuccessX91 = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enquiry_x91'])) {

        $enqNameX91    = trim($_POST['name'] ?? '');
        $enqPhoneX91   = trim($_POST['phone'] ?? '');
        $enqAreaX91    = trim($_POST['area'] ?? '');
        $enqPincodeX91 = trim($_POST['pincode'] ?? '');

        // Name validation
        if (
            !preg_match("/^[\p{L}][\p{L}\s.'-]{1,79}$/u", $enqNameX91)
        ) {
            $enqErrorsX91[] = 'Please enter a valid name.';
        }

        // Indian mobile number validation
        if (!preg_match('/^[6-9][0-9]{9}$/', $enqPhoneX91)) {
            $enqErrorsX91[] = 'Please enter a valid 10-digit mobile number.';
        }

        // Area validation
        if (
            $enqAreaX91 === '' ||
            mb_strlen($enqAreaX91) > 100
        ) {
            $enqErrorsX91[] = 'Please enter a valid area or city.';
        }

        // Pincode validation
        if (!preg_match('/^[1-9][0-9]{5}$/', $enqPincodeX91)) {
            $enqErrorsX91[] = 'Please enter a valid 6-digit pincode.';
        }

        if (empty($enqErrorsX91)) {

            // Replace with your actual receiving email
            $recipientX91 = 'abhiweb.69@gmail.com';

            $subjectX91 = 'New Enquiry - Vedayur Healthcare';

            $timeX91 = date('d-m-Y h:i:s A');

            $messageX91  = "New Enquiry Received - Vedayur Healthcare\n\n";
            $messageX91 .= "Date & Time: " . $timeX91 . "\n";
            $messageX91 .= "Name: " . $enqNameX91 . "\n";
            $messageX91 .= "Phone: " . $enqPhoneX91 . "\n";
            $messageX91 .= "Area / City: " . $enqAreaX91 . "\n";
            $messageX91 .= "Pincode: " . $enqPincodeX91 . "\n";

            // Use a real domain email address
            $fromEmailX91 = 'no-reply@vedayurhealthcare.com';

            $headersX91  = "From: Vedayur Healthcare <" . $fromEmailX91 . ">\r\n";
            $headersX91 .= "Reply-To: " . $fromEmailX91 . "\r\n";



            // Use a real domain email address

            // $fromEmailX91 = 'info@vedayurhealthcare.com';
            // $headersX91  = "From: Vedayur Healthcare <" . $fromEmailX91 . ">\r\n";
            // $headersX91 .= "Reply-To: " . $fromEmailX91 . "\r\n";
            // $headersX91 .= "MIME-Version: 1.0\r\n";
            // $headersX91 .= "Content-Type: text/plain; charset=UTF-8\r\n";



            $headersX91 .= "MIME-Version: 1.0\r\n";
            $headersX91 .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($recipientX91, $subjectX91, $messageX91, $headersX91)) {

                echo "<script>
                alert('Thank you! Your enquiry has been sent successfully.');
                window.location.href = window.location.pathname + '#enquiry';
            </script>";
                exit;
            } else {
                $enqErrorsX91[] = 'Email could not be sent. Please try again later.';
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
                     maxlength="80" required>
                 <small class="error-message" id="nameError"></small>
             </div>

             <div class="field">
                 <label for="enqPhone">Phone Number <span class="required-star">*</span></label>
                 <input type="tel" id="enqPhone" name="phone" placeholder="10-digit Phone Number"
                     autocomplete="tel" inputmode="numeric" maxlength="10" required>
                 <small class="error-message" id="phoneError"></small>
             </div>

             <div class="field">
                 <label for="enqArea">Area <span class="required-star">*</span></label>
                 <input type="text" id="enqArea" name="area" placeholder="Your Area / City"
                     autocomplete="address-level2" maxlength="100" required>
                 <small class="error-message" id="areaError"></small>
             </div>

             <div class="field">
                 <label for="enqPincode">Pincode <span class="required-star">*</span></label>
                 <input type="text" id="enqPincode" name="pincode" placeholder="6-digit Pincode"
                     autocomplete="postal-code" inputmode="numeric" maxlength="6" required>
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