<?php
$currentPage = 'users';
$pageTitle = 'Users & Roles Management';
require_once 'includes/header.php';

$notice = '';
$error = '';

// Handle Status & Role Changes
if (isset($_GET['action']) && isset($_GET['id'])) {
    $targetId = (int)$_GET['id'];
    $act = $_GET['action'];

    // Prevent admin from modifying or deleting themselves
    if ($targetId === (int)$_SESSION['user_id'] && in_array($act, ['suspend', 'delete', 'reject'])) {
        $error = "You cannot modify, suspend, or delete your own active administrator account.";
    } else {
        if ($act === 'approve') {
            // Approve user
            $up = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = :id");
            $up->execute(['id' => $targetId]);

            // Also if vendor, verify their vendor profile
            $upV = $pdo->prepare("UPDATE vendor_profiles SET verification_status = 'verified' WHERE user_id = :uid");
            $upV->execute(['uid' => $targetId]);

            // Add notification
            $notif = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Account Approved!', 'Your account has been approved by the Nuustar Events Administrator. Welcome aboard!', 'account')");
            $notif->execute(['uid' => $targetId]);

            $notice = "User ID #{$targetId} has been successfully APPROVED and activated!";
        } elseif ($act === 'reject') {
            $up = $pdo->prepare("UPDATE users SET status = 'rejected' WHERE id = :id");
            $up->execute(['id' => $targetId]);
            $notice = "User ID #{$targetId} status set to rejected.";
        } elseif ($act === 'suspend') {
            $up = $pdo->prepare("UPDATE users SET status = 'suspended' WHERE id = :id");
            $up->execute(['id' => $targetId]);
            $notice = "User ID #{$targetId} has been suspended.";
        } elseif ($act === 'activate') {
            $up = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = :id");
            $up->execute(['id' => $targetId]);
            $notice = "User ID #{$targetId} is now active.";
        } elseif ($act === 'make_admin') {
            $up = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = :id");
            $up->execute(['id' => $targetId]);
            $notice = "User ID #{$targetId} has been promoted to Admin.";
        } elseif ($act === 'delete') {
            $del = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $del->execute(['id' => $targetId]);
            $notice = "User ID #{$targetId} has been deleted permanently.";
        }
    }
}

// Handle Edit User Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $editId = (int)$_POST['user_id'];
    $editName = trim($_POST['name'] ?? '');
    $editEmail = trim($_POST['email'] ?? '');
    $editPhone = trim($_POST['phone'] ?? '');
    $editRole = $_POST['role'] ?? 'customer';
    $editStatus = $_POST['status'] ?? 'active';
    $editPassword = trim($_POST['password'] ?? '');

    if (empty($editName) || empty($editEmail) || empty($editPhone)) {
        $error = "Name, email, and phone are required fields.";
    } else {
        // Check email uniqueness excluding current user
        $check = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
        $check->execute(['email' => $editEmail, 'id' => $editId]);
        if ($check->fetch()) {
            $error = "Another account is already using this email address.";
        } else {
            if (!empty($editPassword)) {
                $hash = password_hash($editPassword, PASSWORD_BCRYPT);
                $upStmt = $pdo->prepare("
                    UPDATE users 
                    SET name = :name, email = :email, phone = :phone, role = :role, status = :status, password = :pwd 
                    WHERE id = :id
                ");
                $upStmt->execute([
                    'name' => $editName,
                    'email' => $editEmail,
                    'phone' => $editPhone,
                    'role' => $editRole,
                    'status' => $editStatus,
                    'pwd' => $hash,
                    'id' => $editId
                ]);
            } else {
                $upStmt = $pdo->prepare("
                    UPDATE users 
                    SET name = :name, email = :email, phone = :phone, role = :role, status = :status 
                    WHERE id = :id
                ");
                $upStmt->execute([
                    'name' => $editName,
                    'email' => $editEmail,
                    'phone' => $editPhone,
                    'role' => $editRole,
                    'status' => $editStatus,
                    'id' => $editId
                ]);
            }

            // If status approved & vendor, also update vendor profile verification
            if ($editStatus === 'active' && $editRole === 'vendor') {
                $pdo->prepare("UPDATE vendor_profiles SET verification_status = 'verified' WHERE user_id = :uid")->execute(['uid' => $editId]);
            }

            $notice = "User account #{$editId} ({$editName}) updated successfully!";
        }
    }
}

// Handle Add New User Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'customer';
    $status = $_POST['status'] ?? 'active';

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required to create a new account.";
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            $error = "A user with this email address already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, status) VALUES (:name, :email, :phone, :pwd, :role, :status)");
            $ins->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'pwd' => $hash,
                'role' => $role,
                'status' => $status
            ]);
            $newUid = $pdo->lastInsertId();

            if ($role === 'vendor') {
                // Create basic vendor profile
                $pdo->prepare("INSERT INTO vendor_profiles (user_id, business_name, category_id, region, district, whatsapp_number, verification_status, commission_rate) VALUES (:uid, :bname, 1, 'Dar es Salaam', 'Kinondoni', :phone, :vstat, 10.00)")->execute([
                    'uid' => $newUid,
                    'bname' => $name . ' Services',
                    'phone' => $phone,
                    'vstat' => ($status === 'active' ? 'verified' : 'pending')
                ]);
            }

            $notice = "New account for {$name} ({$role}) created successfully!";
        }
    }
}

// Filter and Search logic
$filterRole = $_GET['role'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$searchKeyword = trim($_GET['search'] ?? '');

$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

if (!empty($filterRole)) {
    $sql .= " AND role = :role";
    $params['role'] = $filterRole;
}

if (!empty($filterStatus)) {
    $sql .= " AND status = :status";
    $params['status'] = $filterStatus;
}

if (!empty($searchKeyword)) {
    $sql .= " AND (name LIKE :kw1 OR email LIKE :kw2 OR phone LIKE :kw3)";
    $params['kw1'] = '%' . $searchKeyword . '%';
    $params['kw2'] = '%' . $searchKeyword . '%';
    $params['kw3'] = '%' . $searchKeyword . '%';
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Counts for filter tabs
$countAll = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$countPending = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
$countAdmins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$countVendors = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'vendor'")->fetchColumn();
$countCustomers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
?>

<div style="margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark);"><i class="fa-solid fa-users" style="color: var(--primary); margin-right: 8px;"></i> User Accounts & Approvals</h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Approve customer and vendor registrations, manage account statuses, edit profiles, and assign permissions.</p>
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

<!-- Role & Status Tabs -->
<div class="filter-tabs">
    <a href="users.php" class="filter-tab-btn <?php echo (empty($filterRole) && empty($filterStatus)) ? 'active' : ''; ?>">
        All Users (<?php echo $countAll; ?>)
    </a>
    <a href="users.php?status=pending" class="filter-tab-btn <?php echo $filterStatus === 'pending' ? 'active' : ''; ?>" style="<?php echo $countPending > 0 ? 'background: #fffbeb; border-color: #f59e0b; color: #b45309; font-weight: 700;' : ''; ?>">
        <i class="fa-solid fa-clock"></i> Pending Approval (<?php echo $countPending; ?>)
    </a>
    <a href="users.php?role=customer" class="filter-tab-btn <?php echo $filterRole === 'customer' ? 'active' : ''; ?>">
        <i class="fa-solid fa-user"></i> Customers (<?php echo $countCustomers; ?>)
    </a>
    <a href="users.php?role=vendor" class="filter-tab-btn <?php echo $filterRole === 'vendor' ? 'active' : ''; ?>">
        <i class="fa-solid fa-store"></i> Vendors (<?php echo $countVendors; ?>)
    </a>
    <a href="users.php?role=admin" class="filter-tab-btn <?php echo $filterRole === 'admin' ? 'active' : ''; ?>">
        <i class="fa-solid fa-shield-halved"></i> Admins (<?php echo $countAdmins; ?>)
    </a>
</div>

<!-- Search Bar -->
<div class="content-box" style="padding: 16px 20px; margin-bottom: 20px;">
    <form action="users.php" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <?php if (!empty($filterRole)): ?><input type="hidden" name="role" value="<?php echo htmlspecialchars($filterRole); ?>"><?php endif; ?>
        <?php if (!empty($filterStatus)): ?><input type="hidden" name="status" value="<?php echo htmlspecialchars($filterStatus); ?>"><?php endif; ?>
        <div style="flex: 1; min-width: 250px;">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchKeyword); ?>" placeholder="Search by name, email, or phone number..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
        </div>
        <button type="submit" class="btn btn-primary btn-sm" style="padding: 9px 18px;">
            <i class="fa-solid fa-magnifying-glass"></i> Search
        </button>
        <a href="users.php" class="btn btn-outline btn-sm" style="padding: 9px 15px;">Reset</a>
    </form>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
    <!-- Users List Table -->
    <div class="content-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--dark); margin: 0;">Registered Accounts (<?php echo count($users); ?>)</h2>
            <?php if ($countPending > 0 && empty($filterStatus)): ?>
                <a href="users.php?status=pending" style="font-size: 0.85rem; color: #b45309; background: #fef3c7; padding: 4px 10px; border-radius: 20px; font-weight: 700; text-decoration: none;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $countPending; ?> Pending Approval
                </a>
            <?php endif; ?>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User & Contacts</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">
                            No user accounts match the current filter.
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($users as $u): ?>
                    <tr style="<?php echo $u['status'] === 'pending' ? 'background: #fffbeb;' : ''; ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($u['name']); ?></strong><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($u['email']); ?></small><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($u['phone']); ?></small>
                        </td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="logo-badge" style="background: #dc2626;">Admin</span>
                            <?php elseif ($u['role'] === 'vendor'): ?>
                                <span class="logo-badge" style="background: var(--primary);">Vendor</span>
                            <?php else: ?>
                                <span class="logo-badge" style="background: #4b5563;">Customer</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="badge-status badge-verified-status"><i class="fa-solid fa-circle-check"></i> Active</span>
                            <?php elseif ($u['status'] === 'pending'): ?>
                                <span class="badge-status badge-pending-status" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;"><i class="fa-solid fa-clock"></i> Pending Approval</span>
                            <?php elseif ($u['status'] === 'suspended'): ?>
                                <span class="badge-status badge-rejected-status"><i class="fa-solid fa-ban"></i> Suspended</span>
                            <?php else: ?>
                                <span class="badge-status badge-rejected-status"><?php echo htmlspecialchars($u['status']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">
                            <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <!-- Approve Pending User -->
                                <?php if ($u['status'] === 'pending'): ?>
                                    <a href="users.php?action=approve&id=<?php echo $u['id']; ?>" class="btn btn-primary btn-sm" style="background: #059669; border-color: #059669; padding: 5px 10px; font-weight: 700;" title="Approve Account" onclick="return confirm('Approve and activate this user account?')">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </a>
                                    <a href="users.php?action=reject&id=<?php echo $u['id']; ?>" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626; padding: 5px 9px;" title="Reject Registration" onclick="return confirm('Reject this registration?')">
                                        <i class="fa-solid fa-xmark"></i> Reject
                                    </a>
                                <?php endif; ?>

                                <!-- Edit User Trigger Modal -->
                                <button type="button" class="btn btn-outline btn-sm" style="padding: 5px 9px; color: var(--primary); border-color: var(--primary);" title="Edit User" onclick='openEditModal(<?php echo json_encode($u); ?>)'>
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                                    <!-- Suspend / Activate toggle -->
                                    <?php if ($u['status'] === 'active'): ?>
                                        <a href="users.php?action=suspend&id=<?php echo $u['id']; ?>" class="btn btn-outline btn-sm" style="color: #d97706; border-color: #d97706; padding: 5px 9px;" title="Suspend Account" onclick="return confirm('Suspend this user account?')">
                                            <i class="fa-solid fa-pause"></i>
                                        </a>
                                    <?php elseif ($u['status'] === 'suspended'): ?>
                                        <a href="users.php?action=activate&id=<?php echo $u['id']; ?>" class="btn btn-outline btn-sm" style="color: #059669; border-color: #059669; padding: 5px 9px;" title="Activate Account" onclick="return confirm('Re-activate this user?')">
                                            <i class="fa-solid fa-play"></i>
                                        </a>
                                    <?php endif; ?>

                                    <!-- Delete Account -->
                                    <a href="users.php?action=delete&id=<?php echo $u['id']; ?>" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626; padding: 5px 9px;" title="Delete User" onclick="return confirm('Permanently delete this account and all associated data?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: #059669; font-weight: 700;"><i class="fa-solid fa-user-shield"></i> You</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create User / Admin Form -->
    <div class="content-box">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 12px; color: var(--dark);"><i class="fa-solid fa-user-plus" style="color: var(--accent);"></i> Add New User</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
            Create a customer, vendor, or administrator directly with immediate or pending status.
        </p>

        <form action="users.php" method="POST">
            <input type="hidden" name="create_user" value="1">

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Full Name</label>
                <input type="text" name="name" placeholder="e.g. John Doe" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Email Address</label>
                <input type="email" name="email" placeholder="e.g. customer@example.com" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Phone Number</label>
                <input type="tel" name="phone" placeholder="+255 7XX XXX XXX" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" placeholder="Min. 6 characters" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Account Role</label>
                <select name="role" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    <option value="customer">Event Host / Customer</option>
                    <option value="vendor">Service Vendor</option>
                    <option value="admin">Administrator (Super Admin)</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Initial Status</label>
                <select name="status" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    <option value="active">Active (Immediate Access)</option>
                    <option value="pending">Pending Approval</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa-solid fa-plus"></i> Create Account
            </button>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 520px; border-radius: var(--radius-sm); padding: 30px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); position: relative; max-height: 90vh; overflow-y: auto;">
        <button type="button" onclick="closeEditModal()" style="position: absolute; top: 18px; right: 20px; background: none; border: none; font-size: 1.3rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        
        <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--dark); margin-bottom: 5px;">
            <i class="fa-solid fa-user-pen" style="color: var(--primary);"></i> Edit User Account
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">Update user details, role permissions, or account status.</p>

        <form action="users.php" method="POST">
            <input type="hidden" name="edit_user" value="1">
            <input type="hidden" name="user_id" id="editUserId">

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Full Name</label>
                <input type="text" name="name" id="editUserName" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Email Address</label>
                <input type="email" name="email" id="editUserEmail" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Phone Number</label>
                <input type="tel" name="phone" id="editUserPhone" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div class="form-group">
                    <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Account Role</label>
                    <select name="role" id="editUserRole" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        <option value="customer">Customer / Event Host</option>
                        <option value="vendor">Service Vendor</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Account Status</label>
                    <select name="status" id="editUserStatus" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        <option value="active">Active (Approved)</option>
                        <option value="pending">Pending Approval</option>
                        <option value="suspended">Suspended</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 22px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">New Password (Leave blank to keep unchanged)</label>
                <input type="password" name="password" placeholder="Enter new password to change" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()" class="btn btn-outline btn-sm" style="padding: 10px 18px;">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 22px;">
                    <i class="fa-solid fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserName').value = user.name;
    document.getElementById('editUserEmail').value = user.email;
    document.getElementById('editUserPhone').value = user.phone;
    document.getElementById('editUserRole').value = user.role;
    document.getElementById('editUserStatus').value = user.status;

    const modal = document.getElementById('editUserModal');
    modal.style.display = 'flex';
}

function closeEditModal() {
    const modal = document.getElementById('editUserModal');
    modal.style.display = 'none';
}

// Close when clicking outside modal box
window.addEventListener('click', function(e) {
    const modal = document.getElementById('editUserModal');
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
