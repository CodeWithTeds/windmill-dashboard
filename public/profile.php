<?php
session_start();
require_once 'config/Database.php';
require_once 'models/User.php';

require('views/head.php');
require('views/sidebar.php');
require('views/header.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateData = [
        'username' => trim($_POST['username']),
        'email' => trim($_POST['email']),
        'phone_number' => trim($_POST['phone_number']),
        'address' => trim($_POST['address']),
        'city' => trim($_POST['city']),
        'zip_code' => trim($_POST['zip_code'])
    ];
    
    if ($user->updateUserProfile($_SESSION['user_id'], $updateData)) {
        $_SESSION['success'] = "Profile updated successfully!";
        // Get updated user data
        $userData = $user->getUserById($_SESSION['user_id']);
        // Use JavaScript to reload the page
        echo "<script>
            window.location.href = 'profile.php';
        </script>";
        exit();
    } else {
        $error = "Failed to update profile.";
    }
}

$userData = $user->getUserById($_SESSION['user_id']);
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="profile-name text-center"><?php echo htmlspecialchars($userData['username']); ?></h4>
                    <p class="profile-role text-center">User Profile</p>
                    <div class="profile-details">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?php echo htmlspecialchars($userData['email']); ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Phone</span>
                                <span class="info-value"><?php echo $userData['phone_number'] ? htmlspecialchars($userData['phone_number']) : 'Not set'; ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Address</span>
                                <span class="info-value"><?php echo $userData['address'] ? htmlspecialchars($userData['address']) : 'Not set'; ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-city"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">City</span>
                                <span class="info-value"><?php echo $userData['city'] ? htmlspecialchars($userData['city']) : 'Not set'; ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Zip Code</span>
                                <span class="info-value"><?php echo $userData['zip_code'] ? htmlspecialchars($userData['zip_code']) : 'Not set'; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    margin: 20px auto;
    overflow: hidden;
    min-width: 800px; /* Added minimum width */
    max-width: 1200px; 
}

.profile-header {
    background: #1a73e8;
    height: 80px;
    position: relative;
}

.profile-avatar {
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 60px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.profile-avatar i {
    font-size: 30px;
    color: #1a73e8;
}

.profile-name {
    margin-top: 35px;
    font-size: 20px;
    font-weight: 600;
    color: #333;
}

.profile-role {
    color: #666;
    font-size: 14px;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    align-items: center;
    padding: 12px 40px;
    border-bottom: 1px solid #eef2f7;
    transition: all 0.3s ease;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item:hover {
    background: #f8f9fa;
}

.info-icon {
    width: 35px;
    height: 35px;
    background: #e8f0fe;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.info-icon i {
    color: #1a73e8;
    font-size: 16px;
}

.info-content {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-right: 40px;
}

.info-label {
    font-size: 14px;
    color: #666;
    min-width: 100px;
}

.info-value {
    color: #333;
    font-size: 14px;
    font-weight: 500;
    margin-left: 20px;
}

.btn-edit {
    background: #1a73e8;
    color: white;
    border: none;
    padding: 8px 25px;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    background: #1557b0;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(26,115,232,0.4);
}

/* Modal Styles */
.modal-content {
    border-radius: 15px;
    border: none;
}

.modal-header {
    background: linear-gradient(45deg, #1a73e8, #289cf5);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px;
}

.modal-body {
    padding: 25px;
}

.form-control {
    border-radius: 8px;
    padding: 12px;
    border: 1px solid #e1e5eb;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26,115,232,0.2);
}

.form-group label {
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
}

.modal-footer {
    border-top: none;
    padding: 20px;
}

.btn-success {
    background: #1a73e8;
    border: none;
    padding: 10px 30px;
    border-radius: 25px;
}

.btn-success:hover {
    background: #1557b0;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(26,115,232,0.4);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal
    const myModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
    const successToast = new bootstrap.Toast(document.getElementById('successToast'));
    
    // Show modal if there was an error
    <?php if (isset($error)): ?>
        myModal.show();
    <?php endif; ?>

    // Show success toast if needed
    <?php if (isset($_SESSION['success'])): ?>
        successToast.show();
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    // Handle form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Submit form using fetch
        fetch('profile.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.text())
        .then(data => {
            // Reload the page to show updated data
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">
                    <i class="fas fa-user-edit"></i> Edit Profile Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form id="profileForm" method="POST" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label><i class="fas fa-user"></i> Username</label>
                                <input type="text" name="username" 
                                       value="<?php echo htmlspecialchars($userData['username']); ?>" 
                                       class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" 
                                       value="<?php echo htmlspecialchars($userData['email']); ?>" 
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="text" name="phone_number" 
                                       value="<?php echo htmlspecialchars($userData['phone_number'] ?? ''); ?>" 
                                       class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label><i class="fas fa-map-pin"></i> Zip Code</label>
                                <input type="text" name="zip_code" 
                                       value="<?php echo htmlspecialchars($userData['zip_code'] ?? ''); ?>" 
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label><i class="fas fa-map-marker-alt"></i> Address</label>
                        <textarea name="address" rows="3" class="form-control"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label><i class="fas fa-city"></i> City</label>
                        <input type="text" name="city" 
                               value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>" 
                               class="form-control">
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="submit" class="btn btn-success" id="saveChanges">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                Profile updated successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
