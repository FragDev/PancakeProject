<?php
include 'phpback/db.php';
session_start();
if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
  try {
    $stm = DB()->prepare('select *, DATE_FORMAT(prepare_datetime, "%e/%c/%Y") as "date", DATE_FORMAT(prepare_datetime, "%h:%i %p") as "time" from planned_cooking where id = :cooking_id and user_id = :user_id');
    $stm->execute([
      'cooking_id' => $_GET['id'],
      'user_id' => $_SESSION['user_id'],
    ]);
    $planned_cooking = $stm->fetchAll();
  } catch (PDOException $e) {
    echo "Connection failed: " . $e -> getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr" data-theme="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] != NULL ? $_COOKIE['theme'] : 'dark'; ?>">

<head>
  <meta charset="utf-8">
  <title>Pancake Machine</title>
  <link href="styles/style.css" rel="stylesheet" type="text/css">
  <link href="styles/pancakes.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.jsdelivr.net/npm/daisyui@2.15.2/dist/full.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/index.min.css" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
  <div class="flex flex-col justify-between items-center bg-base-200 h-screen">
    <!-- Navbar -->
    <div class="navbar bg-base-100">
      <a class="btn btn-ghost text-accent normal-case text-xl">PancakeProject</a>
      <div class="navbar-end absolute right-8">
        <?php
        if (isset($_SESSION['user_id'])) {
          echo ('<div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-accent rounded-btn"><i class="fa-regular fa-circle-user fa-xl mr-3"></i>' . $_SESSION["name"] . '</label>
                <ul tabindex="0" class="menu dropdown-content p-2 shadow bg-base-100 rounded-box w-52 mt-4">
                  <li><label for="settings-modal">Paramètres</label></li>
                  <li><a href="phpback/login/logout.php">Se déconnecter</a></li>
                </ul>
              </div>');
        } else {
          echo '<label for="login-modal" class="btn text-white bg-primary hover:bg-primary-focus border-none modal-button">Connexion</label>';
        }
        ?>
      </div>
    </div>

    <div class="bg-base-100 shadow-xl rounded-2xl my-4">
      <!-- Menu Bar -->
      <div class="dropdown w-full rounded-2xl rounded-b-none bg-base-300">
        <div class="bg-opacity-100">
          <div class="tabs w-full flex-grow-0">
            <a href="index.php" class="tab tab-lifted tab-border-none tab-lg flex-1 rounded-tr-2xl text-accent-focus font-normal hover:bg-base-200">Programmé</a>
            <a href="plannify.php" class="tab tab-lifted tab-active tab-border-none tab-lg flex-1 rounded-tl-2xl text-accent font-medium">Planifier</a>
            <a href="history.php" class="tab tab-lifted tab-border-none tab-lg flex-1 text-accent-focus font-normal hover:bg-base-200">Historique</a>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="grid grid-cols-2 sm:grid-cols-3 m-8 mt-4 gap-4 justify-items-center">
        <!-- Title -->
        <h2 class="text-center text-2xl font-bold col-span-2 sm:col-span-3">Planifier une cuisson</h2>

        <div class="flex-col items-center justify-center col-span-2 w-64">
          <form id="new-cooking-form" action="phpback/new-cooking.php" method="post">
            <div class="hidden">
              <input type="text" name="cooking_id" <?php echo ((isset($_GET['id']) && !isset($_GET['replanned'])) ? 'value="' . $_GET['id'] . '"' : ""); ?>>
            </div>

            <!-- Pancake Size -->
            <div class="mb-3">
              <input type="range" name="size" min="1" max="3" <?php echo ('value="' . ((isset($planned_cooking) and count($planned_cooking)) > 0 ? array_search($planned_cooking[0]['size'], array(1 => 'SMALL', 2 => 'MEDIUM', 3 => 'LARGE')) : "1") . '"'); ?> class="range range-accent range-sm" step="1" required />
              <div class="w-full flex justify-between text-md font-medium text-accent px-2">
                <span>S</span>
                <span>M</span>
                <span>L</span>
              </div>
            </div>

            <!-- Pancake Number -->
            <div class="flex flex-row rounded-lg relative mb-3">
              <button type="button" data-action="decrement" class="py-1.5 bg-white text-black hover:text-white hover:bg-primary h-full w-20 rounded-l cursor-pointer border border-solid border-gray-300">
                <span class="m-auto text-3xl font-light">-</span>
              </button>
              <input type="number" name="quantity" <?php echo ('value="' . ((isset($planned_cooking) and count($planned_cooking)) > 0 ? $planned_cooking[0]['quantity'] : "1") . '"'); ?> class="py-1.5 outline-none focus:outline-none text-center w-full bg-white font-semibold text-md hover:text-black focus:text-black focus:border-primary md:text-basecursor-default flex items-center text-gray-700 border border-l-0 border-r-0 border-solid border-gray-300" name="custom-input-number" min="1" required></input>
              <button type="button" data-action="increment" class="py-1.5 bg-white text-black hover:text-white hover:bg-primary h-full w-20 rounded-r cursor-pointer border border-solid border-gray-300">
                <span class="m-auto text-3xl font-light">+</span>
              </button>
            </div>

            <!-- Pancake Day -->
            <div class="datepicker relative form-floating mb-3" data-mdb-toggle-button="false">
              <input type="text" name="date" <?php echo ('value="' . ((isset($planned_cooking) and count($planned_cooking)) > 0 ? $planned_cooking[0]['date'] : "") . '"'); ?> class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-primary focus:outline-none" placeholder="Select a date" data-mdb-toggle="datepicker" required />
              <label for="floatingInput" class="text-gray-700">Sélectionne le jour</label>
            </div>

            <!-- Pancake Hours -->
            <div class="timepicker relative form-floating mb-3" data-mdb-with-icon="false" id="input-toggle-timepicker">
              <input type="text" name="time" <?php echo ('value="' . ((isset($planned_cooking) and count($planned_cooking)) > 0 ? $planned_cooking[0]['time'] : "") . '"'); ?> class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-primary focus:outline-none" placeholder="Select a date" data-mdb-toggle="input-toggle-timepicker" required />
              <label for="floatingInput" class="text-gray-700">Sélectionne l'heure</label>
            </div>
          </form>
        </div>

        <div class="flex-col items-center my-auto absolute sm:relative invisible sm:visible max-h-64 overflow-y-scroll">
          <div class="butter"></div>
          <div id="pancakes-stack">
            <div class="pancake"></div>
          </div>
          <div class="plate">
            <div class="plate-bottom"></div>
          </div>
        </div>

        <!-- Pancake Valid Btn -->
        <button class="btn col-span-2 sm:col-span-3 w-64 sm:w-full text-white bg-primary hover:bg-primary-focus border-none" form="new-cooking-form">Planifier</button>
      </div>
    </div>

    <footer class="footer footer-center p-4 bg-base-300 text-base-content">
      <div>
        <p class="text-accent">Développé par <strong class="text-primary">FragmentDev</strong></p>
      </div>
    </footer>
  </div>

  <!-- Modals -->
  <input type="checkbox" id="login-modal" class="modal-toggle" />
  <div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full max-w-xs">
      <label for="login-modal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
      <h3 class="text-lg font-bold">Connexion</h3>
      <form class="flex flex-col mt-4" action="phpback/login/login.php" method="post">
        <label for="mail">Mail</label>
        <input type="email" name="mail" placeholder="Entrer votre mail" class="input input-bordered bg-neutral w-full max-w-xs mb-4" />
        <label for="password">Mot de passe</label>
        <input type="password" name="password" placeholder="Entrer votre mot de passe" class="input input-bordered bg-neutral w-full max-w-xs mb-6" />
        <button name="button" class="btn text-white border-none bg-primary hover:bg-primary-accent w-full max-w-xs">Connexion</button>
      </form>
    </div>
  </div>

  <input type="checkbox" id="settings-modal" class="modal-toggle" />
  <div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full max-w-xs">
      <label for="settings-modal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
      <h3 class="text-lg font-bold">Paramètres</h3>
      <form class="" action="phpback/settings.php" method="post">
        <div class="form-control">
          <label class="label cursor-pointer">
            <span class="label-text">Thème sombre</span>
            <input name="theme" type="checkbox" class="toggle toggle-accent" <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'dark' ? 'checked' : '' ?> />
          </label>
        </div>
        <button name="button" class="btn text-white border-none bg-primary hover:bg-primary-accent w-full max-w-xs">Appliquer</button>
      </form>
    </div>
  </div>

  <!-- SCRIPTS -->
  <script src="scripts/scripts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/index.min.js"></script>
</body>

</html>