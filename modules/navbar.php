<nav class="navbar navbar-expand-lg navbar-dark bg-custom mb-4">
      <div class="container">
         <a class="navbar-brand pt-2 pb-2 mb-0 h1" id="fom" href="main.php">Farm Optimization Model</a>
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
         </button>
         <?php
         $sessionLogin = $_SESSION['login'] ?? null;
         if ($sessionLogin == 'root') 
            $root = '<li><a class="dropdown-item" href="admin.php">Admin panel</a></li>';
         else 
            $root = '<li><a class="dropdown-item" href="#">Tizim haqida</a></li>';
            
         if ($sessionLogin !== null) {
            echo '<div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="me-auto mb-2 mb-lg-0"></div>
            <div class="dropdown">
               <a class="drop-link dropdown-toggle" id="dropdownMenuButton1" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  '. ucfirst($sessionLogin) .'
               </a>
               <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                  <li><a class="dropdown-item" href="main.php">Bosh sahifa</a></li>
                  '. $root .'
                  <li><a class="dropdown-item" href="history.php">Tarix</a></li>
                  <li><a class="dropdown-item" href="modules/logout.php">Chiqish</a></li>
               </ul>
            </div>
         </div>';
         } else {
            echo '<div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="me-auto mb-2 mb-lg-0"></div>
            <div class="dropdown">
               <a class="drop-link dropdown-toggle" id="dropdownMenuButton1" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Kirish
               </a>
               <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                  <li><a class="dropdown-item" href="#">Tizim haqida</a></li>
                  <li><a class="dropdown-item" href="main.php">Bosh sahifa</a></li>
                  <li><a class="dropdown-item" href="login.php">Kirish</a></li>
                  <li><a class="dropdown-item" href="register.php">R\'oyxatdan o\'tish</a></li>
               </ul>
            </div>
         </div>';
         }
         ?>
      <script>
         window.onload = function changeFom() {
            if (window.screen.width < 768) {
               document.getElementById('fom').innerHTML = 'FOM';
            }
         }
         window.onresize = function changeF() {
            if (window.screen.width < 768) {
               document.getElementById('fom').innerHTML = 'FOM';
            } else {
               document.getElementById('fom').innerHTML = 'Farm Optimization Model';
            }
         }
      </script>
      </div>
   </nav>
