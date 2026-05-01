<?php
   session_start();

   function normalizePlantKey(string $value): string {
      $value = strtolower(trim($value));
      if ($value === 'paxta') {
         return 'paxta';
      }
      if (str_starts_with($value, 'bug')) {
         return 'bugdoy';
      }

      return $value;
   }

   function displayPlantName(string $value): string {
      return normalizePlantKey($value) === 'bugdoy' ? "Bug'doy" : ucfirst(trim($value));
   }

   if (!isset($_GET['id'])) {
      header('Location: main.php');
      exit;
   }

   require_once 'modules/db.php';

   $session_id = mysqli_real_escape_string($conn, (string) $_GET['id']);
   $query = "SELECT `place`, `cotton_gektar`, `wheat_gektar`, `oil_litr`, `water_tonna`, `human_son` FROM user_posibility WHERE session_id='" . $session_id . "'";
   $query_result = mysqli_query($conn, $query);
   if (!$query_result || !mysqli_num_rows($query_result)) {
      header('Location: main.php');
      exit;
   }

   $table_item = 1;
   $user_posible_data = [];
   $sum_place = 0;
   $sum_income = 0;

   while ($row = mysqli_fetch_row($query_result)) {
      if ($table_item == 1) {
         $i = 0;
         foreach ($row as $value) {
            $user_posible_data[$i] = $value;
            $i += 2;
         }
      } else {
         $i = 1;
         foreach ($row as $value) {
            $user_posible_data[$i] = $value;
            $i += 2;
         }
      }

      $table_item++;
   }

   ksort($user_posible_data);

   $query = "SELECT plant_name, plant_result_gektar, plant_result_gektar * plant_income as income
             FROM user_plants
             INNER JOIN plants
             ON user_plants.plant_id = plants.id
             WHERE session_id='" . $session_id . "'";
   $query_result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="uz">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <link rel="stylesheet" href="css/bootstrap.min.css">
   <link rel="stylesheet" href="css/main.css">
   <title>Natija</title>
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
                     <th>No</th>
                     <th>Ekin nomi</th>
                     <th>Ekiladigan yer maydoni (gektarda)</th>
                     <th>Olinadigan daromad</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $table_item = 1;
                     while ($query_result && ($row = mysqli_fetch_array($query_result))) {
                        if (normalizePlantKey($row['plant_name']) === 'paxta') {
                           $text = ' (shartnoma bo\'yicha <strong>' . ($user_posible_data[2] ?? 0) . '</strong> gektar)';
                        } else if (normalizePlantKey($row['plant_name']) === 'bugdoy') {
                           $text = ' (shartnoma bo\'yicha <strong>' . ($user_posible_data[4] ?? 0) . '</strong> gektar)';
                        } else {
                           $text = '';
                        }

                        echo '<tr>
                                 <td>' . $table_item . '</td>
                                 <td>' . displayPlantName($row['plant_name']) . $text . '</td>
                                 <td class="text-end">' . number_format((float) $row['plant_result_gektar'], 2, '.', ' ') . '</td>
                                 <td class="text-end">' . number_format((float) $row['income'], 2, '.', ' ') . '</td>
                              </tr>';

                        $sum_place += (float) $row['plant_result_gektar'];
                        $sum_income += (float) $row['income'];
                        $table_item++;
                     }
                  ?>
               </tbody>
               <tfoot>
                  <tr>
                     <th colspan="2" class="text-end">Jami:</th>
                     <th class="text-center"><?php echo number_format((float) $sum_place, 2, '.', ' ') ?></th>
                     <th class="text-end"><?php echo number_format((float) $sum_income, 2, '.', ' ') ?></th>
                  </tr>
               </tfoot>
            </table>
            <h2 class="text-center mb-3">Harajatlar</h2>
            <table class="table table-bordered table-hover align-middle">
               <thead>
                  <tr class="text-center">
                     <th style="width: 350px">Resurslar</th>
                     <th>Mavjud</th>
                     <th>Sarflanadi</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td class="text-end fw-bold">Jami yer maydoni:</td>
                     <td class="text-end"><?php echo number_format((float) ($user_posible_data[0] ?? 0), 2, '.', ' ')?></td>
                     <td class="text-end"><?php echo number_format((float) ($user_posible_data[1] ?? 0), 2, '.', ' ')?></td>
                  </tr>
                  <tr>
                     <td class="text-end fw-bold">Yonilg'i miqdori:</td>
                     <td class="text-end"><?php echo number_format((float) ($user_posible_data[6] ?? 0), 2, '.', ' ')?></td>
                     <td class="text-end"><?php echo number_format((float) ($user_posible_data[7] ?? 0), 2, '.', ' ')?></td>
                  </tr>
                  <tr>
                     <td class="text-end fw-bold">Suv miqdori:</td>
                     <td class="text-end"><?php echo number_format((float) ($user_posible_data[8] ?? 0), 2, '.', ' ')?></td>
                     <td class="text-end"><?php echo number_format((float) ($user_posible_data[9] ?? 0), 2, '.', ' ')?></td>
                  </tr>
                  <tr>
                     <td class="text-end fw-bold">Doimiy ishchilar soni:</td>
                     <td class="text-end"><?php echo number_format((float) ($user_posible_data[10] ?? 0) / 365 / 8, 2, '.', ' ')?></td>
                     <td class="text-end"><?php echo number_format((float) ($user_posible_data[11] ?? 0) / 365 / 8, 2, '.', ' ')?></td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <script src="js/bootstrap.min.js"></script>
   <?php mysqli_close($conn); ?>

</body>

</html>
