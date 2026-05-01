<?php 
   session_start();
   $logs = [];
   $login = '';
   $pwd1 = '';
   $pwd2 = '';

   if (isset($_SESSION['login'])) {
      header('Location: main.php');
      exit;
   }

   if ($_SERVER["REQUEST_METHOD"] == "POST") {
      
      $teg1 = '<div class="row mb-4"><div class="col-lg-8 offset-lg-2 col-12 offset-0"><div class="alert alert-danger alert-dismissible fade show" role="alert">  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg><strong>';
      $teg2 = '</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div></div>';

      $login = trim($_POST['login'] ?? '');
      $pwd1 = trim($_POST['pwd1'] ?? '');
      $pwd2 = trim($_POST['pwd2'] ?? '');
      
      if (empty($login))
      {
         $logs[] = $teg1 . 'Login kiriting' . $teg2;
      }
      else {
         $login = clr_str($login);
         if (strlen($login) < 4) 
            $logs[] = $teg1 . 'Login uzunligi kamida 4 belgi bo\'lishi kerak' . $teg2;
      }

      if (empty($pwd1) || empty($pwd2)) {
         $logs[] = $teg1 . 'Parol kiriting va uni takroran kiriting' . $teg2;
      } 
      else {
         if (strlen(clr_str($pwd1)) < 6) {
            $logs[] = $teg1 . 'Kiritilgan parol uzunligi 6 simvoldan kam' . $teg2;
         }
         else {
            if ($pwd1 != $pwd2)
               $logs[] = $teg1 . 'Kiritilgan parollar bir xil emas' . $teg2;
         }
      }

      if (empty($logs)) {
         require_once 'modules/db.php';
         $login = mysqli_real_escape_string($conn, $login);
         $pwd1 = mysqli_real_escape_string($conn, $pwd1);
         $pwd2 = mysqli_real_escape_string($conn, $pwd2);

         try {
            $query = "SELECT * FROM users WHERE user_login='" . $login . "'";
            $result = mysqli_query($conn, $query);
            
            if ($result && mysqli_num_rows($result)) {
               $logs[] = $teg1 . 'Siz kiritgan login tizimda mavjud boshqa login kiriting' . $teg2;
            } else {
               $pwd_hash = password_hash($pwd1, PASSWORD_DEFAULT);
               $query = "INSERT INTO `users`(`user_login`, `user_pass`) VALUES ('" . $login . "', '" . $pwd_hash . "')";
               if (mysqli_query($conn, $query)) {
                  $_SESSION['login'] = $login;
                  $_SESSION['id'] = mysqli_insert_id($conn);

                  header('Location: main.php');
                  exit;
               }

               $logs[] = $teg1 . 'Ro\'yxatdan o\'tishda xatolik yuz berdi' . $teg2;
            }
         } catch (mysqli_sql_exception $e) {
            $logs[] = $teg1 . 'Ro\'yxatdan o\'tishda xatolik yuz berdi' . $teg2;
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
               <h3 class="fw-bold text-center mb-4">Ro'yxatdan o'ting</h3>
               <div class="row mb-4">
                  <div class="col-12">
                     <input type="text" class="form-control reg-input login-label" name="login" id="login" value="<?php echo htmlspecialchars($login) ?>" placeholder="Loginingizni kiriting">
                     <div class="invalid-feedback">
                        Login kiritilishi shart
                     </div>
                  </div>
               </div>
               <div class="row mb-4">
                  <div class="col-12">
                     <input type="password" class="form-control reg-input login-label" name="pwd1" id="pwd1" value="<?php echo htmlspecialchars($pwd1) ?>" placeholder="Parol kiriting">
                     <div class="invalid-feedback">
                        Parol kiritilishi shart
                     </div>
                  </div>
               </div>
               <div class="row mb-4">
                  <div class="col-12">
                     <input type="password" class="form-control reg-input login-label" name="pwd2" id="pwd2" value="<?php echo htmlspecialchars($pwd2) ?>" placeholder="Parolni qayta kiriting">
                     <div class="invalid-feedback">
                        Parol qayta kiritilishi shart
                     </div>
                  </div>
               </div>
               <div class="row mt-4">
                  <div class="col-12">
                     <button class="btn btn-blue login-label w-100" onclick="checkInputs(event, '.reg-input')">Ro'yxatdan o'tish</button>
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
