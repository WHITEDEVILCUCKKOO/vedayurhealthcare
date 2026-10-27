<?php
/* ------------------------------------------------------------------
   mail-helper.php
   Dono forms (Enquiry + Contact) yahi file use karte hain.
   Password sirf yahan ek baar daalna hai.
------------------------------------------------------------------- */

const ENQ_SMTP_USER_X91 = 'info@vedayurhealthcare.com';
const ENQ_SMTP_PASS_X91 = '8FPR~c~Z&iBIWSBw';
const ENQ_FROM_NAME_X91 = 'Vedayur Healthcare';
const ENQ_TO_EMAIL_X91  = 'vedayurayurveda@gmail.com';   // enquiry yahan aayegi

// Pehle localhost try hoga, na chale to mail.domain
const ENQ_SMTP_HOSTS_X91 = [
    'ssl://localhost:465',
    'ssl://mail.vedayurhealthcare.com:465',
];

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


/* ------------------------------------------------------------------
   10 DIN KA COOLDOWN (cookie based)
   Koi bhi ek form submit hone ke baad dono forms 10 din ke liye band.
------------------------------------------------------------------- */
const ENQ_COOLDOWN_SECONDS_X91 = 864000;    // 10 din = 10 x 24 x 60 x 60
const ENQ_LOCK_COOKIE_X91      = 'vh_form_lock';

// Server check: cookie maujood hai matlab abhi lock chal raha hai
// (cookie 10 din baad browser khud delete kar deta hai)
function enqCooldownActiveX91()
{
    return isset($_COOKIE[ENQ_LOCK_COOKIE_X91])
        && ctype_digit((string) $_COOKIE[ENQ_LOCK_COOKIE_X91]);
}

// Success par chalne wala JS: cookie set karta hai
function enqLockCookieJsX91()
{
    return "var vhExp = Date.now() + " . (ENQ_COOLDOWN_SECONDS_X91 * 1000) . ";"
        . "document.cookie = '" . ENQ_LOCK_COOKIE_X91 . "=' + vhExp"
        . " + '; max-age=" . ENQ_COOLDOWN_SECONDS_X91 . "; path=/; SameSite=Lax';";
}

// Page par lock hone par button disable + countdown message dikhata hai
// (ek hi baar print hota hai, chahe 2 sections me call ho)
function enqLockScriptX91()
{
    static $printed = false;
    if ($printed) {
        return;
    }
    $printed = true;
    ?>
<script>
    (function() {
        var COOKIE = "<?= ENQ_LOCK_COOKIE_X91 ?>";
        var FORMS = [
            { form: "enquiryForm", button: "enquirySubmit" },
            { form: "contactForm", button: "contactSubmit" }
        ];

        function getLockEnd() {
            var m = document.cookie.match(new RegExp("(?:^|; )" + COOKIE + "=([^;]*)"));
            if (!m) return 0;
            var exp = parseInt(m[1], 10);
            return exp > Date.now() ? exp : 0;
        }

        // Bacha hua time: "9 days 23:59:59" format me
        function fmt(ms) {
            var s = Math.ceil(ms / 1000);
            var d = Math.floor(s / 86400);
            s = s % 86400;
            var h = Math.floor(s / 3600);
            s = s % 3600;
            var m = Math.floor(s / 60);
            s = s % 60;

            function p(n) { return n < 10 ? "0" + n : "" + n; }

            var out = "";
            if (d > 0) {
                out += d + (d === 1 ? " day " : " days ");
            }
            out += p(h) + ":" + p(m) + ":" + p(s);
            return out;
        }

        function init() {
            FORMS.forEach(function(item) {
                var form = document.getElementById(item.form);
                var btn = document.getElementById(item.button);
                if (!form || !btn) return;

                var note = document.createElement("div");
                note.setAttribute("role", "status");
                note.style.cssText = "display:none;margin:10px 0;padding:10px 12px;border-radius:8px;" +
                    "background:#fef3c7;color:#92400e;font-size:14px;font-weight:500;line-height:1.5;";
                btn.parentNode.insertBefore(note, btn);

                function tick() {
                    var end = getLockEnd();
                    if (end) {
                        btn.disabled = true;
                        btn.style.opacity = "0.6";
                        btn.style.cursor = "not-allowed";
                        note.style.display = "block";
                        note.textContent = "Your details were already submitted. You can send another request in " +
                            fmt(end - Date.now()) + ".";
                    } else {
                        btn.disabled = false;
                        btn.style.opacity = "";
                        btn.style.cursor = "";
                        note.style.display = "none";
                    }
                }

                form.addEventListener("submit", function(e) {
                    if (getLockEnd()) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                    }
                }, true);

                tick();
                setInterval(tick, 1000);
            });
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", init);
        } else {
            init();
        }
    })();
</script>
<?php
}


// Success popup + cookie lock + redirect (dono forms yahi use karte hain)
function enqSuccessScriptX91($redirect = '/index.php')
{
    $message = "✅ Thank you! Your details have been sent.\n\n"
        . "☎️ Our specialist will call you shortly.\n"
        . "🗣️ Please attend the call.\n\n"
        . "धन्यवाद! हमारे विशेषज्ञ जल्द ही आपको फ़ोन करेंगे।\n"
        . "📞 कृपया फ़ोन ज़रूर उठाएँ।";

    return '<script>' . enqLockCookieJsX91()
        . 'alert(' . json_encode($message, JSON_UNESCAPED_UNICODE) . ');'
        . 'window.location.href = ' . json_encode($redirect) . ';</script>';
}