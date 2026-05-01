<?php
   session_start();
   $logs = [];
   $login = '';
   $pwd = '';
   
   if (isset($_SESSION['login']) && $_SESSION['login'] == 'root') {
      header('Location: admin.php');
      exit;
   }

   if (isset($_SESSION['login'])) {
      header('Location: main.php');
      exit;
   }

   if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $teg1 = '<div class="row mb-4"><div class="col-lg-8 offset-lg-2 col-12 offset-0"><div class="alert alert-danger alert-dismissible fade show" role="alert">  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg><strong>';
      $teg2 = '</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div></div>';

      $login = trim($_POST['login'] ?? '');
      $pwd = trim($_POST['pass'] ?? '');

      if (empty($login)) {
         $logs[] = $teg1 . 'Loginingizni kiriting' . $teg2;
      } else {
         $login = clr_str($login);
      }
      if (empty($pwd)) {
         $logs[] = $teg1 . 'Parolingizni kiriting' . $teg2;
      } else {
         $pwd = clr_str($pwd);
      }
      if (empty($logs)) {
         require_once 'modules/db.php';
         $login = mysqli_real_escape_string($conn, $login);
         $pwd = mysqli_real_escape_string($conn, $pwd);

         $query = "SELECT * FROM users WHERE user_login='".$login."'";
         
         if ($result = mysqli_query($conn, $query)) {
            $row = mysqli_fetch_array($result);
            if ($row && isset($row['user_pass']) && password_verify($pwd, $row['user_pass'])) {
               $_SESSION['login'] = $row['user_login'];
               $_SESSION['id'] = $row['user_id'];

               if ($_SESSION['login'] == 'root')
                  header('Location: admin.php');
               else 
                  header('Location: main.php');
               exit;
            }
            else {
               $login = '';
               $pwd = '';
               $logs[] = $teg1 . 'Login yoki parol xato kiritilgan' . $teg2;
            }
         }
         else {
            $login = '';
            $pwd = '';
            $logs[] = $teg1 . 'Login yoki parol xato kiritilgan' . $teg2;
         }
         mysqli_close($conn);
      }
   }
   function clr_str($str) {
      $str = trim($str);
      $str = stripslashes($str);
      $str = htmlspecialchars($str);
      return $str;
   }
?>

<!DOCTYPE html>
<html lang="uz">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <link rel="stylesheet" href="css/bootstrap.min.css">
   <link rel="stylesheet" href="css/main.css">
   <title>Optimal</title>
</head>

<body class="pb-4">
<?php require_once 'modules/navbar.php'?>
   
   <div class="flex-block">
      <div class="container">
      <?php 
            if(!empty($logs)) 
               echo array_shift($logs);
      ?>
         <div class="login-block pt-5 pb-5 px-5">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
               <div >
                  <h3 class="fw-bold text-center mb-4">Tizimga kiring</h3>
                  <div class="row mb-4">
                     <div class="col-12">
                        <input type="text" class="form-control log-input login-label" name="login" id="login" value="<?php echo htmlspecialchars($login) ?>" placeholder="Loginingizni kiriting">
                        <div class="invalid-feedback">
                           Login kiritish shart
                        </div>
                     </div>
                  </div>
                  <div class="row mb-4">
                     <div class="col-12">
                        <input type="password" class="form-control log-input login-label" name="pass" id="pass" placeholder="Parol kiriting">
                        <div class="invalid-feedback">
                           Parol kiritish shart
                        </div>
                     </div>
                  </div>
                  <div class="row mt-4">
                     <div class="col-12">
                        <button class="btn btn-blue login-label w-100" onclick="checkLogin(event)">Kirish</button>
                     </div>
                  </div>
               </div>
            </form>

            <!-- Modal -->
            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
               <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                  <path
                     d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
               </symbol>
               <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                  <path
                     d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
               </symbol>
               <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                  <path
                     d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
               </symbol>
            </svg>
            <div class="modal fade" id="reg-error-modal" tabindex="-1" aria-labelledby="error-label" aria-hidden="true">
               <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                     <div class="alert alert-danger mb-0 alert-dismissible fade show" role="alert">
                        <strong id="alert-custom-text"></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                     </div>
                  </div>
               </div>
            </div>

         </div>
      </div>
   </div>
   <script src="js/bootstrap.min.js"></script>
   <script src="js/reg.js"></script>
</body>

</html>
