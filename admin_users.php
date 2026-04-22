<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $user_type = isset($_GET['type']) ? $_GET['type'] : 'user';
   
   if($user_type == 'admin'){
      // Delete admin and their related products
      mysqli_query($conn, "DELETE FROM `admin` WHERE id = '$delete_id'") or die('query failed');
   }else{
      // When user is deleted, delete their orders and cart items too
      mysqli_query($conn, "DELETE FROM `orders` WHERE user_id = '$delete_id'") or die('query failed');
      mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$delete_id'") or die('query failed');
      mysqli_query($conn, "DELETE FROM `message` WHERE user_id = '$delete_id'") or die('query failed');
      mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
   }
   header('location:admin_users.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">
   
   <!-- separate table css file link  -->
   <link rel="stylesheet" href="css/separate_table.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">

   <!-- ADMIN USERS TABLE -->
   <div class="section-header admin">
      <h2>Admin Users</h2>
   </div>

   <div class="table-container">
      <table class="data-table admin-table">
         <thead>
            <tr>
               <th>User ID</th>
               <th>Username</th>
               <th>Email</th>
               <th>User Type</th>
               <th>Actions</th>
            </tr>
         </thead>
         <tbody>
            <?php
               $select_admin = mysqli_query($conn, "SELECT * FROM `admin`") or die('query failed');
               if(mysqli_num_rows($select_admin) > 0){
                  $admin_count = 1;
                  while($fetch_admin = mysqli_fetch_assoc($select_admin)){
            ?>
            <tr>
               <td><?php echo $admin_count; ?></td>
               <td><?php echo $fetch_admin['name']; ?></td>
               <td><?php echo $fetch_admin['email']; ?></td>
               <td>
                  <span class="user-type-badge admin-badge">Admin</span>
               </td>
               <td>
                  <a href="admin_users.php?delete=<?php echo $fetch_admin['id']; ?>&type=admin" class="table-action-btn delete-btn" onclick="return confirm('delete this admin user?');"><i class="fas fa-trash"></i> Delete</a>
               </td>
            </tr>
            <?php
                  $admin_count++;
               }
            } else {
               echo '<tr><td colspan="5" class="empty-state"><i class="fas fa-user-shield"></i><br>No admin users found</td></tr>';
            }
            ?>
         </tbody>
      </table>
   </div>

   <!-- REGULAR USERS TABLE -->
   <div class="section-header user">
      <h2>Regular Users</h2>
   </div>

   <div class="table-container">
      <table class="data-table user-table">
         <thead>
            <tr>
               <th>User ID</th>
               <th>Username</th>
               <th>Email</th>
               <th>User Type</th>
               <th>Actions</th>
            </tr>
         </thead>
         <tbody>
            <?php
               $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
               if(mysqli_num_rows($select_users) > 0){
                  $user_count = 1;
                  while($fetch_users = mysqli_fetch_assoc($select_users)){
            ?>
            <tr>
               <td><?php echo $user_count; ?></td>
               <td><?php echo $fetch_users['name']; ?></td>
               <td><?php echo $fetch_users['email']; ?></td>
               <td>
                  <span class="user-type-badge user-badge">User</span>
               </td>
               <td>
                  <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>&type=user" class="table-action-btn delete-btn" onclick="return confirm('delete this user?');"><i class="fas fa-trash"></i> Delete</a>
               </td>
            </tr>
            <?php
                  $user_count++;
               }
            } else {
               echo '<tr><td colspan="5" class="empty-state"><i class="fas fa-users"></i><br>No regular users found</td></tr>';
            }
            ?>
         </tbody>
      </table>
   </div>

</section>









<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>