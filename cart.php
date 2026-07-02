<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Cart — LUXE Store</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
  <style>
    .promo-input {
      background: var(--bg3) !important;
      border: 1px solid var(--border) !important;
      border-right: none !important;
      color: var(--text) !important;
      font-size: 0.85rem !important;
      border-radius: 8px 0 0 8px !important;
    }
    .promo-input:focus {
      border-color: var(--accent) !important;
      box-shadow: none !important;
    }
    .promo-btn {
      background: var(--bg3);
      border: 1px solid var(--border);
      border-left: none;
      color: var(--accent);
      font-size: 0.85rem;
      font-weight: 600;
      padding: 0 1.25rem;
      border-radius: 0 8px 8px 0;
      cursor: pointer;
      transition: all 0.2s;
    }
    .promo-btn:hover { background: var(--accent); color: #000; border-color: var(--accent); }
    .checkout-steps {
      display: flex;
      align-items: center;
      gap: 0;
      margin-bottom: 2rem;
    }
    .step {
      flex: 1;
      text-align: center;
      font-size: 0.78rem;
      color: var(--muted);
      position: relative;
    }
    .step.active { color: var(--accent); }
    .step-circle {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: var(--bg3);
      border: 2px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 0.4rem;
      font-size: 0.8rem;
      font-weight: 700;
    }
    .step.active .step-circle {
      background: var(--accent);
      border-color: var(--accent);
      color: #000;
    }
    .step::after {
      content: '';
      position: absolute;
      top: 14px;
      left: 50%;
      width: 100%;
      height: 2px;
      background: var(--border);
      z-index: -1;
    }
    .step:last-child::after { display: none; }
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
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item ms-2">
          <a href="cart.php" class="btn btn-outline-gold btn-sm cart-btn-wrap active">
            <i class="fas fa-bag-shopping me-1"></i> Cart
            <span class="cart-badge cart-count" style="display:none">0</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- ── PAGE HEADER ────────────────────────────────────── -->
<div style="background:var(--bg2);border-bottom:1px solid var(--border);padding:1.5rem 0;">
  <div class="container">
    <h1 style="font-size:1.8rem;margin-bottom:0.2rem;">Your <span style="color:var(--accent);">Cart</span></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0" style="font-size:0.8rem;">
        <li class="breadcrumb-item"><a href="index.html" style="color:var(--muted);">Home</a></li>
        <li class="breadcrumb-item active" style="color:var(--accent);">Cart</li>
      </ol>
    </nav>
  </div>
</div>

<!-- ── MAIN ───────────────────────────────────────────── -->
<div class="container py-5">

  <!-- Steps -->
  <div class="checkout-steps mb-5">
    <div class="step active"><div class="step-circle">1</div>Cart</div>
    <div class="step"><div class="step-circle">2</div>Checkout</div>
    <div class="step"><div class="step-circle">3</div>Payment</div>
    <div class="step"><div class="step-circle">4</div>Done</div>
  </div>

  <!-- Cart Content -->
  <div id="cart-content">
    <div class="row g-4">
      <!-- Cart Items Col -->
      <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 style="margin:0;font-family:'DM Sans',sans-serif;font-size:0.95rem;color:var(--muted);">
            ITEMS IN CART (<span id="item-count">0</span>)
          </h5>
          <button onclick="clearCart()" style="background:none;border:none;color:var(--danger);font-size:0.85rem;cursor:pointer;font-weight:500;">
            <i class="fas fa-trash me-1"></i>Clear All
          </button>
        </div>
        <div id="cart-items-wrap"></div>
      </div>

      <!-- Order Summary Col -->
      <div class="col-lg-4">
        <div class="order-summary">
          <h4><i class="fas fa-receipt me-2" style="color:var(--accent);font-size:1rem;"></i>Order Summary</h4>

          <div class="summary-row">
            <span>Subtotal</span>
            <span id="subtotal">₹0</span>
          </div>
          <div class="summary-row">
            <span>Discount</span>
            <span id="discount" style="color:var(--success);">-₹0</span>
          </div>
          <div class="summary-row">
            <span>Delivery</span>
            <span id="delivery">₹99</span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <span id="total">₹0</span>
          </div>

          <!-- Promo Code -->
          <div class="mt-3 mb-3">
            <label style="font-size:0.78rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:0.4rem;">Promo Code</label>
            <div class="input-group">
              <input type="text" id="promo-code" class="promo-input form-control" placeholder="Enter code" />
              <button class="promo-btn" onclick="applyPromo()">Apply</button>
            </div>
            <div id="promo-msg" style="font-size:0.78rem;margin-top:0.4rem;"></div>
          </div>

          <button class="btn btn-gold w-100 py-3" onclick="checkout()">
            <i class="fas fa-lock me-2"></i>Proceed to Checkout
          </button>
          <a href="products.html" class="btn btn-outline-gold w-100 mt-2 py-2">
            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
          </a>

          <!-- Payment Icons -->
          <div style="margin-top:1.5rem;text-align:center;">
            <p style="font-size:0.75rem;color:var(--muted);margin-bottom:0.75rem;">Secure Payments via</p>
            <div class="d-flex justify-content-center gap-3" style="font-size:1.5rem;color:var(--muted);">
              <i class="fab fa-cc-visa"></i>
              <i class="fab fa-cc-mastercard"></i>
              <i class="fab fa-cc-paypal"></i>
              <i class="fab fa-google-pay"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Empty Cart -->
  <div id="empty-cart" class="empty-state" style="display:none;">
    <div class="empty-icon">🛒</div>
    <h3>Your cart is empty</h3>
    <p>Looks like you haven't added anything yet.</p>
    <a href="products.html" class="btn btn-gold mt-3 px-5 py-2">Start Shopping</a>
  </div>
</div>

<!-- ── RELATED / YOU MAY LIKE ─────────────────────────── -->
<section class="py-5" style="background:var(--bg2);border-top:1px solid var(--border);">
  <div class="container">
    <p class="section-title mb-1">You May <span>Also Like</span></p>
    <div class="section-divider"></div>
    <div class="row g-4" id="related-grid"></div>
  </div>
</section>

<!-- ── FOOTER ─────────────────────────────────────────── -->
<footer class="site-footer mt-0">
  <div class="container">
    <div class="footer-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
      <span>© 2025 LUXE Store. All rights reserved.</span>
      <div class="d-flex gap-3">
        <a href="index.html">Home</a>
        <a href="products.html">Products</a>
        <a href="contact.html">Contact</a>
      </div>
    </div>
  </div>
</footer>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div style="font-size:3rem;margin-bottom:1rem;">🎉</div>
      <h4 style="font-family:'Playfair Display',serif;color:var(--text);">Order Placed Successfully!</h4>
      <p style="color:var(--muted);font-size:0.9rem;margin:1rem 0;">Thank you for your purchase. You'll receive a confirmation email shortly.</p>
      <div style="background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:1rem;margin:1rem 0;font-size:0.85rem;color:var(--muted);">
        Order ID: <strong style="color:var(--accent);" id="order-id">#LX0000</strong>
      </div>
      <a href="products.html" class="btn btn-gold w-100 mt-2" data-bs-dismiss="modal">Continue Shopping</a>
    </div>
  </div>
</div>

<div id="toast-container"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
<script>
let promoApplied = 0;

const PROMOS = { 'LUXE30': 30, 'SAVE10': 10, 'FIRST20': 20 };

function renderCart() {
  const cart = getCart();
  const wrap = document.getElementById('cart-items-wrap');
  const emptyDiv = document.getElementById('empty-cart');
  const contentDiv = document.getElementById('cart-content');

  if (cart.length === 0) {
    emptyDiv.style.display = 'block';
    contentDiv.style.display = 'none';
    return;
  }
  emptyDiv.style.display = 'none';
  contentDiv.style.display = 'block';
  document.getElementById('item-count').textContent = cart.length;

  wrap.innerHTML = cart.map(item => `
    <div class="cart-item" id="ci-${item.id}">
      <img src="${item.image}" alt="${item.name}" />
      <div class="item-info">
        <div class="item-name">${item.name}</div>
        <div style="font-size:0.75rem;color:var(--muted);margin-bottom:0.4rem;">${item.category}</div>
        <div class="item-price">₹${item.price.toLocaleString()}</div>
      </div>
      <div class="qty-control">
        <button class="qty-btn" onclick="changeQty('${item.id}', -1)">−</button>
        <span class="qty-display" id="qty-${item.id}">${item.qty}</span>
        <button class="qty-btn" onclick="changeQty('${item.id}', 1)">+</button>
      </div>
      <div style="min-width:70px;text-align:right;">
        <div style="font-weight:700;color:var(--text);font-size:0.95rem;">₹${(item.price * item.qty).toLocaleString()}</div>
        <button class="remove-btn mt-1" onclick="removeItem('${item.id}')"><i class="fas fa-times"></i></button>
      </div>
    </div>`).join('');

  updateSummary();
}

function changeQty(id, delta) {
  updateQty(id, delta);
  renderCart();
}

function removeItem(id) {
  removeFromCart(id);
  renderCart();
}

function clearCart() {
  if (!confirm('Clear all items from cart?')) return;
  localStorage.removeItem('luxe_cart');
  saveCart([]);
  renderCart();
}

function updateSummary() {
  const cart = getCart();
  const sub = cart.reduce((s, i) => s + i.price * i.qty, 0);
  const disc = Math.round(sub * promoApplied / 100);
  const delivery = sub >= 999 ? 0 : 99;
  const total = sub - disc + delivery;

  document.getElementById('subtotal').textContent = `₹${sub.toLocaleString()}`;
  document.getElementById('discount').textContent = `-₹${disc.toLocaleString()}`;
  document.getElementById('delivery').textContent = delivery === 0 ? 'FREE' : `₹${delivery}`;
  document.getElementById('total').textContent = `₹${total.toLocaleString()}`;
}

function applyPromo() {
  const code = document.getElementById('promo-code').value.trim().toUpperCase();
  const msg = document.getElementById('promo-msg');
  if (PROMOS[code]) {
    promoApplied = PROMOS[code];
    msg.innerHTML = `<span style="color:var(--success);">✓ ${promoApplied}% discount applied!</span>`;
    updateSummary();
  } else {
    promoApplied = 0;
    msg.innerHTML = `<span style="color:var(--danger);">✗ Invalid promo code.</span>`;
    updateSummary();
  }
}

function checkout() {
  const cart = getCart();
  if (cart.length === 0) return;
  const orderId = '#LX' + Math.floor(10000 + Math.random() * 90000);
  document.getElementById('order-id').textContent = orderId;
  localStorage.removeItem('luxe_cart');
  saveCart([]);
  new bootstrap.Modal(document.getElementById('checkoutModal')).show();
}

// Related products
const related = PRODUCTS.slice(4, 8);
const relGrid = document.getElementById('related-grid');
related.forEach(p => {
  const stars = '★'.repeat(p.rating) + '☆'.repeat(5 - p.rating);
  relGrid.innerHTML += `
    <div class="col-sm-6 col-lg-3">
      <div class="product-card">
        <div class="img-wrap"><img src="${p.image}" alt="${p.name}" /><span class="badge-tag">${p.badge}</span></div>
        <div class="card-body">
          <div class="product-category">${p.category}</div>
          <div class="product-name">${p.name}</div>
          <div class="stars">${stars}</div>
          <div class="product-price">₹${p.price.toLocaleString()} <del>₹${p.oldPrice.toLocaleString()}</del></div>
          <button class="btn btn-gold w-100" onclick="addToCart('${p.id}','${p.name.replace(/'/g,"\\'")}',${p.price},'${p.image}','${p.category}');renderCart()">
            <i class="fas fa-bag-shopping me-2"></i>Add to Cart
          </button>
        </div>
      </div>
    </div>`;
});

renderCart();
</script>
</body>
</html>