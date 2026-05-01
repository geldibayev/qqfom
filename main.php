<?php
   session_start();
?>
<!DOCTYPE html>
<html lang="uz">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <link rel="stylesheet" href="css/bootstrap.min.css">
   <link rel="stylesheet" href="css/main.css">
   <title>Farm optimization model</title>
</head>

<body class="pb-4">
   <?php require_once 'modules/navbar.php'?>
   <div class="flex-block">
      <div class="container">
         <div class="plants-block pt-5 pb-5 px-5">
            <form action="" method="POST">
               <div id="first-block">
                  <h1 class="fw-bold text-center mb-5">Imkoniyatlaringizni kiriting</h1>
                  <div class="row">
                     <div class="col-lg-4 col-12 col-md-6 mb-4">
                        <label for="place" class="form-label mb-1">Jami yer maydonini kiriting (gektarda):</label>
                        <input type="number" step="any" min="0" inputmode="decimal" class="form-control input-opportunity" id="place">
                        <div class="invalid-feedback">
                           Yer maydonini kiritish shart
                        </div>
                     </div>
                     <div class="col-lg-4 col-12 col-md-6 mb-4">
                        <label for="paxta" class="form-label mb-1">Paxta shartnoma bo'yicha (gektarda):</label>
                        <input type="number" step="any" min="0" inputmode="decimal" class="form-control input-opportunity" id="paxta">
                        <div class="invalid-feedback">
                           Paxta shartnoma bo'yicha kiritish shart
                        </div>
                     </div>
                     <div class="col-lg-4 col-12 col-md-6 mb-4">
                        <label for="bugdoy" class="form-label mb-1">Bug'doy shartnoma bo'yicha (gektarda):</label>
                        <input type="number" step="any" min="0" inputmode="decimal" class="form-control input-opportunity" id="bugdoy">
                        <div class="invalid-feedback">
                           Bug'doy shartnoma bo'yicha kiritish shart
                        </div>
                     </div>
                     <div class="col-lg-4 col-12 col-md-6 mb-4">
                        <label for="oil" class="form-label mb-1">Yonilg'i miqdorini kiriting (litrda):</label>
                        <input type="number" step="any" min="0" inputmode="decimal" class="form-control input-opportunity" id="oil">
                        <div class="invalid-feedback">
                           Yinilg'i miqdorini kiritish shart
                        </div>
                     </div>
                     <div class="col-lg-4 col-12 col-md-6 mb-4">
                        <label for="water" class="form-label mb-1">Suv miqdorini kiriting (tonnada):</label>
                        <input type="number" step="any" min="0" inputmode="decimal" class="form-control input-opportunity" id="water">
                        <div class="invalid-feedback">
                           Suv miqdorini kiritish shart
                        </div>
                     </div>
                     <div class="col-lg-4 col-12 col-md-6 mb-4">
                        <label for="human" class="form-label mb-1">Doimiy ishchilar sonini kiriting:</label>
                        <input type="number" step="1" min="0" inputmode="numeric" class="form-control input-opportunity" id="human">
                        <div class="invalid-feedback">
                           Ishchi kuchini kiritish shart
                        </div>
                     </div>
                  </div>
                  <div class="row mt-4">
                     <div class="col-lg-4 offset-lg-4 col-md-6 offset-md-3 col-12">
                        <button class="btn btn-blue w-100" onclick="firstBtnClick(event, 'first-block', 'second-block')">Keyingi qadam</button>
                     </div>
                  </div>
               </div>

               <div id="second-block" class="d-none">
                  <h1 class="fw-bold text-center mb-5">Ekin turlarini tanlang</h1>
                  <div class="row">
                     <?php
                           require_once 'modules/db.php';
                           $query = "SELECT id, plant_name FROM plants WHERE id NOT IN (1, 2)";

                           $result = mysqli_query($conn, $query);

                           while ($result && ($row = mysqli_fetch_array($result))) {
                              $plantName = trim($row['plant_name']);
                              $plantLabel = ucfirst($plantName);

                              echo '<div class="col-lg-3 col-12 col-md-6 mb-4">
                                       <label class="w-100 input-label" for="plant-' . $row['id'] . '">' . $plantLabel . '
                                          <input type="checkbox" class="farmer-checkbox" id="plant-' . $row['id'] . '" data-plant-name="' . htmlspecialchars($plantName, ENT_QUOTES) . '" data-plant-label="' . htmlspecialchars($plantLabel, ENT_QUOTES) . '" autocomplete="off">
                                          <span class="checkmark"></span>
                                       </label>
                                    </div>';
                           }
                           mysqli_close($conn);
                     ?>

                  </div>
                  <div class="row mt-4">
                     <div class="col-lg-4 offset-lg-4 col-md-6 offset-md-3 col-12">
                        <div class="btn-group w-100">
                           <button class="btn btn-blue" onclick="showPrevBlock(event, 'first-block', 'second-block')">Oldingi qadam</button>
                           <button class="btn btn-blue" onclick="secondBtnClick(event, 'second-block', 'third-block')">Keyingi qadam</button>
                        </div>
                     </div>
                  </div>
               </div>

               <div id="third-block" class="d-none">
                  <h1 class="fw-bold text-center mb-5">Ekin narxlarini kiriting</h1>
                  <div class="row" id="price-block"></div>
                  <div class="row mt-4">
                     <div class="col-lg-4 offset-lg-4 col-md-6 offset-md-3 col-12">
                        <div class="btn-group w-100">
                           <button class="btn btn-blue" onclick="showPrevBlock(event, 'second-block', 'third-block')">Oldingi qadam</button>
                           <button class="btn btn-blue" onclick="thrirdBtnClick(event, 'third-block', 'firth-block')">Keyingi qadam</button>
                        </div>
                     </div>
                  </div>
               </div>

               <div id="firth-block" class="d-none">
                  <h1 class="fw-bold text-center mb-5">Har gektardan hosildorlikni kiriting</h1>
                  <div class="row" id="sentner-block"></div>
                  <div class="row mt-4">
                     <div class="col-lg-4 offset-lg-4 col-md-6 offset-md-3 col-12">
                        <div class="btn-group w-100">
                           <button class="btn btn-blue" onclick="showPrevBlock(event, 'third-block', 'firth-block')">Oldingi qadam</button>
                           <button class="btn btn-blue" onclick="sendFarmerData(event)">Natijani olish</button>
                        </div>
                     </div>
                  </div>
               </div>
            </form>

            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
               <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
               </symbol>
               <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
               </symbol>
               <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
               </symbol>
            </svg>
            <div class="modal fade" id="error-modal" tabindex="-1" aria-labelledby="error-label" aria-hidden="true">
               <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                     <div class="alert alert-danger mb-0 alert-dismissible fade show" role="alert">
                        <strong id="modal-change-text"></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <script src="js/bootstrap.min.js"></script>
   <script src="js/main.js"></script>
</body>

</html>
