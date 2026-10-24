// Nuustar Events Hub - Interactive JavaScript

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Toggle
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }

    // 2. Click-to-WhatsApp: All customer WhatsApp inquiries route strictly to Nuustar Admin Desk (Marketplace Protection)
    const NUUSTAR_ADMIN_WHATSAPP = '255624947668';

    window.openAdminWhatsApp = function(vendorName, inquiryDetails = '') {
        let message = `Hello Nuustar Events Admin, I am inquiring about booking "${vendorName}" for my event on your platform.`;
        if (inquiryDetails) {
            message += ` Celebration details: ${inquiryDetails}`;
        }
        window.open(`https://wa.me/${NUUSTAR_ADMIN_WHATSAPP}?text=${encodeURIComponent(message)}`, '_blank');
    };

    // Override any general WhatsApp calls to strictly go to Admin, never to vendor directly
    window.openWhatsApp = function(phone, vendorName) {
        window.openAdminWhatsApp(vendorName);
    };
});
