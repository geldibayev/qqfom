<?php
   session_start();
   if (!isset($_SESSION['login'])) {
      header('Location: main.php');
      exit;
   }
   require_once 'modules/db.php';
   $user_id = (int) $_SESSION['id'];
   $group_by_query = 'SELECT SUM(plant_result_gektar * plant_income) AS income, session_id FROM user_plants WHERE user_id=' . $user_id . ' GROUP BY session_id';
   $result = mysqli_query($conn, $group_by_query);
?>
<!DOCTYPE html>
<html lang="uz">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <link rel="stylesheet" href="css/bootstrap.min.css">
   <link rel="stylesheet" href="css/main.css">
   <title>Tarix</title>
</head>

<body class="pb-4">
   <?php require_once 'modules/navbar.php'?>
   <div class="flex-block">
      <div class="container">
         <div class="plants-block pt-lg-5 pb-lg-5 px-lg-5 px-3 pb-3 pt-3">
            <h2 class="text-center mb-3">Natijalar</h2>
            <table class="table table-bordered table-hover align-middle">
               <thead>
                  <tr class="text-center"> 
                     <th>№</th>
                     <th>Ekin nomi (ekiladigan yer maydoni)</th>
                     <th>Olinadigan daromad</th>
                     <th>Ma'lumot</th>
                  </tr>
               </thead>
               <tbody>
                  <?php 
                        $i = 1;
                        while ($result && ($group_by_row = mysqli_fetch_array($result))) {
                           echo  '<tr>
                                    <td>'.$i.'</td>
                                    <td>'; 
                           $query = "SELECT plant_name, plant_result_gektar FROM `user_plants` INNER JOIN plants ON plants.id = plant_id WHERE user_id=" . $user_id . " AND session_id='" . $group_by_row['session_id'] . "'"; 
                           $plant_result = mysqli_query($conn, $query);
                           while ($plant_result && ($plants_name = mysqli_fetch_array($plant_result))) {
                              echo ucfirst($plants_name['plant_name']) . ' (<strong>' . number_format((float)$plants_name['plant_result_gektar'], 2, '.', ' ') . 'ga</strong>)<br>';
                           }

                           echo  '  </td>
                                    <td class="text-end">'. number_format((float)$group_by_row['income'], 2, '.', ' ') . '</td>
                                    <td class="text-center"><a class="btn btn-primary" href="show_answer.php?id='.$group_by_row['session_id'].'">Batafsil</a></td>
                                 </tr>';
                           $i++;
                        }
                  ?>
               </tbody>
               <tfoot>
                  
               </tfoot>
            </table>
         </div>
      </div>
   </div>
   <script src="js/bootstrap.min.js"></script>
   <?php mysqli_close($conn); ?>

</body>

</html>
