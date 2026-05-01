<?php 
   session_start();
   if (!isset($_SESSION['login']) || $_SESSION['login'] != 'root') {
      header('Location: login.php');
      exit;
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
         <div class="plants-block pt-5 pb-5 px-5">
            <form action="admin_save.php" method="POST">
               

               <div id="second-block" >
                  <h1 class="fw-bold text-center mb-5">Ekinlar uchun harajatlarni</h1>
                  <div class="row">
                     
                     <?php
                           require_once 'modules/db.php';

                           $query = "SELECT oil_price FROM oil";
                           $result = mysqli_query($conn, $query);
                           $row = mysqli_fetch_array($result);
                           $oilPrice = $row['oil_price'] ?? '';

                           echo '<div class="col-lg-4 col-12 col-md-6 mb-4">
                                       <label for="oil_price" class="form-label mb-1">Yonilgi narxini kiriting:</label>
                                       <input type="text" class="form-control input-opportunity" name="oil_price" id="oil_price" value="'.$oilPrice.'">
                                 </div>';

                           $query = "SELECT id, plant_name, consusption FROM plants";
                           
                           $result = mysqli_query($conn, $query);
                           
                           while ($result && ($row = mysqli_fetch_array($result))) {
                              echo '<div class="col-lg-4 col-12 col-md-6 mb-4">
                                       <label class="form-label mb-1" for="' . $row['id'] . '">'. ucfirst($row['plant_name']) .' uchun:</label>
                                       <input type="text" class="form-control input-opportunity" name="'.$row['id'].'" id="'. $row['id'] .'" value="'.$row['consusption'].'" autocomplete="off">
                                    </div>';
                           }
                           mysqli_close($conn);
                     ?>
                      
                  </div>
                  <div class="row mt-4">
                     <div class="col-lg-4 offset-lg-4 col-md-6 offset-md-3 col-12">
                        <button class="btn btn-blue w-100">Saqlash</button>
                     </div>
                  </div>
               </div>

               
            </form>


         </div>
      </div>
   </div>
   <script src="js/bootstrap.min.js"></script>
   <script src="js/admin.js"></script>
</body>

</html>
