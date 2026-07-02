<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LUXE Store — Premium Shopping</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
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
        <li class="nav-item"><a class="nav-link active" href="indexx.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
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

<!-- ── HERO ─────────────────────────────────────────────── -->
<section class="hero">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <p class="hero-tag"><i class="fas fa-star me-2"></i>New Collection 2025</p>
        <h1>Discover <em>Premium</em> Products for Every Lifestyle</h1>
        <p>Shop the finest curated collection of electronics, fashion, and lifestyle products — all delivered to your door with luxury care.</p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="products.html" class="btn btn-gold px-4 py-3">
            <i class="fas fa-arrow-right me-2"></i>Shop Now
          </a>
          <a href="#categories" class="btn btn-outline-gold px-4 py-3">
            Browse Categories
          </a>
        </div>
        <div class="d-flex gap-4 mt-4">
          <div>
            <div style="font-size:1.4rem;font-weight:800;color:var(--accent);">500+</div>
            <div style="font-size:0.8rem;color:var(--muted);">Products</div>
          </div>
          <div style="width:1px;background:var(--border)"></div>
          <div>
            <div style="font-size:1.4rem;font-weight:800;color:var(--accent);">50K+</div>
            <div style="font-size:0.8rem;color:var(--muted);">Happy Customers</div>
          </div>
          <div style="width:1px;background:var(--border)"></div>
          <div>
            <div style="font-size:1.4rem;font-weight:800;color:var(--accent);">4.9★</div>
            <div style="font-size:0.8rem;color:var(--muted);">Average Rating</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-none d-lg-block">
        <div class="hero-img-wrap">
          <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=700&h=500&fit=crop" alt="Shop Banner" class="img-fluid" />
          <div style="position:absolute;bottom:20px;left:20px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1rem 1.4rem;backdrop-filter:blur(10px);">
            <div style="font-size:0.75rem;color:var(--muted);margin-bottom:0.2rem;">Flash Sale Ends In</div>
            <div style="font-size:1.2rem;font-weight:700;color:var(--accent);" id="countdown">02:14:37</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── FEATURES STRIP ────────────────────────────────────── -->
<section class="features-strip">
  <div class="container">
    <div class="row g-3">
      <div class="col-sm-6 col-lg-3">
        <div class="feature-item">
          <div class="feature-icon"><i class="fas fa-truck-fast"></i></div>
          <div>
            <h6>Free Delivery</h6>
            <p>On orders above ₹999</p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="feature-item">
          <div class="feature-icon"><i class="fas fa-rotate-left"></i></div>
          <div>
            <h6>Easy Returns</h6>
            <p>30-day hassle-free returns</p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="feature-item">
          <div class="feature-icon"><i class="fas fa-shield-halved"></i></div>
          <div>
            <h6>Secure Payment</h6>
            <p>100% encrypted checkout</p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="feature-item">
          <div class="feature-icon"><i class="fas fa-headset"></i></div>
          <div>
            <h6>24/7 Support</h6>
            <p>Always here to help you</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CATEGORIES (now dynamic, loaded from admin panel DB) ───────── -->
<section id="categories" class="py-5 mt-4">
  <div class="container">
    <div class="fade-up">
      <p class="section-title">Shop by <span>Category</span></p>
      <div class="section-divider"></div>
    </div>
    <div class="row g-3" id="category-grid">
      <!-- Filled dynamically by JS from get_categories.php -->
      <div class="text-center text-muted py-4" id="category-loading">Loading categories...</div>
    </div>
  </div>
</section>

<!-- ── FEATURED PRODUCTS ──────────────────────────────────── -->
<section class="py-4">
  <div class="container">
    <div class="d-flex align-items-end justify-content-between mb-4 fade-up">
      <div>
        <p class="section-title">Featured <span>Products</span></p>
        <div class="section-divider mb-0"></div>
      </div>
      <a href="../category_view.php" class="btn btn-outline-gold btn-sm">View All →</a>
    </div>
    <div class="row g-4" id="featured-grid">
      <!-- Filled by JS -->
    </div>
  </div>
</section>

<!-- ── BANNER ─────────────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="fade-up" style="background:linear-gradient(135deg,#1a1500,#2a2200);border:1px solid rgba(201,168,76,0.3);border-radius:20px;padding:3rem;text-align:center;position:relative;overflow:hidden;">
      <div style="position:absolute;width:300px;height:300px;background:radial-gradient(circle,rgba(201,168,76,0.1) 0%,transparent 70%);top:-100px;left:-50px;pointer-events:none;"></div>
      <p style="font-size:0.75rem;letter-spacing:3px;text-transform:uppercase;color:var(--accent);margin-bottom:0.75rem;">Limited Time Offer</p>
      <h2 style="font-size:clamp(1.5rem,4vw,2.5rem);color:var(--text);margin-bottom:1rem;">Get Flat <span style="color:var(--accent);">30% OFF</span> on All Electronics</h2>
      <p style="color:var(--muted);margin-bottom:1.5rem;">Use code <strong style="color:var(--text);background:rgba(201,168,76,0.15);padding:2px 10px;border-radius:4px;">LUXE30</strong> at checkout</p>
      <a href="products.html?cat=Electronics" class="btn btn-gold px-5 py-2">Grab the Deal</a>
    </div>
  </div>
</section>

<!-- ── FOOTER ─────────────────────────────────────────────── -->
<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:900;color:var(--accent);margin-bottom:1rem;">LUXE.</div>
        <p style="color:var(--muted);font-size:0.87rem;line-height:1.7;">Premium products for the discerning shopper. Quality, style, and convenience — all in one place.</p>
      </div>
      <div class="col-md-2 col-6">
        <h5>Shop</h5>
        <ul class="list-unstyled" style="line-height:2;">
          <li><a href="products.html">All Products</a></li>
          <li><a href="products.html?cat=Electronics">Electronics</a></li>
          <li><a href="products.html?cat=Fashion">Fashion</a></li>
        </ul>
      </div>
      <div class="col-md-2 col-6">
        <h5>Help</h5>
        <ul class="list-unstyled" style="line-height:2;">
          <li><a href="contact.html">Contact Us</a></li>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Returns</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h5>Newsletter</h5>
        <p style="color:var(--muted);font-size:0.85rem;margin-bottom:0.75rem;">Get exclusive deals right in your inbox.</p>
        <div class="input-group">
          <input type="email" class="form-control" placeholder="your@email.com" style="border-right:none!important;" />
          <button class="btn btn-gold" style="border-radius:0 8px 8px 0!important;">Subscribe</button>
        </div>
      </div>
    </div>
    <div class="footer-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
      <span>© 2025 LUXE Store. All rights reserved.</span>
      <div class="d-flex gap-3">
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-facebook"></i></a>
      </div>
    </div>
  </div>
</footer>

<div id="toast-container"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
<script>
// Render 4 featured products
const grid = document.getElementById('featured-grid');
PRODUCTS.slice(0, 4).forEach(p => {
  const stars = '★'.repeat(p.rating) + '☆'.repeat(5 - p.rating);
  grid.innerHTML += `
    <div class="col-sm-6 col-lg-3 fade-up">
      <div class="product-card">
        <div class="img-wrap">
          <img src="${p.image}" alt="${p.name}" />
          <span class="badge-tag">${p.badge}</span>
        </div>
        <div class="card-body">
          <div class="product-category">${p.category}</div>
          <div class="product-name">${p.name}</div>
          <div class="stars">${stars}</div>
          <div class="product-price">₹${p.price.toLocaleString()} <del>₹${p.oldPrice.toLocaleString()}</del></div>
          <button class="btn btn-gold w-100" onclick="addToCart('${p.id}','${p.name}',${p.price},'${p.image}','${p.category}')">
            <i class="fas fa-bag-shopping me-2"></i>Add to Cart
          </button>
        </div>
      </div>
    </div>`;
});

// Countdown timer
function startCountdown() {
  let h = 2, m = 14, s = 37;
  setInterval(() => {
    if (s > 0) s--;
    else if (m > 0) { m--; s = 59; }
    else if (h > 0) { h--; m = 59; s = 59; }
    const el = document.getElementById('countdown');
    if (el) el.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  }, 1000);
}
startCountdown();

// ── DYNAMIC CATEGORIES — fetched from admin panel DB via get_categories.php ──
function loadCategoriesFromDB() {
  const catGrid = document.getElementById('category-grid');

  fetch('get_categories.php')
    .then(res => res.json())
    .then(categories => {
      catGrid.innerHTML = '';

      if (!categories || categories.length === 0) {
        catGrid.innerHTML = '<div class="text-center text-muted py-4">No categories found</div>';
        return;
      }

      categories.forEach(cat => {
        const imageHtml = cat.image
          ? `<img src="${cat.image}" alt="${cat.name}" style="width:50px;height:50px;object-fit:cover;border-radius:8px;margin-bottom:1rem;" />`
          : `<div style="font-size:2.5rem;margin-bottom:1rem;">🛍️</div>`;

        catGrid.innerHTML += `
          <div class="col-6 col-md-3">
           <a href="product.php?cat_id=${cat.id}&cat_name=${encodeURIComponent(cat.name)}" class="text-decoration-none">
              <div class="category-card" style="background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:2rem 1rem;text-align:center;transition:all 0.3s;">
                ${imageHtml}
                <div style="font-weight:700;color:var(--text);">${cat.name}</div>
              </div>
            </a>
          </div>`;
      });

      attachCategoryHoverEffects();
    })
    .catch(err => {
      console.error('Category load error:', err);
      catGrid.innerHTML = '<div class="text-center text-danger py-4">Categories load nahi ho payi.</div>';
    });
}

function attachCategoryHoverEffects() {
  document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('mouseenter', () => { card.style.borderColor = 'var(--accent)'; card.style.transform = 'translateY(-4px)'; });
    card.addEventListener('mouseleave', () => { card.style.borderColor = 'var(--border)'; card.style.transform = ''; });
  });
}

loadCategoriesFromDB();
</script>

</body>
</html>