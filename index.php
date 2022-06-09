<?php
include 'phpback/db.php';
session_start();

if (isset($_SESSION['user_id'])) {
  try {
    $stm = DB()->prepare('select *, DATE_FORMAT(prepare_datetime, "%Hh%im %e/%c/%Y") as "formated_date" from planned_cooking where user_id = :user_id and prepare_datetime > CURRENT_TIMESTAMP order by prepare_datetime');
    $stm->execute([
      'user_id' => $_SESSION['user_id'],
    ]);
    $planned_cooking = $stm->fetchAll();
  } catch (PDOException $e) {
    // echo "Connection failed: " . $e -> getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr" data-theme="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] != NULL ? $_COOKIE['theme'] : 'dark'; ?>">

<head>
  <meta charset="utf-8">
  <title>Pancake Machine</title>
  <link rel="icon" type="image/png" href="assets/logo.png" />
  <link href="styles/style.css" rel="stylesheet" type="text/css">
  <link href="styles/pancakes.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.jsdelivr.net/npm/daisyui@2.15.2/dist/full.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/index.min.css" />
  <script src="https://kit.fontawesome.com/ca25d103c3.js" crossorigin="anonymous"></script>
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
            <a href="index.php" class="tab tab-lifted tab-active tab-border-none tab-lg flex-1 rounded-tr-2xl text-accent font-medium hover:bg-base-200">Programmé</a>
            <a href="plannify.php" class="tab tab-lifted tab-border-none tab-lg flex-1 rounded-tl-2xl text-accent-focus font-normal">Planifier</a>
            <a href="history.php" class="tab tab-lifted tab-border-none tab-lg flex-1 text-accent-focus font-normal hover:bg-base-200">Historique</a>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="grid grid-cols-2 sm:grid-cols-3 m-8 mt-4 gap-4 justify-items-center">
        <!-- Title -->
        <h2 class="text-center text-2xl font-bold col-span-3">Cuissons programmé</h2>
        <div class="overflow-x-auto w-full col-span-3">
          <table class="table w-full">
            <thead>
              <tr>
                <th>Quantité</th>
                <th>Taille</th>
                <th>Préparé pour</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!isset($planned_cooking) || count($planned_cooking) == 0) {
                echo '<tr><td colspan="4" class="text-center">Aucune cuisson plannifié.</td></tr>';
              } else {
                foreach ($planned_cooking as $cooking) {
                  echo '<tr>
                    <td>
                      <div class="flex items-center space-x-3">
                        <div class="indicator">
                          <span class="indicator-item badge badge-primary">x' . $cooking['quantity'] . '</span>
                          <div class="flex-col items-center my-auto absolute sm:relative invisible sm:visible">
                            <div class="butter small"></div>
                            <div id="pancakes-stack">';
                  for ($i = 0; $i < min(8, $cooking['quantity']); $i++) {
                    echo      '<div class="pancake small"></div>';
                  }
                  echo     '</div>
                            <div class="plate small">
                              <div class="plate-bottom small"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td>
                      ' . ($cooking['size'] != 'SMALL' ? ($cooking['size'] != 'MEDIUM' ? '<div class="badge badge-primary font-bold uppercase">large</div>' : '<div class="badge badge-secondary font-bold uppercase">medium</div>') : '<div class="badge badge-accent font-bold uppercase">small</div>') . '
                    </td>
                    <td>' . $cooking['formated_date'] . '</td>
                    <th>
                      <div class="flex items-center justify-center gap-2">
                        <a href="phpback/edit-planned.php?id=' . $cooking['id'] . '">
                          <i class="fa-solid fa-pen-to-square text-primary hover:text-primary-focus fa-xl"></i>
                        </a>
                        <a href="phpback/remove-planned.php?id=' . $cooking['id'] . '">
                          <i class="fa-solid fa-trash-can text-red-600 hover:text-red-800 fa-xl"></i>
                        </a>
                      </div>
                    </th>
                  </tr>';
                }
              }
              ?>
            </tbody>
          </table>
        </div>
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

  <input type="checkbox" id="remove-cooking-modal" class="modal-toggle" />
  <div class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
      <h3 class="font-bold text-lg">Congratulations random Interner user!</h3>
      <p class="py-4">You've been selected for a chance to get one year of subscription to use Wikipedia for free!</p>
      <div class="modal-action">
        <label for="remove-cooking-modal" class="btn">Yay!</label>
      </div>
    </div>
  </div>

  <!-- SCRIPTS -->
  <script src="scripts/scripts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/index.min.js"></script>
</body>

</html>