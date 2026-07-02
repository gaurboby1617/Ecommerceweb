<?php
// product.php
// Yahan rakho: EcomWebProject/product.php (indexx.php ke sath, same folder)
// Jab homepage par koi category click hoti hai (e.g. product.php?cat_id=3&cat_name=Beauty),
// ye page us category ki sub-categories dikhata hai (admin panel se add ki gayi).

$catId   = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
$catName = isset($_GET['cat_name']) ? htmlspecialchars($_GET['cat_name']) : 'Category';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $catName; ?> — LUXE Store</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<!-- ── NAVBAR ───────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="indexx.php">LUXE<span>.</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a class="nav-link" href="indexx.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
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

<!-- ── BREADCRUMB / TITLE ───────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <p class="section-title">Shop <span><?php echo $catName; ?></span></p>
    <div class="section-divider"></div>
    <p style="color:var(--muted);">
      <a href="indexx.php" style="color:var(--muted);text-decoration:none;">Home</a>
      <i class="fas fa-chevron-right mx-2" style="font-size:0.7rem;"></i>
      <?php echo $catName; ?>
    </p>
  </div>
</section>

<!-- ── SUB-CATEGORY GRID ────────────────────────────────── -->
<section class="pb-5">
  <div class="container">
    <div class="row g-3" id="subcategory-grid">
      <div class="text-center text-muted py-4" id="subcat-loading">Loading sub-categories...</div>
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
          <li><a href="products.php">All Products</a></li>
        </ul>
      </div>
      <div class="col-md-2 col-6">
        <h5>Help</h5>
        <ul class="list-unstyled" style="line-height:2;">
          <li><a href="contact.php">Contact Us</a></li>
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
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
<script>
const CATEGORY_ID = <?php echo $catId; ?>;

function loadSubCategories() {
  const grid = document.getElementById('subcategory-grid');

  if (!CATEGORY_ID) {
    grid.innerHTML = '<div class="text-center text-danger py-4">Category id missing hai.</div>';
    return;
  }

  fetch('get_subcategories.php?category_id=' + CATEGORY_ID)
    .then(res => res.json())
    .then(subcategories => {
      grid.innerHTML = '';

      if (!subcategories || subcategories.length === 0) {
        grid.innerHTML = '<div class="text-center text-muted py-4">Is category me abhi koi sub-category nahi hai.</div>';
        return;
      }

      subcategories.forEach(sub => {
        const imageHtml = sub.image
          ? `<img src="${sub.image}" alt="${sub.name}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;margin-bottom:1rem;" />`
          : `<div style="font-size:2.5rem;margin-bottom:1rem;">🛍️</div>`;

        grid.innerHTML += `
          <div class="col-6 col-md-3">
            <a href="products.php?subcat_id=${sub.id}&subcat_name=${encodeURIComponent(sub.name)}" class="text-decoration-none">
              <div class="category-card" style="background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:2rem 1rem;text-align:center;transition:all 0.3s;">
                ${imageHtml}
                <div style="font-weight:700;color:var(--text);">${sub.name}</div>
              </div>
            </a>
          </div>`;
      });

      attachHoverEffects();
    })
    .catch(err => {
      console.error('Sub-category load error:', err);
      grid.innerHTML = '<div class="text-center text-danger py-4">Sub-categories load nahi ho payi.</div>';
    });
}

function attachHoverEffects() {
  document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('mouseenter', () => { card.style.borderColor = 'var(--accent)'; card.style.transform = 'translateY(-4px)'; });
    card.addEventListener('mouseleave', () => { card.style.borderColor = 'var(--border)'; card.style.transform = ''; });
  });
}

loadSubCategories();
</script>

</body>
</html>