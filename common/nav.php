<nav>
  <?php
  $cartCount = 0;
  if (isset($pdo)) {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    class CartManager
    {
      private $pdo;

      public function __construct($pdo)
      {
        $this->pdo = $pdo;
      }

      public function getCartItemCount($cart_id)
      {
        $stmt = $this->pdo->prepare("SELECT SUM(quantity) AS total_quantity FROM cart_items WHERE cart_id = :cart_id");
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result && isset($result['total_quantity'])) ? (int)$result['total_quantity'] : 0;
      }
      public function getActiveCartId($user_id)
      {
        $stmt = $this->pdo->prepare("SELECT cart_id FROM carts WHERE user_id = :user_id AND active = 1 ORDER BY created_at DESC LIMIT 1");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result && isset($result['cart_id'])) ? (int)$result['cart_id'] : null;
      }
    }
    $cartManager = new CartManager($pdo);

    if (isset($_SESSION["user_id"])) {
      $userCartId = isset($_SESSION["cart_id"]) ? $_SESSION["cart_id"] : null;
      $activeCartId = $cartManager->getActiveCartId($_SESSION["user_id"]);

      if ($userCartId !== $activeCartId && $activeCartId !== null) {
        $_SESSION["cart_id"] = $activeCartId;
        $userCartId = $activeCartId;
      }

      if ($userCartId !== null) {
        $cartCount = $cartManager->getCartItemCount($userCartId);
      }
    }
  }
  ?>
  <span class="logo">
    <a href="index.php"><span><b class="active">COMMITTERS</b></span></a>
  </span>
  <div class="hamburger">
    <div class="line"></div>
    <div class="line"></div>
    <div class="line"></div>
  </div>
  <ul class="navigationList">
    <li><a href="index.php"> HOME</a></li>
    <li><a href="collection.php">TOOL LIST</a></li>
    <?php if (isset($_SESSION["user_id"]) && $_SESSION["user_id"]) { ?>
      <li><a href="logout.php">LOGOUT</a></li>
    <?php } else { ?>
      <li><a href="login.php">LOGIN</a></li>
    <?php } ?>
    <li>
      <a href="cart.php" class="cart">
        <?php if ($cartCount > 0) { ?>
          <span class="cartcount"><?= $cartCount ?></span>
        <?php } ?>
        <img src="imgs/shopping-bag.png" alt="" class="cartimg">
      </a>
    </li>
  </ul>
</nav>