
document.getElementById('enquiryForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const phone = document.getElementById('enqPhone').value.replace(/\D/g, '');
    const pin = document.getElementById('enqPincode').value.replace(/\D/g, '');
    if (phone.length < 10) { alert('Please enter a valid phone number.'); return; }
    if (pin.length !== 6) { alert('Please enter a valid 6-digit pincode.'); return; }
    const t = document.getElementById('toast'); t.textContent = 'Thank you! We received your enquiry.'; t.style.display = 'block'; this.reset();
    setTimeout(() => t.style.display = 'none', 3000);
});

document.getElementById('contactForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const phone = document.getElementById('contactPhone').value.replace(/\D/g, '');
    const pin = document.getElementById('contactPincode').value.replace(/\D/g, '');
    if (phone.length < 10) { alert('Please enter a valid phone number.'); return; }
    if (pin.length !== 6) { alert('Please enter a valid 6-digit pincode.'); return; }
    const t = document.getElementById('toast'); t.textContent = 'Thank you! We received your contact details.'; t.style.display = 'block'; this.reset();
    setTimeout(() => t.style.display = 'none', 3000);
});


// nav 
(() => {
    const vhdrBtn = document.getElementById('vhdrMenuBtn');
    const vhdrMenu = document.getElementById('vhdrNavMenu');

    if (!vhdrBtn || !vhdrMenu) return;

    // Toggle mobile drawer
    vhdrBtn.addEventListener('click', () => {
        vhdrBtn.classList.toggle('vhdr-btn-is-active');
        vhdrMenu.classList.toggle('vhdr-menu-is-open');
    });

    // Auto close when an anchor link is selected
    vhdrMenu.querySelectorAll('.vhdr-nav-item').forEach((linkItem) => {
        linkItem.addEventListener('click', () => {
            vhdrBtn.classList.remove('vhdr-btn-is-active');
            vhdrMenu.classList.remove('vhdr-menu-is-open');
        });
    });
})();

// email whatsapp form 

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("enquiryForm");

    if (!form) {
        console.error("Enquiry form not found!");
        return;
    }

    // Prevent this script from being attached more than once.
    if (form.dataset.whatsappInitialized === "true") return;
    form.dataset.whatsappInitialized = "true";

    const fields = {
        name: document.getElementById("enqName"),
        phone: document.getElementById("enqPhone"),
        area: document.getElementById("enqArea"),
        pincode: document.getElementById("enqPincode")
    };

    const whatsappNumber = "917303722421";

    const errorElements = {
        name: document.getElementById("nameError"),
        phone: document.getElementById("phoneError"),
        area: document.getElementById("areaError"),
        pincode: document.getElementById("pincodeError")
    };

    // Make sure all required fields exist.
    for (const key in fields) {
        if (!fields[key] || !errorElements[key]) {
            console.error("Missing form field or error element:", key);
            return;
        }
    }

    // Phone field: digits only, maximum 10.
    fields.phone.setAttribute("inputmode", "numeric");
    fields.phone.setAttribute("maxlength", "10");

    fields.phone.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, "").slice(0, 10);
        clearError("phone");
    });

    function showError(key, message) {
        errorElements[key].textContent = message;
        fields[key].setAttribute("aria-invalid", "true");
    }

    function clearError(key) {
        errorElements[key].textContent = "";
        fields[key].removeAttribute("aria-invalid");
    }

    // Clear each field's error when the user edits it.
    Object.keys(fields).forEach(function (key) {
        fields[key].addEventListener("input", function () {
            clearError(key);
        });
    });

    function validateForm() {
        let valid = true;

        Object.keys(fields).forEach(clearError);

        const name = fields.name.value.trim();
        const phone = fields.phone.value.trim();
        const area = fields.area.value.trim();
        const pincode = fields.pincode.value.trim();

        if (
            name.length < 2 ||
            !/^[\p{L}\p{M}][\p{L}\p{M}\s.'-]*$/u.test(name)
        ) {
            showError("name", "Please enter a valid name.");
            valid = false;
        }

        if (!/^[6-9]\d{9}$/.test(phone)) {
            showError("phone", "Enter a valid 10-digit mobile number.");
            valid = false;
        }

        if (area.length < 2 || area.length > 100) {
            showError("area", "Please enter a valid area or city.");
            valid = false;
        }

        if (!/^[1-9]\d{5}$/.test(pincode)) {
            showError("pincode", "Enter a valid 6-digit pincode.");
            valid = false;
        }

        if (!valid) {
            const firstInvalid = Object.keys(fields).find(function (key) {
                return fields[key].getAttribute("aria-invalid") === "true";
            });

            if (firstInvalid) fields[firstInvalid].focus();
        }

        return valid;
    }

    // Capture submit before other form submit handlers.
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        if (!validateForm()) return;

        const name = fields.name.value.trim();
        const phone = fields.phone.value.trim();
        const area = fields.area.value.trim();
        const pincode = fields.pincode.value.trim();

        const message =
            "New Enquiry - Vedayur Healthcare\n\n" +
            "Name: " + name + "\n" +
            "Phone: " + phone + "\n" +
            "Area: " + area + "\n" +
            "Pincode: " + pincode;

        const whatsappURL =
            "https://wa.me/" + whatsappNumber +
            "?text=" + encodeURIComponent(message);

        // Open the new tab directly during the submit action.
        const whatsappTab = window.open("about:blank", "_blank");

        if (!whatsappTab) {
            alert("WhatsApp is opening in this tab. Please wait.");
            window.location.assign(whatsappURL);
            return;
        }

        // Only one alert for a valid submission.
        alert("Your enquiry is ready. Opening WhatsApp now.");

        try {
            whatsappTab.location.href = whatsappURL;
        } catch (error) {
            window.location.assign(whatsappURL);
            return;
        }

        // Reload the original page after opening WhatsApp.
        window.setTimeout(function () {
            window.location.reload();
        }, 1000);

    }, true);

});


// 2 contact form
document.addEventListener("DOMContentLoaded", function () {

    const contactForm = document.getElementById("contactForm");

    if (!contactForm || contactForm.dataset.contactInitialized === "true") {
        return;
    }

    contactForm.dataset.contactInitialized = "true";

    const contactWhatsAppNumber = "917303722421";

    const contactFields = {
        name: document.getElementById("contactName"),
        phone: document.getElementById("contactPhone"),
        area: document.getElementById("contactArea"),
        pincode: document.getElementById("contactPincode")
    };

    const contactErrors = {
        name: document.getElementById("contactNameError"),
        phone: document.getElementById("contactPhoneError"),
        area: document.getElementById("contactAreaError"),
        pincode: document.getElementById("contactPincodeError")
    };

    for (const key in contactFields) {
        if (!contactFields[key] || !contactErrors[key]) {
            console.error("Contact form field missing:", key);
            return;
        }
    }

    function clearContactError(key) {
        contactErrors[key].textContent = "";
        contactFields[key].removeAttribute("aria-invalid");
    }

    function showContactError(key, message) {
        contactErrors[key].textContent = message;
        contactFields[key].setAttribute("aria-invalid", "true");
    }

    // Phone number: digits only, maximum 10
    contactFields.phone.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, "").slice(0, 10);
        clearContactError("phone");
    });

    Object.keys(contactFields).forEach(function (key) {
        contactFields[key].addEventListener("input", function () {
            clearContactError(key);
        });
    });

    function validateContactForm() {
        Object.keys(contactFields).forEach(clearContactError);

        let valid = true;

        const name = contactFields.name.value.trim();
        const phone = contactFields.phone.value.trim();
        const area = contactFields.area.value.trim();
        const pincode = contactFields.pincode.value.trim();

        if (
            name.length < 2 ||
            !/^[\p{L}\p{M}][\p{L}\p{M}\s.'-]*$/u.test(name)
        ) {
            showContactError("name", "Please enter a valid name.");
            valid = false;
        }

        if (!/^[6-9]\d{9}$/.test(phone)) {
            showContactError("phone", "Enter a valid 10-digit mobile number.");
            valid = false;
        }

        if (area.length < 2 || area.length > 100) {
            showContactError("area", "Please enter a valid area.");
            valid = false;
        }

        if (!/^[1-9]\d{5}$/.test(pincode)) {
            showContactError("pincode", "Enter a valid 6-digit pincode.");
            valid = false;
        }

        if (!valid) {
            const firstInvalid = Object.keys(contactFields).find(function (key) {
                return contactFields[key].getAttribute("aria-invalid") === "true";
            });

            if (firstInvalid) contactFields[firstInvalid].focus();
        }

        return valid;
    }

    contactForm.addEventListener("submit", function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        if (!validateContactForm()) return;

        const name = contactFields.name.value.trim();
        const phone = contactFields.phone.value.trim();
        const area = contactFields.area.value.trim();
        const pincode = contactFields.pincode.value.trim();

        const contactMessage =
            "Contact Enquiry - Vedayur Healthcare\n\n" +
            "Name: " + name + "\n" +
            "Phone: " + phone + "\n" +
            "Area: " + area + "\n" +
            "Pincode: " + pincode;

        const contactWhatsAppURL =
            "https://wa.me/" + contactWhatsAppNumber +
            "?text=" + encodeURIComponent(contactMessage);

        // Open WhatsApp in a new tab during the submit action.
        const whatsappTab = window.open("about:blank", "_blank");

        if (!whatsappTab) {
            alert("Your contact details are ready. WhatsApp will open in this tab.");
            window.location.assign(contactWhatsAppURL);
            return;
        }

        alert("Your contact details are ready. Opening WhatsApp now.");

        whatsappTab.location.href = contactWhatsAppURL;

        // Reload the original page after opening WhatsApp.
        window.setTimeout(function () {
            window.location.reload();
        }, 1000);

    }, true);

});

