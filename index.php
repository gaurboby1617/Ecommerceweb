<?php include 'auth_check.php'; ?>
<?php
  include 'include/header.php';
  include 'include/conn.php';
?>

<style>
    .welcome-banner {
        background: linear-gradient(135deg, #6a3de8 0%, #9b59f6 100%);
        border-radius: 14px;
        padding: 28px 32px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .welcome-banner::after {
        content: "";
        position: absolute;
        right: -30px; top: -30px;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .welcome-banner h2 { font-weight: 600; }
    .welcome-banner p { opacity: 0.9; margin-bottom: 0; }

    .stat-card {
        border: none;
        border-radius: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
    .icon-box {
        width: 52px; height: 52px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; color: #fff;
        flex-shrink: 0;
    }
    .stat-number { font-size: 1.6rem; font-weight: 700; margin-bottom: 0; }
    .stat-label { color: #8a8fa3; font-size: 0.85rem; }

    .quick-link-card {
        border: none;
        border-radius: 14px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .quick-link-card:hover {
        background-color: #6a3de8 !important;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(106,61,232,0.25);
    }
    .quick-link-card:hover .ql-icon,
    .quick-link-card:hover .ql-text {
        color: #fff !important;
    }
    .ql-icon { font-size: 28px; }
    .ql-text { font-weight: 500; color: #333; margin-top: 8px; }

    .section-title {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 16px;
        color: #2d2d3a;
    }

    .table-recent th { font-size: 0.8rem; color: #8a8fa3; border-bottom-width: 1px; }
    .table-recent td { vertical-align: middle; }
    .badge-stock { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; }
</style>

<div class="container-fluid py-4 px-4">

    <!-- Welcome Banner -->
    <div class="welcome-banner mb-4 d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h2 class="mb-1">Welcome, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?> 👋</h2>
            <p>Here's what's happening in your store today.</p>
        </div>
        <a href="logout.php" class="btn btn-light text-dark fw-medium px-4 mt-2">Logout</a>
    </div>

    <?php
        function getDashboardCount($conn, $table) {
            $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM $table");
            if ($result) {
                return mysqli_fetch_assoc($result)['cnt'];
            }
            return 0;
        }

        $totalProducts      = getDashboardCount($conn, 'products');
        $totalBrands        = getDashboardCount($conn, 'brand');
        $totalCategories    = getDashboardCount($conn, 'categories');
        $totalSubCategories = getDashboardCount($conn, 'sub_categories');
    ?>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box" style="background:#4361ee;">📦</div>
                    <div>
                        <p class="stat-number"><?= $totalProducts ?></p>
                        <span class="stat-label">Total Products</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box" style="background:#2ec4b6;">🏷️</div>
                    <div>
                        <p class="stat-number"><?= $totalBrands ?></p>
                        <span class="stat-label">Total Brands</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box" style="background:#ff9f1c;">📂</div>
                    <div>
                        <p class="stat-number"><?= $totalCategories ?></p>
                        <span class="stat-label">Total Categories</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box" style="background:#e63946;">📁</div>
                    <div>
                        <p class="stat-number"><?= $totalSubCategories ?></p>
                        <span class="stat-label">Total Sub Categories</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quick Links -->
        <div class="col-lg-5">
            <p class="section-title">Quick Actions</p>
            <div class="row g-3">
                <div class="col-6">
                    <a href="brand.php" class="quick-link-card card shadow-sm text-center p-3 d-block">
                        <div class="ql-icon">🏷️</div>
                        <div class="ql-text">Manage Brands</div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="category.php" class="quick-link-card card shadow-sm text-center p-3 d-block">
                        <div class="ql-icon">📂</div>
                        <div class="ql-text">Manage Categories</div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="sub-category.php" class="quick-link-card card shadow-sm text-center p-3 d-block">
                        <div class="ql-icon">📁</div>
                        <div class="ql-text">Manage Sub Categories</div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="product.php" class="quick-link-card card shadow-sm text-center p-3 d-block">
                        <div class="ql-icon">📦</div>
                        <div class="ql-text">Manage Products</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recently Added Products -->
        <div class="col-lg-7">
            <p class="section-title">Recently Added Products</p>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-recent mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Product</th>
                                <th>Price</th>
                                <th class="pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $recent = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT 5");
                                if ($recent && mysqli_num_rows($recent) > 0) {
                                    while ($row = mysqli_fetch_assoc($recent)) {
                            ?>
                                <tr>
                                    <td class="ps-3"><?= htmlspecialchars($row['name'] ?? $row['product_name'] ?? 'N/A') ?></td>
                                    <td>₹<?= htmlspecialchars($row['price'] ?? '0') ?></td>
                                    <td class="pe-3">
                                        <?php if (($row['status'] ?? 1) == 1): ?>
                                            <span class="badge bg-success-subtle text-success badge-stock">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger badge-stock">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php
                                    }
                                } else {
                            ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">No products added yet.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
  include 'include/footer.php';
?>