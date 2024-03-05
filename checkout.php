  <?php
  $title = "Commiters - Checkout";
  include('common/head.php');
  require('db_conn.php');


  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
  </head>

  <body>
    <?php include('common/nav.php');

    class User
    {
      private $pdo;

      public function __construct($pdo)
      {
        $this->pdo = $pdo;
      }

      public function updateUserDetails($user_id, $first_name, $last_name, $address, $city)
      {

        $updateQuery = "UPDATE users 
                            SET first_name = :first_name, 
                                last_name = :last_name, 
                                address = :address, 
                                city = :city
                            WHERE user_id = :user_id";

        $stmt = $this->pdo->prepare($updateQuery);
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':city', $city, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id);

        try {
          $stmt->execute();
          return "User details updated successfully!";
        } catch (PDOException $e) {
          return "Error: " . $e->getMessage();
        }
      }

      public function inactivateCart($user_id, $cart_id)
      {
        $updateCartQuery = "UPDATE carts SET active = 0 WHERE cart_id = :cart_id AND user_id = :user_id";
        $stmtUpdateCart = $this->pdo->prepare($updateCartQuery);
        $stmtUpdateCart->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmtUpdateCart->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtUpdateCart->execute();
      }

      public function createNewCart($user_id)
      {
        $createCartQuery = "INSERT INTO carts (user_id, active) VALUES (:user_id, 1)";
        $stmtCreateCart = $this->pdo->prepare($createCartQuery);
        $stmtCreateCart->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtCreateCart->execute();
        return $this->pdo->lastInsertId();
      }
    }

    $userHandler = new User($pdo);

    $message = "";
    $errors = [];

    $formFields = array(
      'fname' => 'First Name',
      'lname' => 'Last Name',
      'address' => 'Address',
      'city' => 'City',
    );

    if (isset($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];
      if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $errors = [];

        function validateInput($data)
        {
          $data = trim($data);
          $data = stripslashes($data);
          $data = htmlspecialchars($data);
          return $data;
        }

        foreach ($formFields as $key => $label) {
          $value = validateInput($_POST[$key]);
          if (empty($value)) {
            $errors[$key] = ucfirst($label) . " is required";
          }
        }

        if (empty($errors)) {

          $first_name = validateInput($_POST['fname']);
          $last_name = validateInput($_POST['lname']);
          $address = validateInput($_POST['address']);
          $city = validateInput($_POST['city']);

          if (isset($_SESSION["cart_id"])) {
            $cart_id = $_SESSION['cart_id'];
            $userHandler->inactivateCart($user_id, $cart_id);
            $new_cart_id = $userHandler->createNewCart($user_id);

            $message = $userHandler->updateUserDetails($user_id, $first_name, $last_name, $address, $city);

            header("Location: receipt.php?first_name=$first_name&last_name=$last_name&address=$address&city=$city&cart_id=$cart_id");
            exit();
          }
        }
      }
    } else {

      header("Location: login.php");
      exit();
    }


    ?>

    <main>
      <div id="checkout-container">
        <h2>Checkout</h2>
        <?php if (!empty($message)) : ?>
          <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <?php
          foreach ($formFields as $key => $label) {
            echo '<label class="bold checkout" for="' . $key . '">' . $label . '</label>';
            echo '<input type="text" id="' . $key . '" name="' . $key . '">';
            echo '<p class="text-danger mb-3 error">' . (isset($errors[$key]) ? $errors[$key] : '') . '</p>';
          }
          ?>
          <div class="d-flex">
            <button type="submit">Place Order</button>
            <button type="submit" class="disable">Payment</button>
          </div>
        </form>
      </div>
    </main>


    <?php include('common/footer.php'); ?>
  </body>

  </html>