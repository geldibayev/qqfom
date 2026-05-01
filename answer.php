<?php
   session_start();

   function exitWithError(?mysqli $conn = null): void {
      if ($conn instanceof mysqli) {
         mysqli_close($conn);
      }

      http_response_code(400);
      exit('error');
   }

   function normalizePlantKey(string $value): string {
      $value = strtolower(trim($value));
      $wheatAliases = ['bugdoy', "bug'doy"];

      if (in_array($value, $wheatAliases, true)) {
         return 'bugdoy';
      }

      return $value;
   }

   $get_json = file_get_contents('php://input');
   if (!$get_json) {
      header('Location: main.php');
      exit;
   }

   $user_id = $_SESSION['id'] ?? 3;
   $farmer_data = json_decode($get_json, true);
   if (!is_array($farmer_data)) {
      exitWithError();
   }

   require_once 'modules/db.php';

   $prices = [];
   $kg = [];
   $plant_price = [];
   $plant_consumption = [];
   $plants_id = [];
   $paxta_gektar = 0;
   $bugdoy_gektar = 0;
   $session_id = uniqid('', true);

   $SIMPLEX = [[0]];

   $i = 1;
   $j = 0;
   $user_posibility_query = "INSERT INTO `user_posibility` (`id`, `place`, `cotton_gektar`, `wheat_gektar`, `oil_litr`, `water_tonna`, `human_son`, `session_id`, `user_id`) VALUES (NULL, '";

   foreach ($farmer_data as $data) {
      $itemId = (string) ($data['id'] ?? '');
      $itemValue = (float) ($data['value'] ?? 0);
      $itemKey = normalizePlantKey($itemId);
      $user_plants_query = "INSERT INTO `user_plants` (`id`, `plant_id`, `plant_price`, `plant_sentner`, `plant_result_gektar`, `plant_income`, `session_id`, `user_id`) VALUES (NULL, '";

      if ($i < 7) {
         if ($itemKey === 'paxta') {
            $SIMPLEX[0][0] -= $itemValue;
            $paxta_gektar = $itemValue;
         } else if ($itemKey === 'bugdoy') {
            $SIMPLEX[0][0] -= $itemValue;
            $bugdoy_gektar = $itemValue;
         } else {
            if ($itemKey === 'human') {
               $itemValue *= 8 * 365;
            }

            $SIMPLEX[$j][0] = $itemValue;
            $j++;
         }

         $user_posibility_query .= $itemValue . "', '";
      } else {
         if (preg_match('/^plant-(\d+)-(narxi|sentner)$/', $itemId, $matches) === 1) {
            $plantId = (int) $matches[1];
            $fieldType = $matches[2];

            if ($fieldType === 'narxi') {
               $prices[$plantId] = $itemValue;
               $i++;
               continue;
            }

            $query = "SELECT plants.id, plant_name, consusption, oil_price, oil, worker, water FROM plants, oil WHERE plants.id=" . $plantId;
            $result = mysqli_query($conn, $query);
            $row = $result ? mysqli_fetch_array($result) : false;
            if (!$row) {
               exitWithError($conn);
            }

            $plant_consumption[$plantId] = (($itemValue / 10) * (float) $row['consusption']);
            $plant_consumption[$plantId] += ((float) $row['oil'] * ($itemValue / 10) * (float) $row['oil_price']);

            $kg[$plantId] = $itemValue * 100;
            $income = ($prices[$plantId] ?? 0) * $kg[$plantId] - $plant_consumption[$plantId];

            $plants_id[] = (int) $row['id'];
            $user_plants_query .= $row['id'] . "', '" . ($prices[$plantId] ?? 0) . "', '" . $itemValue . "', 0,'" . $income . "', '" . $session_id . "', '" . $user_id . "');";

            $j++;
            $SIMPLEX[0][$j] = 1;
            $SIMPLEX[1][$j] = (float) $row['oil'] * ($itemValue / 10);
            $SIMPLEX[2][$j] = (float) $row['water'];
            $SIMPLEX[3][$j] = (float) $row['worker'] * $itemValue;

            if (($SIMPLEX[0][0] ?? 0) < 0 || ($SIMPLEX[1][0] ?? 0) < 0 || ($SIMPLEX[2][0] ?? 0) < 0 || ($SIMPLEX[3][0] ?? 0) < 0) {
               exitWithError($conn);
            }

            mysqli_query($conn, $user_plants_query);
         } else {
            $separatorPosition = strpos($itemId, '-');
            if ($separatorPosition === false) {
               exitWithError($conn);
            }

            $rawPlantName = substr($itemId, 0, $separatorPosition);

            if (substr($itemId, -5) === 'narxi') {
               $prices[$rawPlantName] = $itemValue;
            } else {
               $plant_name = mysqli_real_escape_string($conn, $rawPlantName);
               $query = "SELECT plants.id, consusption, oil_price, oil, worker, water FROM plants, oil WHERE plant_name='" . $plant_name . "'";

               $result = mysqli_query($conn, $query);
               $row = $result ? mysqli_fetch_array($result) : false;
               if (!$row) {
                  exitWithError($conn);
               }

               $plantKey = normalizePlantKey($rawPlantName);
               $plant_consumption[$rawPlantName] = (($itemValue / 10) * (float) $row['consusption']);
               $plant_consumption[$rawPlantName] += ((float) $row['oil'] * ($itemValue / 10) * (float) $row['oil_price']);

               $kg[$rawPlantName] = $itemValue * 100;
               $income = ($prices[$rawPlantName] ?? 0) * $kg[$rawPlantName] - $plant_consumption[$rawPlantName];

               $plants_id[] = $row[0];
               $user_plants_query .= $row[0] . "', '" . ($prices[$rawPlantName] ?? 0) . "', '" . $itemValue . "', 0,'" . $income . "', '" . $session_id . "', '" . $user_id . "');";

               if ($plantKey === 'paxta') {
                  $j = 1;
                  $SIMPLEX[1][0] = ($SIMPLEX[1][0] ?? 0) - (float) $row['oil'] * ($itemValue / 10) * $paxta_gektar;
                  $SIMPLEX[2][0] = ($SIMPLEX[2][0] ?? 0) - (float) $row['water'] * $paxta_gektar;
                  $SIMPLEX[3][0] = ($SIMPLEX[3][0] ?? 0) - (float) $row['worker'] * $itemValue * $paxta_gektar;
               } else {
                  $j++;
               }

               if ($plantKey === 'bugdoy') {
                  $SIMPLEX[1][0] = ($SIMPLEX[1][0] ?? 0) - (float) $row['oil'] * ($itemValue / 10) * $bugdoy_gektar;
                  $SIMPLEX[2][0] = ($SIMPLEX[2][0] ?? 0) - (float) $row['water'] * $bugdoy_gektar;
                  $SIMPLEX[3][0] = ($SIMPLEX[3][0] ?? 0) - (float) $row['worker'] * $itemValue * $bugdoy_gektar;
               }

               $SIMPLEX[0][$j] = 1;
               $SIMPLEX[1][$j] = (float) $row['oil'] * ($itemValue / 10);
               $SIMPLEX[2][$j] = (float) $row['water'];
               $SIMPLEX[3][$j] = (float) $row['worker'] * $itemValue;

               if (($SIMPLEX[0][0] ?? 0) < 0 || ($SIMPLEX[1][0] ?? 0) < 0 || ($SIMPLEX[2][0] ?? 0) < 0 || ($SIMPLEX[3][0] ?? 0) < 0) {
                  exitWithError($conn);
               }

               mysqli_query($conn, $user_plants_query);
            }
         }
      }

      $i++;
   }

   foreach ($prices as $key => $value) {
      if (isset($kg[$key])) {
         $plant_price[$key] = $value * $kg[$key];
      }
   }

   $SIMPLEX[4][0] = 0;

   foreach ($plant_price as $key => $value) {
      $SIMPLEX[4][] = ($value - ($plant_consumption[$key] ?? 0)) * -1;
   }

   require_once 'simplex_class.php';

   $result = [];
   for ($i = 0; $i < count($SIMPLEX[0]) - 1; $i++) {
      $result[] = 0;
   }

   $simplex_object = new simplexMethod($SIMPLEX);
   $table_result = $simplex_object->calculate($result);
   $table_result[0] = ($table_result[0] ?? 0) + $paxta_gektar;
   $table_result[1] = ($table_result[1] ?? 0) + $bugdoy_gektar;

   $limit = [];
   $limit[0] = array_sum($table_result);

   for ($i = 1; $i < 4; $i++) {
      $result_item = 0;
      $limit[$i] = 0;

      for ($j = 1; $j <= count($table_result); $j++) {
         $limit[$i] += (($SIMPLEX[$i][$j] ?? 0) * ($table_result[$result_item] ?? 0));
         $result_item++;
      }
   }

   $limit[4] = 0;
   $i = 1;
   foreach ($table_result as $value) {
      $limit[4] += (($SIMPLEX[4][$i] ?? 0) * -1 * $value);
      $i++;
   }

   $user_posibility_query .= $session_id . "', '" . $user_id . "');";
   mysqli_query($conn, $user_posibility_query);

   $i = 0;
   foreach ($table_result as $item) {
      if (!isset($plants_id[$i])) {
         $i++;
         continue;
      }

      $update_query = "UPDATE `user_plants` SET `plant_result_gektar` = '" . $item . "' WHERE `user_plants`.`plant_id` = '" . $plants_id[$i] . "' AND `user_plants`.`session_id`='" . $session_id . "';";
      $i++;
      mysqli_query($conn, $update_query);
   }

   $user_posibility_query = "INSERT INTO `user_posibility` (`id`, `place`, `cotton_gektar`, `wheat_gektar`, `oil_litr`, `water_tonna`, `human_son`, `session_id`, `user_id`) VALUES (NULL, '" . $limit[0] . "', '" . ($table_result[0] ?? 0) . "', '" . ($table_result[1] ?? 0) . "', '" . $limit[1] . "', '" . $limit[2] . "', '" . $limit[3] . "', '" . $session_id . "', '" . $user_id . "');";
   mysqli_query($conn, $user_posibility_query);
   mysqli_close($conn);

   echo $session_id;
?>
