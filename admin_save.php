<?php 
   session_start();
   if (!isset($_SESSION['login']) || $_SESSION['login'] != 'root') {
      header('Location: login.php');
      exit;
   }
   if ($_SERVER["REQUEST_METHOD"] == "POST"){
      require_once 'modules/db.php';
      $oilPrice = mysqli_real_escape_string($conn, (string) ($_POST['oil_price'] ?? '0'));
      
      $oil_query = "UPDATE oil SET oil_price='" . $oilPrice . "' WHERE id=1";
      mysqli_query($conn, $oil_query);

      for ($i=1; $i < 17; $i++) { 
         $consumption = mysqli_real_escape_string($conn, (string) ($_POST[$i] ?? '0'));
         $plant_query = "UPDATE plants SET consusption='" . $consumption . "' WHERE id=" . $i;
         mysqli_query($conn, $plant_query);
      }

      $info = 'Baza yangilandi';
   } else {
      header('Location: admin.php');
      exit;
   }
   mysqli_close($conn);
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

<body class="pt-4 pb-4">
   
   <div class="flex-block">
      <div class="container">
         <div class="plants-block pt-5 pb-5 px-5">
               <div id="second-block" >
                  <h1 class="fw-bold text-center mb-5"><?php echo $info ?></h1>
               </div>
         </div>
      </div>
   </div>
</body>

</html>
