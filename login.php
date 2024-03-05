<!DOCTYPE html>
<html lang="en">
<?php
$title = "Commiters - Login";
include('common/head.php');
?>

<body>
  <?php
  require('db_conn.php');
  include('common/nav.php');

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    redirectIfLoggedIn();
  }

  function isValidEmail($email)
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  function isEmpty($value)
  {
    return empty(trim($value));
  }
  class User
  {
    private $db;
    public $uid;

    public function __construct($db)
    {
      $this->db = $db;
    }

    public function isUserRegistered($email)
    {
      $stmt =  $this->db->prepare('SELECT * FROM users WHERE email = :email');
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validateUser($email, $password)
    {
      $stmt =  $this->db->prepare('SELECT * FROM users WHERE email = :email');
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      if (isset($user['user_id']))
        $this->uid = $user['user_id'];
      if ($user !== false && password_verify($password, $user['password'])) {
        $cart_id = $this->getActiveCartID($user['user_id']);
        $_SESSION['cart_id'] = $cart_id;
        return true;
      } else {
        return false;
      }
    }

    private function getActiveCartID($user_id)
    {

      $stmt = $this->db->prepare('SELECT cart_id FROM carts WHERE user_id = :user_id AND active = 1');
      $stmt->bindParam(':user_id', $user_id);
      $stmt->execute();
      $active_cart = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($active_cart !== false) {
        return $active_cart['cart_id'];
      } else {
        $stmt = $this->db->prepare('INSERT INTO carts (user_id) VALUES (:user_id)');
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $this->db->lastInsertId();
      }
    }


    public function registerUser($email, $password)
    {

      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $stmt =  $this->db->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':password', $hashedPassword);
      return $stmt->execute();
    }
  }

  $user = new User($pdo);
  $success_message = "";
  $error_message = "";
  $email = "";
  $password = "";
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['login-email'], $_POST['login-password'])) {
      $email = $_POST['login-email'];
      $password = $_POST['login-password'];

      if (!isEmpty($email) && !isEmpty($password)) {
        if (isValidEmail($email)) {
          if ($user->validateUser($email, $password)) {
            session_regenerate_id();
            $_SESSION['user_id'] = $user->uid;
            header('Location: index.php');
            exit();
          } else {
            $error_message = 'Invalid email or password';
          }
        } else {
          $error_message = 'Invalid email format';
        }
      } else {
        $error_message = 'Email and password are required fields';
      }
    }

    if (isset($_POST['reg-email'], $_POST['reg-password'])) {
      $email = $_POST['reg-email'];
      $password = $_POST['reg-password'];
      if (!isEmpty($email) && !isEmpty($password)) {
        if (isValidEmail($email)) {
          if (!$user->isUserRegistered($email)) {
            if ($user->registerUser($email, $password)) {
              $success_message = "Registeration Successful. Please Login...";
              //exit();
            } else {
              $error_message = 'Error during registration';
            }
          } else {
            $error_message = 'Email already registered';
          }
        } else {
          $error_message = 'Invalid email format';
        }
      } else {
        $error_message = 'Email and password are required fields';
      }
    }
  }

  ?>

  <main class="login">
    <div class="header">
    </div>
    <h3 class="success log_msg"><?= $success_message ?></h3>
    <h3 class="error log_msg"><?= $error_message ?></h3>
    <div class="container">

      <div class="form-container">
        <form method="POST" action="" id="login-form" class="form">
          <h2>Login</h2>
          <label for="login-email">Email</label>
          <input type="text" id="login-email" name="login-email" value="<?= $email ?>">
          <label for="login-password">Password</label>
          <input type="password" id="login-password" name="login-password">
          <button type="submit">Login</button>
        </form>
      </div>

      <div class="form-container registration-form">
        <form method="POST" action="" id="registration-form" class="form">
          <h2>Register</h2>
          <label for="reg-email">Email</label>
          <input type="text" id="reg-email" name="reg-email" value="<?= $email ?>">
          <label for="reg-password">Password</label>
          <input type="password" id="reg-password" name="reg-password">
          <button type="submit">Register</button>
        </form>
      </div>
    </div>
  </main>
</body>

</html>