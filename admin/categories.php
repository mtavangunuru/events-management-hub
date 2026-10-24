<?php
$currentPage = 'categories';
$pageTitle = 'Category Management';
require_once 'includes/header.php';

$notice = '';
$error = '';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $icon = trim($_POST['icon'] ?? 'fa-layer-group');

    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        // Check duplicate slug
        $check = $pdo->prepare("SELECT id FROM categories WHERE slug = :slug LIMIT 1");
        $check->execute(['slug' => $slug]);
        if ($check->fetch()) {
            $error = "A category with a similar name already exists.";
        } else {
            $ins = $pdo->prepare("INSERT INTO categories (name, slug, icon, vendor_count) VALUES (:name, :slug, :icon, 0)");
            $ins->execute([
                'name' => $name,
                'slug' => $slug,
                'icon' => $icon
            ]);
            $notice = "New category '{$name}' created successfully!";
        }
    }
}

// Handle Delete Category
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delId = (int)$_GET['id'];
    
    // Check if vendors are assigned
    $vCheck = $pdo->prepare("SELECT COUNT(*) FROM vendor_profiles WHERE category_id = :id");
    $vCheck->execute(['id' => $delId]);
    $assignedCount = (int)$vCheck->fetchColumn();

    if ($assignedCount > 0) {
        $error = "Cannot delete this category because {$assignedCount} vendor(s) are currently assigned to it.";
    } else {
        $del = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $del->execute(['id' => $delId]);
        $notice = "Category deleted successfully.";
    }
}

// Fetch Categories with live vendor counts
$categories = $pdo->query("
    SELECT c.*, COUNT(vp.id) AS live_vendor_count 
    FROM categories c 
    LEFT JOIN vendor_profiles vp ON c.id = vp.category_id 
    GROUP BY c.id 
    ORDER BY c.name ASC
")->fetchAll();
?>

<div style="margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark);"><i class="fa-solid fa-layer-group" style="color: var(--primary); margin-right: 8px;"></i> Service Categories Management</h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Add, edit, or configure event service categories displayed on the homepage and search filters.</p>
</div>

<?php if (!empty($notice)): ?>
    <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($notice); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
    <!-- Categories List -->
    <div class="content-box">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 15px; color: var(--dark);">Existing Categories (<?php echo count($categories); ?>)</h2>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Category Name</th>
                    <th>Slug (URL)</th>
                    <th>Live Vendors</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td style="font-size: 1.3rem; color: var(--primary); text-align: center; width: 50px;">
                        <i class="fa-solid <?php echo htmlspecialchars($cat['icon']); ?>"></i>
                    </td>
                    <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                    <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                    <td>
                        <span class="badge-status badge-verified-status">
                            <?php echo (int)$cat['live_vendor_count']; ?> Vendors
                        </span>
                    </td>
                    <td>
                        <div class="admin-table-actions">
                            <a href="../vendors.php?category=<?php echo urlencode($cat['slug']); ?>" target="_blank" class="btn btn-outline btn-sm" title="View Public Catalog">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                            <a href="categories.php?action=delete&id=<?php echo $cat['id']; ?>" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626;" title="Delete Category" onclick="return confirm('Delete this category?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Category Form -->
    <div class="content-box">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 15px; color: var(--dark);"><i class="fa-solid fa-circle-plus" style="color: var(--accent);"></i> Add New Category</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
            Create a new category for vendors to register under (e.g., Honeymoon Resorts, Drone Cinematography, Ushers & Security).
        </p>

        <form action="categories.php" method="POST">
            <input type="hidden" name="add_category" value="1">

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 6px;">Category Name</label>
                <input type="text" name="name" placeholder="e.g. Traditional Dancers" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 6px;">FontAwesome Icon Class</label>
                <select name="icon" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    <option value="fa-building-columns">🏛️ fa-building-columns (Venues)</option>
                    <option value="fa-wand-magic-sparkles">✨ fa-wand-magic-sparkles (Decor)</option>
                    <option value="fa-camera">📷 fa-camera (Photography)</option>
                    <option value="fa-video">🎥 fa-video (Videography / 4K)</option>
                    <option value="fa-microphone-lines">🎤 fa-microphone-lines (MC)</option>
                    <option value="fa-cake-candles">🎂 fa-cake-candles (Catering & Cakes)</option>
                    <option value="fa-compact-disc">🎧 fa-compact-disc (DJs & Sound)</option>
                    <option value="fa-car-side">🚗 fa-car-side (Bridal Transport)</option>
                    <option value="fa-shirt">👗 fa-shirt (Bridal Gowns & Tuxedos)</option>
                    <option value="fa-spray-can-sparkles">💄 fa-spray-can-sparkles (Makeup & Hair)</option>
                    <option value="fa-shield-halved">🛡️ fa-shield-halved (Security & Bouncers)</option>
                    <option value="fa-people-group">👥 fa-people-group (Ushers & Protocol)</option>
                    <option value="fa-music">🎵 fa-music (Live Bands)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa-solid fa-plus"></i> Save Category
            </button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
