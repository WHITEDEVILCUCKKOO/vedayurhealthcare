
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
