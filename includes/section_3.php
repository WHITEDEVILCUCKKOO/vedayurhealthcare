<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$enqErrorsX91 = [];
$enqSuccessX91 = '';

$enqNameX91 = '';
$enqPhoneX91 = '';
$enqAreaX91 = '';
$enqPincodeX91 = '';

if (empty($_SESSION['enq_csrf_x91'])) {
    $_SESSION['enq_csrf_x91'] = bin2hex(random_bytes(32));
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['enquiry_submit_x91'])
) {
    $tokenX91 = $_POST['enq_csrf_x91'] ?? '';

    if (
        !is_string($tokenX91) ||
        !hash_equals($_SESSION['enq_csrf_x91'], $tokenX91)
    ) {
        $enqErrorsX91['general'] = 'Invalid request. Please refresh and try again.';
    }

    $enqNameX91 = trim((string) ($_POST['name'] ?? ''));
    $enqPhoneX91 = trim((string) ($_POST['phone'] ?? ''));
    $enqAreaX91 = trim((string) ($_POST['area'] ?? ''));
    $enqPincodeX91 = trim((string) ($_POST['pincode'] ?? ''));

    // Name validation
    if ($enqNameX91 === '') {
        $enqErrorsX91['name'] = 'Please enter your name.';
    } elseif (
        mb_strlen($enqNameX91, 'UTF-8') > 80 ||
        !preg_match("/^[\p{L}\p{M}]+(?:[ '\x{2019}.-][\p{L}\p{M}]+)*$/u", $enqNameX91)
    ) {
        $enqErrorsX91['name'] = 'Please enter a valid name.';
    }

    // Indian mobile number validation
    if (!preg_match('/^[6-9][0-9]{9}$/', $enqPhoneX91)) {
        $enqErrorsX91['phone'] = 'Enter a valid 10-digit Indian mobile number.';
    }

    // Area validation
    if ($enqAreaX91 === '') {
        $enqErrorsX91['area'] = 'Please enter your area or city.';
    } elseif (
        mb_strlen($enqAreaX91, 'UTF-8') > 100 ||
        !preg_match("/^[\p{L}\p{M}0-9]+(?:[ \-.,'\/][\p{L}\p{M}0-9]+)*$/u", $enqAreaX91)
    ) {
        $enqErrorsX91['area'] = 'Please enter a valid area or city.';
    }

    // Indian PIN code validation
    if (!preg_match('/^[1-9][0-9]{5}$/', $enqPincodeX91)) {
        $enqErrorsX91['pincode'] = 'Enter a valid 6-digit PIN code.';
    }

    // Send email only when validation passes
    if (empty($enqErrorsX91)) {
        // $recipientX91 = 'vedayurayurveda@gmail.com';
        $recipientX91 = 'heligo3242@hiringview.com';
        $subjectX91 = 'New Enquiry - Vedayur Healthcare';

        $messageX91 =
            "New enquiry received from Vedayur Healthcare website.\n\n" .
            "Name: " . $enqNameX91 . "\n" .
            "Phone: " . $enqPhoneX91 . "\n" .
            "Area / City: " . $enqAreaX91 . "\n" .
            "Pincode: " . $enqPincodeX91 . "\n" .
            "Date: " . date('d-m-Y h:i A') . "\n";

        /*
         * Replace this with a valid email address
         * belonging to your website's domain.
         */
        $senderEmailX91 = 'noreply@yourdomain.com';

        $headersX91  = "From: Vedayur Healthcare <{$senderEmailX91}>\r\n";
        $headersX91 .= "MIME-Version: 1.0\r\n";
        $headersX91 .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headersX91 .= "X-Mailer: PHP/" . phpversion();

        if (mail($recipientX91, $subjectX91, $messageX91, $headersX91)) {
            $enqSuccessX91 = 'Thank you! Your enquiry has been submitted successfully.';

            $_SESSION['enq_csrf_x91'] = bin2hex(random_bytes(32));

            $enqNameX91 = '';
            $enqPhoneX91 = '';
            $enqAreaX91 = '';
            $enqPincodeX91 = '';
        } else {
            error_log('Vedayur Healthcare enquiry email could not be sent.');
            $enqErrorsX91['general'] =
                'Sorry, your enquiry could not be sent. Please try again later.';
        }
    }
}
?>

<style>
    .enq-server-success-x91,
    .enq-server-error-x91 {
        padding: 12px 15px;
        margin-bottom: 16px;
        border-radius: 6px;
        font-size: 14px;
        line-height: 1.5;
    }

    .enq-server-success-x91 {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    .enq-server-error-x91 {
        color: #b91c1c;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .enquiry-form .error-message {
        display: block;
        color: #dc2626;
        font-size: 12px;
        margin-top: 5px;
    }
</style>

<!-- Section 3: Enquiry -->
<section class="enquiry-highlight" id="enquiry">
    <div class="enquiry-inner">

        <div class="enquiry-copy">
            <span class="enquiry-kicker">QUICK ENQUIRY</span>
            <h2>Get in Touch With Vedayur Healthcare</h2>
            <p>Share your details and our team will contact you for product information and guidance.</p>
        </div>

        <form class="enquiry-form"
              id="enquiryForm"
              method="POST"
              action="#enquiry"
              accept-charset="UTF-8"
              novalidate>

            <input type="hidden" name="enq_csrf_x91"
                   value="<?= htmlspecialchars($_SESSION['enq_csrf_x91'], ENT_QUOTES, 'UTF-8') ?>">

            <input type="hidden" name="enquiry_submit_x91" value="1">

            <?php if ($enqSuccessX91 !== ''): ?>
                <div class="enq-server-success-x91" role="status">
                    <?= htmlspecialchars($enqSuccessX91, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($enqErrorsX91['general'])): ?>
                <div class="enq-server-error-x91" role="alert">
                    <?= htmlspecialchars($enqErrorsX91['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div class="field">
                <label for="enqName">Name <span class="required-star">*</span></label>
                <input type="text" id="enqName" name="name"
                       placeholder="Your Name" autocomplete="name"
                       maxlength="80" required
                       value="<?= htmlspecialchars($enqNameX91, ENT_QUOTES, 'UTF-8') ?>">
                <small class="error-message" id="nameError"><?= htmlspecialchars($enqErrorsX91['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            </div>

            <div class="field">
                <label for="enqPhone">Phone Number <span class="required-star">*</span></label>
                <input type="tel" id="enqPhone" name="phone"
                       placeholder="10-digit Phone Number" autocomplete="tel"
                       inputmode="numeric" maxlength="10" required
                       value="<?= htmlspecialchars($enqPhoneX91, ENT_QUOTES, 'UTF-8') ?>">
                <small class="error-message" id="phoneError"><?= htmlspecialchars($enqErrorsX91['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            </div>

            <div class="field">
                <label for="enqArea">Area <span class="required-star">*</span></label>
                <input type="text" id="enqArea" name="area"
                       placeholder="Your Area / City" autocomplete="address-level2"
                       maxlength="100" required
                       value="<?= htmlspecialchars($enqAreaX91, ENT_QUOTES, 'UTF-8') ?>">
                <small class="error-message" id="areaError"><?= htmlspecialchars($enqErrorsX91['area'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            </div>

            <div class="field">
                <label for="enqPincode">Pincode <span class="required-star">*</span></label>
                <input type="text" id="enqPincode" name="pincode"
                       placeholder="6-digit Pincode" autocomplete="postal-code"
                       inputmode="numeric" maxlength="6" required
                       value="<?= htmlspecialchars($enqPincodeX91, ENT_QUOTES, 'UTF-8') ?>">
                <small class="error-message" id="pincodeError"><?= htmlspecialchars($enqErrorsX91['pincode'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            </div>

            <button class="submit" id="enquirySubmit" type="submit">
                ➤ Submit Enquiry
            </button>

            <div class="safe">
                🔒 Your information is safe with us.
            </div>

        </form>
    </div>
</section>

<script>
(function () {
    const formX91 = document.getElementById('enquiryForm');
    if (!formX91) return;

    const fieldsX91 = {
        name: document.getElementById('enqName'),
        phone: document.getElementById('enqPhone'),
        area: document.getElementById('enqArea'),
        pincode: document.getElementById('enqPincode')
    };

    const errorsX91 = {
        name: document.getElementById('nameError'),
        phone: document.getElementById('phoneError'),
        area: document.getElementById('areaError'),
        pincode: document.getElementById('pincodeError')
    };

    function validateFieldX91(keyX91) {
        const valueX91 = fieldsX91[keyX91].value.trim();
        let messageX91 = '';

        if (!valueX91) {
            messageX91 = 'This field is required.';
        } else if (keyX91 === 'name') {
            if (valueX91.length > 80 ||
                !/^[\p{L}\p{M}]+(?:[ '\u2019.-][\p{L}\p{M}]+)*$/u.test(valueX91)) {
                messageX91 = 'Please enter a valid name.';
            }
        } else if (keyX91 === 'phone') {
            if (!/^[6-9]\d{9}$/.test(valueX91)) {
                messageX91 = 'Enter a valid 10-digit Indian mobile number.';
            }
        } else if (keyX91 === 'area') {
            if (valueX91.length > 100 ||
                !/^[\p{L}\p{M}0-9]+(?:[ \-.,'\/][\p{L}\p{M}0-9]+)*$/u.test(valueX91)) {
                messageX91 = 'Please enter a valid area or city.';
            }
        } else if (keyX91 === 'pincode') {
            if (!/^[1-9]\d{5}$/.test(valueX91)) {
                messageX91 = 'Enter a valid 6-digit PIN code.';
            }
        }

        errorsX91[keyX91].textContent = messageX91;
        fieldsX91[keyX91].setAttribute('aria-invalid', messageX91 ? 'true' : 'false');

        return messageX91 === '';
    }

    Object.keys(fieldsX91).forEach(function (keyX91) {
        fieldsX91[keyX91].addEventListener('input', function () {
            validateFieldX91(keyX91);
        });

        fieldsX91[keyX91].addEventListener('blur', function () {
            validateFieldX91(keyX91);
        });
    });

    fieldsX91.phone.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    fieldsX91.pincode.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

    formX91.addEventListener('submit', function (eventX91) {
        let validX91 = true;

        Object.keys(fieldsX91).forEach(function (keyX91) {
            if (!validateFieldX91(keyX91)) {
                validX91 = false;
            }
        });

        if (!validX91) {
            eventX91.preventDefault();

            const firstInvalidX91 = formX91.querySelector('[aria-invalid="true"]');
            if (firstInvalidX91) firstInvalidX91.focus();
        }
    });
})();
</script>