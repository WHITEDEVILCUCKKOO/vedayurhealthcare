
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
