// ============================================================
// LUXE STORE — Shared JavaScript (script.js)
// ============================================================

// ── Cart Storage ──────────────────────────────────────────
function getCart() {
  return JSON.parse(localStorage.getItem('luxe_cart') || '[]');
}

function saveCart(cart) {
  localStorage.setItem('luxe_cart', JSON.stringify(cart));
  updateCartCount();
}

function updateCartCount() {
  const cart = getCart();
  const total = cart.reduce((sum, i) => sum + i.qty, 0);
  document.querySelectorAll('.cart-count').forEach(el => {
    el.textContent = total;
    el.style.display = total > 0 ? 'inline' : 'none';
  });
}

// ── Add to Cart ───────────────────────────────────────────
function addToCart(id, name, price, image, category) {
  const cart = getCart();
  const existing = cart.find(i => i.id === id);
  if (existing) {
    existing.qty += 1;
  } else {
    cart.push({ id, name, price, image, category, qty: 1 });
  }
  saveCart(cart);
  showToast(`✓ "${name}" added to cart!`);
}

// ── Remove from Cart ─────────────────────────────────────
function removeFromCart(id) {
  let cart = getCart().filter(i => i.id !== id);
  saveCart(cart);
}

// ── Update Quantity ───────────────────────────────────────
function updateQty(id, delta) {
  const cart = getCart();
  const item = cart.find(i => i.id === id);
  if (item) {
    item.qty = Math.max(1, item.qty + delta);
    saveCart(cart);
  }
}

// ── Toast Notification ────────────────────────────────────
function showToast(msg) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = 'toast-msg';
  toast.innerHTML = `<span class="toast-icon">🛒</span><span>${msg}</span>`;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

// ── Scroll Animations ─────────────────────────────────────
function initScrollAnimations() {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) e.target.classList.add('visible');
    });
  }, { threshold: 0.1 });
  document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
}

// ── Init on DOM ready ────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  updateCartCount();
  initScrollAnimations();
});

// ============================================================
// PRODUCTS DATA (shared across pages)
// ============================================================
const PRODUCTS = [
  {
    id: 'p1',
    name: 'Wireless Noise-Cancelling Headphones',
    category: 'Electronics',
    price: 4999,
    oldPrice: 7999,
    badge: 'Best Seller',
    image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop',
    rating: 5,
    desc: 'Premium sound quality with 30-hour battery life and active noise cancellation for an immersive audio experience.'
  },
  {
    id: 'p2',
    name: 'Leather Crossbody Bag',
    category: 'Fashion',
    price: 2499,
    oldPrice: 3999,
    badge: 'New',
    image: 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&h=300&fit=crop',
    rating: 4,
    desc: 'Genuine leather with multiple compartments. Perfect for everyday use with elegant gold hardware details.'
  },
  {
    id: 'p3',
    name: 'Smart Watch Series X',
    category: 'Electronics',
    price: 8999,
    oldPrice: 12999,
    badge: 'Sale',
    image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=300&fit=crop',
    rating: 5,
    desc: 'Track fitness, receive notifications, and monitor your health with this premium smartwatch.'
  },
  {
    id: 'p4',
    name: 'Minimalist Running Shoes',
    category: 'Footwear',
    price: 3299,
    oldPrice: 5499,
    badge: 'Hot',
    image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=300&fit=crop',
    rating: 4,
    desc: 'Lightweight and breathable. Designed for both casual wear and performance running.'
  },
  {
    id: 'p5',
    name: 'Scented Candle Gift Set',
    category: 'Home & Living',
    price: 1299,
    oldPrice: 1899,
    badge: 'New',
    image: 'https://images.unsplash.com/photo-1602607526574-a8a979a4e3a1?w=400&h=300&fit=crop',
    rating: 4,
    desc: 'Set of 3 premium scented candles — Sandalwood, Vanilla Spice, and Ocean Breeze.'
  },
  {
    id: 'p6',
    name: 'Polarised Aviator Sunglasses',
    category: 'Fashion',
    price: 1799,
    oldPrice: 2999,
    badge: 'Sale',
    image: 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=300&fit=crop',
    rating: 5,
    desc: 'UV400 protection with scratch-resistant lens coating. Lightweight titanium frame.'
  },
  {
    id: 'p7',
    name: 'Bamboo Yoga Mat',
    category: 'Sports',
    price: 2199,
    oldPrice: 3299,
    badge: 'Eco',
    image: 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400&h=300&fit=crop',
    rating: 4,
    desc: 'Eco-friendly non-slip yoga mat with alignment lines. 6mm thickness for joint support.'
  },
  {
    id: 'p8',
    name: 'Mechanical Keyboard',
    category: 'Electronics',
    price: 5499,
    oldPrice: 7999,
    badge: 'Best Seller',
    image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&h=300&fit=crop',
    rating: 5,
    desc: 'Tactile RGB mechanical keyboard with brown switches. Perfect for gaming and coding.'
  }
];