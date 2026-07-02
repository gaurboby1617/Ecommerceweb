<!DOCTYPE html>
  <html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us — LUXE Store</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
  <style>
    .map-placeholder {
      background: var(--bg3);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      height: 220px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--muted);
      font-size: 2.5rem;
      margin-top: 1.5rem;
      position: relative;
      overflow: hidden;
    }
    .map-placeholder::before {
      content: '';
      position: absolute;
      inset: 0;
      background: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 20px,
        rgba(201,168,76,0.03) 20px,
        rgba(201,168,76,0.03) 40px
      );
    }
    .social-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.5rem;
      text-align: center;
      transition: all 0.3s;
      cursor: pointer;
    }
    .social-card:hover {
      border-color: var(--accent);
      transform: translateY(-3px);
    }
    .social-card .icon { font-size: 1.8rem; margin-bottom: 0.75rem; }
    .social-card h6 { font-size: 0.85rem; color: var(--text); margin-bottom: 0.25rem; font-family: 'DM Sans', sans-serif; }
    .social-card p { font-size: 0.78rem; color: var(--muted); margin: 0; }
    .faq-item {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.25rem 1.5rem;
      margin-bottom: 0.75rem;
      cursor: pointer;
      transition: border-color 0.2s;
    }
    .faq-item:hover { border-color: var(--accent); }
    .faq-item .faq-q {
      font-weight: 600;
      font-size: 0.9rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .faq-item .faq-a {
      font-size: 0.85rem;
      color: var(--muted);
      margin-top: 0.75rem;
      display: none;
      line-height: 1.7;
    }
    .faq-item.open .faq-a { display: block; }
    .faq-item.open .faq-icon { transform: rotate(45deg); }
    .faq-icon { transition: transform 0.2s; color: var(--accent); }
    .success-banner {
      background: rgba(76, 175, 125, 0.1);
      border: 1px solid rgba(76, 175, 125, 0.3);
      border-radius: var(--radius);
      padding: 1.25rem;
      text-align: center;
      color: var(--success);
      display: none;
      animation: slideIn 0.4s ease;
    }
  </style>
</head>
<body>

<!-- ── NAVBAR ───────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="index.html">LUXE<span>.</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a class="nav-link" href="indexx.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
        <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
        <li class="nav-item ms-2">
          <a href="cart.php" class="btn btn-outline-gold btn-sm cart-btn-wrap">
            <i class="fas fa-bag-shopping me-1"></i> Cart
            <span class="cart-badge cart-count" style="display:none">0</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div style="background:linear-gradient(135deg,var(--bg2),var(--bg3));border-bottom:1px solid var(--border);padding:4rem 0;text-align:center;">
  <div class="container">
    <p style="font-size:0.75rem;letter-spacing:3px;text-transform:uppercase;color:var(--accent);margin-bottom:0.75rem;">Get in Touch</p>
    <h1 style="font-size:clamp(2rem,5vw,3.5rem);color:var(--text);margin-bottom:1rem;">We'd Love to <span style="color:var(--accent);">Hear From You</span></h1>
    <p style="color:var(--muted);font-size:1rem;max-width:500px;margin:0 auto;">Have a question, feedback, or just want to say hello? Our team is here to help.</p>
  </div>
</div>

<!-- ── CONTACT MAIN ────────────────────────────────────── -->
<div class="container py-5">
  <div class="row g-5">

    <!-- Contact Form -->
    <div class="col-lg-7 fade-up">
      <div class="contact-card">
        <h3 style="font-size:1.4rem;margin-bottom:1.75rem;">Send us a <span style="color:var(--accent);">Message</span></h3>

        <!-- Success Banner -->
        <div class="success-banner" id="success-banner">
          <i class="fas fa-check-circle me-2" style="font-size:1.2rem;"></i>
          <strong>Message sent successfully!</strong> We'll get back to you within 24 hours.
        </div>

        <form id="contact-form" onsubmit="submitForm(event)" novalidate>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name *</label>
              <input type="text" class="form-control" id="fname" placeholder="Rahul" required />
              <div class="invalid-feedback">Please enter your first name.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name *</label>
              <input type="text" class="form-control" id="lname" placeholder="Sharma" required />
              <div class="invalid-feedback">Please enter your last name.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email Address *</label>
              <input type="email" class="form-control" id="email" placeholder="you@example.com" required />
              <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="phone" placeholder="+91 98765 43210" />
            </div>
            <div class="col-12">
              <label class="form-label">Subject *</label>
              <select class="form-select" id="subject" required>
                <option value="">Select a topic...</option>
                <option>Order Inquiry</option>
                <option>Returns & Refunds</option>
                <option>Product Question</option>
                <option>Shipping & Delivery</option>
                <option>Technical Support</option>
                <option>Other</option>
              </select>
              <div class="invalid-feedback">Please select a subject.</div>
            </div>
            <div class="col-12">
              <label class="form-label">Message *</label>
              <textarea class="form-control" id="message" rows="5" placeholder="Describe your question or concern in detail..." required style="resize:none;"></textarea>
              <div class="d-flex justify-content-between mt-1">
                <div class="invalid-feedback">Please enter your message.</div>
                <span style="font-size:0.75rem;color:var(--muted);" id="char-count">0 / 500</span>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="newsletter" style="accent-color:var(--accent);" />
                <label class="form-check-label" for="newsletter" style="font-size:0.85rem;color:var(--muted);">
                  Subscribe to our newsletter for exclusive deals
                </label>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-gold px-5 py-3 w-100" id="submit-btn">
                <i class="fas fa-paper-plane me-2"></i>Send Message
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Contact Info -->
    <div class="col-lg-5">
      <div class="fade-up">
        <h3 style="font-size:1.4rem;margin-bottom:1.75rem;">Contact <span style="color:var(--accent);">Information</span></h3>

        <div class="contact-card mb-4">
          <div class="contact-info-item">
            <div class="contact-icon"><i class="fas fa-location-dot"></i></div>
            <div>
              <div style="font-weight:600;font-size:0.9rem;margin-bottom:0.25rem;">Our Store</div>
              <div style="color:var(--muted);font-size:0.85rem;line-height:1.6;">42 Luxury Avenue, Malviya Nagar<br/>Jaipur, Rajasthan 302017</div>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-icon"><i class="fas fa-phone"></i></div>
            <div>
              <div style="font-weight:600;font-size:0.9rem;margin-bottom:0.25rem;">Phone Support</div>
              <div style="color:var(--muted);font-size:0.85rem;">+91 98765 43210</div>
              <div style="color:var(--muted);font-size:0.78rem;">Mon–Sat, 10am – 7pm IST</div>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
            <div>
              <div style="font-weight:600;font-size:0.9rem;margin-bottom:0.25rem;">Email Us</div>
              <div style="color:var(--muted);font-size:0.85rem;">support@luxestore.in</div>
              <div style="color:var(--muted);font-size:0.78rem;">Reply within 24 hours</div>
            </div>
          </div>
          <div class="contact-info-item mb-0">
            <div class="contact-icon"><i class="fas fa-clock"></i></div>
            <div>
              <div style="font-weight:600;font-size:0.9rem;margin-bottom:0.25rem;">Working Hours</div>
              <div style="color:var(--muted);font-size:0.85rem;">Mon–Sat: 10:00 AM – 7:00 PM</div>
              <div style="color:var(--muted);font-size:0.78rem;">Sunday: Closed</div>
            </div>
          </div>
        </div>

        <!-- Map Placeholder -->
        <div class="map-placeholder">
          <div style="text-align:center;position:relative;z-index:1;">
            <div>📍</div>
            <div style="font-size:0.8rem;margin-top:0.5rem;">Jaipur, Rajasthan</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Social Links -->
  <div class="mt-5 fade-up">
    <h3 style="font-size:1.4rem;text-align:center;margin-bottom:2rem;">Find Us on <span style="color:var(--accent);">Social Media</span></h3>
    <div class="row g-3">
      <div class="col-6 col-md-3">
        <div class="social-card">
          <div class="icon" style="color:#E1306C;"><i class="fab fa-instagram"></i></div>
          <h6>Instagram</h6>
          <p>@luxestore.in</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="social-card">
          <div class="icon" style="color:#1877F2;"><i class="fab fa-facebook"></i></div>
          <h6>Facebook</h6>
          <p>/luxestore</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="social-card">
          <div class="icon" style="color:#1DA1F2;"><i class="fab fa-twitter"></i></div>
          <h6>Twitter / X</h6>
          <p>@luxestore</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="social-card">
          <div class="icon" style="color:#25D366;"><i class="fab fa-whatsapp"></i></div>
          <h6>WhatsApp</h6>
          <p>+91 98765 43210</p>
        </div>
      </div>
    </div>
  </div>

  <!-- FAQ -->
  <div class="mt-5 fade-up">
    <h3 style="font-size:1.4rem;margin-bottom:2rem;text-align:center;">Frequently Asked <span style="color:var(--accent);">Questions</span></h3>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">How long does delivery take? <span class="faq-icon"><i class="fas fa-plus"></i></span></div>
          <div class="faq-a">Standard delivery takes 3–5 business days. Express delivery (1–2 days) is available for an additional charge. We ship pan-India.</div>
        </div>
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">What is your return policy? <span class="faq-icon"><i class="fas fa-plus"></i></span></div>
          <div class="faq-a">We offer a 30-day hassle-free return policy. Items must be unused, in original packaging. Refunds are processed within 5–7 business days.</div>
        </div>
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">Are the products genuine? <span class="faq-icon"><i class="fas fa-plus"></i></span></div>
          <div class="faq-a">Absolutely! All our products are 100% authentic and sourced directly from verified manufacturers and authorized distributors.</div>
        </div>
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">How do I track my order? <span class="faq-icon"><i class="fas fa-plus"></i></span></div>
          <div class="faq-a">Once your order is shipped, you'll receive a tracking link via email and SMS. You can also track your order from your account dashboard.</div>
        </div>
        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-q">Do you offer Cash on Delivery? <span class="faq-icon"><i class="fas fa-plus"></i></span></div>
          <div class="faq-a">Yes, COD is available on orders above ₹500. We also accept UPI, credit/debit cards, and net banking.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── FOOTER ─────────────────────────────────────────── -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
      <span>© 2025 LUXE Store. All rights reserved.</span>
      <div class="d-flex gap-3">
        <a href="index.html">Home</a>
        <a href="products.html">Products</a>
        <a href="cart.html">Cart</a>
      </div>
    </div>
  </div>
</footer>

<div id="toast-container"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
<script>
// Character counter
const msgField = document.getElementById('message');
if (msgField) {
  msgField.addEventListener('input', () => {
    const len = msgField.value.length;
    document.getElementById('char-count').textContent = `${len} / 500`;
    if (len > 500) msgField.value = msgField.value.slice(0, 500);
  });
}

// Form submit
function submitForm(e) {
  e.preventDefault();
  const form = document.getElementById('contact-form');
  const fields = ['fname', 'lname', 'email', 'subject', 'message'];
  let valid = true;

  fields.forEach(id => {
    const el = document.getElementById(id);
    if (!el.value.trim()) {
      el.classList.add('is-invalid');
      valid = false;
    } else {
      el.classList.remove('is-invalid');
      el.classList.add('is-valid');
    }
  });

  if (!valid) return;

  const btn = document.getElementById('submit-btn');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
  btn.disabled = true;

  setTimeout(() => {
    form.reset();
    fields.forEach(id => document.getElementById(id).classList.remove('is-valid', 'is-invalid'));
    document.getElementById('char-count').textContent = '0 / 500';
    const banner = document.getElementById('success-banner');
    banner.style.display = 'block';
    banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Message';
    btn.disabled = false;
    setTimeout(() => banner.style.display = 'none', 5000);
  }, 1500);
}

// FAQ toggle
function toggleFaq(el) {
  el.classList.toggle('open');
}
</script>
</body>
</html>