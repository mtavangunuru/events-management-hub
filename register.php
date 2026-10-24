<?php
require_once 'config/db.php';
$pageTitle = "Register - Nuustar Events";

// Check if user clicked "Join as a Vendor"
$isVendor = isset($_GET['type']) && $_GET['type'] === 'vendor';

$successMsg = '';
$errorMsg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountType = trim($_POST['account_type'] ?? 'customer');
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    // Vendor specific fields
    $businessName = trim($_POST['business_name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $region = trim($_POST['region'] ?? 'Dar es Salaam');
    $instagramLink = trim($_POST['instagram_link'] ?? '');

    if (empty($fullName) || empty($phone) || empty($email) || empty($password)) {
        $errorMsg = "Please fill in all required contact and password fields.";
    } elseif ($password !== $confirmPassword) {
        $errorMsg = "Passwords do not match. Please re-enter.";
    } else {
        // Check if email already exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $checkStmt->execute(['email' => $email]);
        if ($checkStmt->fetch()) {
            $errorMsg = "An account with this email already exists. Please login instead.";
        } else {
            // Hash password securely
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $role = ($accountType === 'vendor') ? 'vendor' : 'customer';

            // Insert into users with 'pending' status awaiting admin approval
            $userStmt = $pdo->prepare("
                INSERT INTO users (name, email, phone, password, role, status)
                VALUES (:name, :email, :phone, :password, :role, 'pending')
            ");
            $userStmt->execute([
                'name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'password' => $hashedPassword,
                'role' => $role
            ]);
            $newUserId = $pdo->lastInsertId();

            if ($role === 'vendor') {
                // Handle Cover Photo Upload
                $coverImagePath = '';
                if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['cover_photo']['tmp_name'];
                    $fileName = $_FILES['cover_photo']['name'];
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

                    if (in_array($fileExt, $allowedExts)) {
                        $uploadsDir = 'uploads/';
                        if (!is_dir($uploadsDir)) {
                            mkdir($uploadsDir, 0777, true);
                        }
                        $newFileName = 'vendor_' . time() . '_' . rand(100, 999) . '.' . $fileExt;
                        $destPath = $uploadsDir . $newFileName;
                        if (move_uploaded_file($fileTmp, $destPath)) {
                            $coverImagePath = $destPath;
                        }
                    }
                }

                // Fallback to category-smart default image if none uploaded
                if (empty($coverImagePath)) {
                    $defaults = [
                        1 => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=800&q=80', // Venue
                        2 => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80', // Decor
                        3 => 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?auto=format&fit=crop&w=800&q=80', // Photo
                        4 => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=800&q=80', // MC
                        5 => 'https://images.unsplash.com/photo-1535141192574-5d4897c13136?auto=format&fit=crop&w=800&q=80', // Catering
                        6 => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=800&q=80', // Music
                        7 => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=800&q=80'  // Transport
                    ];
                    $coverImagePath = $defaults[$categoryId] ?? 'uploads/banner.jpg';
                }

                // Insert into vendor_profiles with 'pending' verification and 10% commission default
                $vendorStmt = $pdo->prepare("
                    INSERT INTO vendor_profiles (user_id, business_name, category_id, region, district, bio, whatsapp_number, instagram_link, cover_image, starting_price, verification_status, commission_rate)
                    VALUES (:uid, :bname, :cid, :region, 'Center', :bio, :phone, :insta, :cover, 500000.00, 'pending', 10.00)
                ");
                $vendorStmt->execute([
                    'uid' => $newUserId,
                    'bname' => !empty($businessName) ? $businessName : $fullName . ' Services',
                    'cid' => $categoryId,
                    'region' => $region,
                    'bio' => 'Professional event services provider in ' . $region,
                    'phone' => $phone,
                    'insta' => $instagramLink,
                    'cover' => $coverImagePath
                ]);
                $successMsg = "Registration received successfully! Your vendor account and business profile have been submitted and are currently AWAITING APPROVAL from Nuustar Events Administrator. Once approved, your profile will be published and you will be able to manage your packages and receive bookings.";
                
                // Add initial notification
                $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Vendor Registration Queued', 'Your vendor registration is awaiting administrator review. Once verified, your store will be live!', 'account')")->execute(['uid' => $newUserId]);
            } else {
                $successMsg = "Registration received successfully! Your customer account has been created and is currently AWAITING APPROVAL from Nuustar Events Administrator. You will receive an approval notification shortly, after which you can log in, select packages, make payments, and post event requests.";

                // Add initial notification
                $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Customer Registration Queued', 'Your host account has been created and is awaiting approval by the administrator.', 'account')")->execute(['uid' => $newUserId]);
            }
        }
    }
}

// Fetch categories for vendor signup dropdown
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$signupCategories = $catStmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container" style="max-width: 650px; padding: 50px 20px 100px;">
    <div class="content-box" style="padding: 40px; box-shadow: var(--card-shadow-hover);">
        <div style="text-align: center; margin-bottom: 25px;">
            <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark);">Create Your Account</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Join Tanzania's leading network for event planning</p>
        </div>

        <?php if (!empty($successMsg)): ?>
            <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 16px; border-radius: var(--radius-sm); margin-bottom: 25px; font-weight: 600;">
                <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($successMsg); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 16px; border-radius: var(--radius-sm); margin-bottom: 25px; font-weight: 600;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <!-- Account Type Toggle -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: #eef2ff; padding: 6px; border-radius: var(--radius-sm); margin-bottom: 25px;">
            <button type="button" id="btnCustomerTab" class="btn <?php echo !$isVendor ? 'btn-primary' : ''; ?>" style="font-size: 0.9rem;" onclick="setAccountType('customer')">
                <i class="fa-solid fa-user"></i> I am an Event Host
            </button>
            <button type="button" id="btnVendorTab" class="btn <?php echo $isVendor ? 'btn-primary' : ''; ?>" style="font-size: 0.9rem;" onclick="setAccountType('vendor')">
                <i class="fa-solid fa-store"></i> I am a Service Vendor
            </button>
        </div>

        <form action="register.php" method="POST" enctype="multipart/form-data">
            <!-- Hidden Account Type -->
            <input type="hidden" name="account_type" id="accountTypeInput" value="<?php echo $isVendor ? 'vendor' : 'customer'; ?>">

            <!-- Common Contact Person -->
            <div class="form-group" style="margin-bottom: 18px;">
                <label>Contact Full Name</label>
                <input type="text" name="full_name" placeholder="e.g. Amani Mrosso" required>
            </div>

            <!-- Vendor Verification Fields (Shown only when Vendor tab is active) -->
            <div id="vendorFields" style="<?php echo !$isVendor ? 'display: none;' : ''; ?>">
                <div style="background-color: #f8fafc; border: 1px dashed var(--border); border-radius: var(--radius-sm); padding: 18px; margin-bottom: 20px;">
                    <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--primary); margin-bottom: 15px;">
                        <i class="fa-solid fa-shield-halved"></i> Vendor Verification Details (KYC)
                    </h4>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Business / Brand Name</label>
                        <input type="text" name="business_name" placeholder="e.g. Sparkle Moments Decor & Styling">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Primary Service Category</label>
                        <select name="category_id">
                            <?php foreach ($signupCategories as $sc): ?>
                            <option value="<?php echo $sc['id']; ?>"><?php echo htmlspecialchars($sc['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Operating Region & City</label>
                        <select name="region">
                            <option value="Dar es Salaam - Kinondoni">Dar es Salaam - Kinondoni</option>
                            <option value="Dar es Salaam - Ilala">Dar es Salaam - Ilala</option>
                            <option value="Dar es Salaam - Masaki / Oysterbay">Dar es Salaam - Masaki / Oysterbay</option>
                            <option value="Arusha City">Arusha City</option>
                            <option value="Dodoma City">Dodoma City</option>
                            <option value="Mwanza City">Mwanza City</option>
                            <option value="Zanzibar Island">Zanzibar Island</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Instagram Page / Portfolio Link</label>
                        <input type="text" name="instagram_link" placeholder="e.g. instagram.com/your_brand_tz">
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                            Our admin team reviews your past work to grant the Verified Badge.
                        </small>
                    </div>

                    <div class="form-group" style="margin-bottom: 5px;">
                        <label><i class="fa-solid fa-camera"></i> Business Cover Photo / Picture</label>
                        <input type="file" name="cover_photo" accept="image/*" style="padding: 8px;">
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                            Upload a picture of your venue hall, catering buffet, decor work, or your photo as MC (JPG/PNG).
                        </small>
                    </div>
                </div>
            </div>

            <!-- Contact & Password Fields -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 18px;">
                <div class="form-group">
                    <label>WhatsApp / Phone Number</label>
                    <input type="tel" name="phone" placeholder="+255 7XX XXX XXX" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="name@example.com" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 13px; font-size: 1rem;">
                <i class="fa-solid fa-user-plus"></i> Complete Registration
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border); font-size: 0.9rem;">
            <span>Already have an account? </span>
            <a href="login.php" style="color: var(--primary); font-weight: 700;">Sign In</a>
        </div>
    </div>
</div>

<script>
function setAccountType(type) {
    const vendorFields = document.getElementById('vendorFields');
    const input = document.getElementById('accountTypeInput');
    const btnCustomer = document.getElementById('btnCustomerTab');
    const btnVendor = document.getElementById('btnVendorTab');

    input.value = type;

    if (type === 'vendor') {
        vendorFields.style.display = 'block';
        btnVendor.classList.add('btn-primary');
        btnCustomer.classList.remove('btn-primary');
    } else {
        vendorFields.style.display = 'none';
        btnCustomer.classList.add('btn-primary');
        btnVendor.classList.remove('btn-primary');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
