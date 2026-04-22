<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

// Get current admin data
$select_admin = mysqli_query($conn, "SELECT * FROM `admin` WHERE id = '$admin_id'") or die('query failed');
$admin_data = mysqli_fetch_assoc($select_admin);

// Handle profile update
if(isset($_POST['update_profile'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   
   // Check if email is already taken by someone else
   $check_email = mysqli_query($conn, "SELECT * FROM `admin` WHERE email = '$email' AND id != '$admin_id'") or die('query failed');
   
   if(mysqli_num_rows($check_email) > 0){
      $message[] = 'email already taken!';
   } else {
      mysqli_query($conn, "UPDATE `admin` SET name = '$name', email = '$email' WHERE id = '$admin_id'") or die('query failed');
      $_SESSION['admin_name'] = $name;
      $_SESSION['admin_email'] = $email;
      $message[] = 'profile updated successfully!';
   }
}

// Handle password change
if(isset($_POST['update_password'])){
   $old_password = md5($_POST['old_password']);
   $new_password = md5($_POST['new_password']);
   $confirm_password = md5($_POST['confirm_password']);
   
   // Verify old password
   $verify_pass = mysqli_query($conn, "SELECT * FROM `admin` WHERE id = '$admin_id' AND password = '$old_password'") or die('query failed');
   
   if(mysqli_num_rows($verify_pass) > 0){
      if($new_password === $confirm_password){
         mysqli_query($conn, "UPDATE `admin` SET password = '$new_password' WHERE id = '$admin_id'") or die('query failed');
         $message[] = 'password updated successfully!';
      } else {
         $message[] = 'new passwords do not match!';
      }
   } else {
      $message[] = 'old password is incorrect!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      .admin-profile {
         padding: 2rem 0;
         background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
         min-height: 80vh;
      }

      .admin-profile .title {
         text-align: center;
         margin-bottom: 3rem;
         font-size: 2.5rem;
         color: #2c3e50;
         font-weight: bold;
      }

      .profile-container {
         max-width: 600px;
         margin: 0 auto;
         display: grid;
         gap: 2rem;
         padding: 0 1rem;
      }

      .profile-section {
         background: white;
         padding: 2.5rem;
         border-radius: 12px;
         box-shadow: 0 5px 20px rgba(0,0,0,0.15);
         transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .profile-section:hover {
         transform: translateY(-5px);
         box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      }

      .profile-section h3 {
         font-size: 1.6rem;
         margin-bottom: 1.5rem;
         color: #2c3e50;
         border-bottom: 3px solid #FF6B6B;
         padding-bottom: 0.8rem;
         display: flex;
         align-items: center;
         gap: 10px;
      }

      .profile-section h3 i {
         color: #FF6B6B;
         font-size: 1.8rem;
      }

      .form-group {
         margin-bottom: 1.5rem;
      }

      .form-group label {
         display: block;
         font-weight: 600;
         margin-bottom: 0.7rem;
         color: #555;
         font-size: 0.95rem;
      }

      .form-group input {
         width: 100%;
         padding: 0.9rem 1rem;
         border: 2px solid #e0e0e0;
         border-radius: 8px;
         font-size: 1rem;
         box-sizing: border-box;
         transition: all 0.3s ease;
         font-family: inherit;
      }

      .form-group input:focus {
         outline: none;
         border-color: #FF6B6B;
         box-shadow: 0 0 10px rgba(255, 107, 107, 0.25);
         background: #fff9f9;
      }

      .btn {
         width: 100%;
         padding: 1rem;
         background: linear-gradient(135deg, #FF6B6B 0%, #ff5252 100%);
         color: white;
         border: none;
         border-radius: 8px;
         font-size: 1.05rem;
         font-weight: bold;
         cursor: pointer;
         transition: all 0.3s ease;
         display: flex;
         align-items: center;
         justify-content: center;
         gap: 8px;
      }

      .btn:hover {
         background: linear-gradient(135deg, #ff5252 0%, #ff3030 100%);
         transform: translateY(-2px);
         box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
      }

      .btn:active {
         transform: translateY(0);
      }

      .btn i {
         font-size: 1.1rem;
      }

      @media (max-width: 640px) {
         .profile-container {
            max-width: 100%;
            gap: 1.5rem;
         }
         
         .profile-section {
            padding: 1.5rem;
         }
         
         .admin-profile .title {
            font-size: 2rem;
            margin-bottom: 2rem;
         }

         .profile-section h3 {
            font-size: 1.3rem;
         }

         .form-group input {
            padding: 0.8rem 0.9rem;
            font-size: 16px;
         }
      }
   </style>

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="admin-profile">

   <h1 class="title">admin profile</h1>

   <div class="profile-container">

      <!-- Update Profile Form -->
      <form method="post" class="profile-section">
         <h3><i class="fas fa-user"></i> Update Profile</h3>
         <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo $admin_data['name']; ?>" class="box" required>
         </div>
         <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo $admin_data['email']; ?>" class="box" required>
         </div>
         <button type="submit" name="update_profile" class="btn">
            <i class="fas fa-save"></i> Update Profile
         </button>
      </form>

      <!-- Change Password Form -->
      <form method="post" class="profile-section">
         <h3><i class="fas fa-lock"></i> Change Password</h3>
         <div class="form-group">
            <label for="old_password">Old Password</label>
            <input type="password" id="old_password" name="old_password" class="box" required>
         </div>
         <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" class="box" required>
         </div>
         <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="box" required>
         </div>
         <button type="submit" name="update_password" class="btn">
            <i class="fas fa-key"></i> Update Password
         </button>
      </form>

   </div>

</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>
