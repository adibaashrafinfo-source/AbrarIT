/* Shared footer + floating widgets injected on every page */
document.addEventListener('DOMContentLoaded', () => {
  const mount = document.getElementById('footer-placeholder');
  if (!mount) return;

  mount.innerHTML = `
    <div class="footer-top">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand">
            <a href="index.html" class="brand">
              <img src="assets/img/logo.png" alt="Abrar IT — Your Journey To Digital Excellence" class="brand-logo" width="178" height="50">
            </a>
            <p>Your trusted partner for premium branding, web design, software and growth marketing that builds lasting digital success.</p>
            <div class="footer-social">
              <a href="#" aria-label="Facebook"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
              <a href="#" aria-label="Instagram"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg></a>
              <a href="#" aria-label="LinkedIn"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4V8h4v1.5A4.5 4.5 0 0116 8z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>
              <a href="#" aria-label="YouTube"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3z"/></svg></a>
            </div>
          </div>

          <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
              <li><a href="index.html">Home</a></li>
              <li><a href="about.html">About Us</a></li>
              <li><a href="services.html">Services</a></li>
              <li><a href="portfolio.html">Portfolio</a></li>
              <li><a href="blog.html">Blog</a></li>
              <li><a href="careers.html">Careers</a></li>
              <li><a href="contact.html">Contact</a></li>
            </ul>
          </div>

          <div class="footer-col">
            <h4>Services</h4>
            <ul>
              <li><a href="graphic-design-agency-bangladesh.html">Graphic Design</a></li>
              <li><a href="digital-marketing-agency-bangladesh.html">Digital Marketing</a></li>
              <li><a href="web-design-bangladesh.html">Website Design</a></li>
              <li><a href="software-development-company-bangladesh.html">Software Solutions</a></li>
              <li><a href="motion-graphics-bangladesh.html">Motion Graphics</a></li>
              <li><a href="video-editing-services-bangladesh.html">Video Editing</a></li>
              <li><a href="content-writing-services-bangladesh.html">Content Writing</a></li>
              <li><a href="email-sms-marketing-bangladesh.html">Email &amp; SMS Marketing</a></li>
            </ul>
          </div>

          <div class="footer-col">
            <h4>Get in Touch</h4>
            <ul class="footer-contact">
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg><span>Dhaka, Bangladesh</span></li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3.1-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/></svg><span>+880 1719-686459</span></li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/></svg><span>asrafulislam2000@gmail.com</span></li>
            </ul>
            <div class="newsletter-box">
              <input type="email" placeholder="Your email address">
              <button aria-label="Subscribe"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container footer-bottom-inner">
        <span>&copy; ${new Date().getFullYear()} Abrar IT. All rights reserved.</span>
        <div class="legal-links">
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Service</a>
          <a href="#">FAQ</a>
        </div>
      </div>
    </div>
  `;

  const floatStack = document.createElement('div');
  floatStack.className = 'float-stack';
  floatStack.innerHTML = `
    <button class="back-to-top" aria-label="Back to top">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
    </button>
    <a class="whatsapp-btn" href="https://wa.me/8801719686459" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.39 1.26 4.82L2 22l5.4-1.42a9.8 9.8 0 004.64 1.18h.01c5.46 0 9.9-4.45 9.9-9.91C21.96 6.45 17.5 2 12.04 2zm0 18.03a8.1 8.1 0 01-4.14-1.14l-.3-.18-3.07.8.82-3-.2-.31a8.08 8.08 0 01-1.24-4.3c0-4.48 3.65-8.13 8.14-8.13 2.17 0 4.21.85 5.75 2.39a8.07 8.07 0 012.38 5.75c0 4.49-3.65 8.12-8.14 8.12zm4.46-6.08c-.24-.12-1.44-.71-1.67-.79-.22-.08-.38-.12-.55.12-.16.24-.63.79-.77.95-.14.16-.28.18-.52.06-.24-.12-1.02-.38-1.94-1.2-.72-.64-1.2-1.43-1.34-1.67-.14-.24-.01-.37.11-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.55-1.33-.76-1.82-.2-.48-.4-.42-.55-.42-.14 0-.3-.02-.46-.02-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.44-.59 1.64-1.16.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.46-.28z"/></svg>
    </a>
  `;
  document.body.appendChild(floatStack);

  const backTop = floatStack.querySelector('.back-to-top');
  backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  document.addEventListener('scroll', () => {
    backTop.classList.toggle('show', window.scrollY > 500);
  }, { passive: true });

  const nb = mount.querySelector('.newsletter-box');
  if (nb) {
    window.bindNewsletterBox(nb);
  }
});
