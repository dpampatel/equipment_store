<!DOCTYPE html>
<html lang="en">
<?php
$title = "Commiters - Cart";
include('common/head.php');
?>

<body>
  <?php
  require('db_conn.php');
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  class Cart
  {
    private $pdo;

    public function __construct($pdo)
    {
      $this->pdo = $pdo;
    }

    public function getCartItems($user_id)
    {
      $stmt = $this->pdo->prepare("SELECT cart_items.cart_item_id, tools.tool_name, tools.price, cart_items.quantity, tools.image_name
            FROM cart_items 
            INNER JOIN tools ON cart_items.tool_id = tools.tool_id 
            INNER JOIN carts ON cart_items.cart_id = carts.cart_id
            WHERE carts.user_id = :user_id AND carts.active = 1");
      $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantity($cart_item_id, $quantity)
    {
      $stmt = $this->pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id");
      $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
      $stmt->bindParam(':cart_item_id', $cart_item_id, PDO::PARAM_INT);
      return $stmt->execute();
    }

    public function deleteCartItem($cart_item_id)
    {
      $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE cart_item_id = :cart_item_id");
      $stmt->bindParam(':cart_item_id', $cart_item_id, PDO::PARAM_INT);
      return $stmt->execute();
    }
  }

  if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
  }

  $cartManager = new Cart($pdo);
  $user_id = $_SESSION['user_id'];

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update_quantity'])) {
      $cart_item_id = $_POST['cart_item_id'];
      $quantity = $_POST['quantity'];
      $cartManager->updateQuantity($cart_item_id, $quantity);
    } elseif (isset($_POST['delete_item'])) {
      $cart_item_id = $_POST['cart_item_id'];
      $cartManager->deleteCartItem($cart_item_id);
    }
  }

  $cart_items = $cartManager->getCartItems($user_id);
  $subtotal = 0;

  include('common/nav.php');
  ?>

  <main>
    <?php if (empty($cart_items)) : ?>
      <div class="header cart_header">
        <h1>Your cart is empty.</h1>
      </div>
    <?php else : ?>
      <div class="header">
        <h1>YOUR CART</h1>
      </div>

      <table>
        <thead>
          <tr>
            <th>Tool</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cart_items as $item) : ?>
            <tr>
              <td class="cart-item">
                <img class="tool-image" src="imgs/tools/<?= $item["image_name"] ?>" alt="Tool 1" />
                <div class="tool-details">
                  <p><?= $item['tool_name'] ?></p>
                  <p>$<?= $item['price'] ?></p>
                </div>
              </td>
              <td>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?>">
                  <div class="qty cart-item__quantity-wrapper">
                    <div class="main_qty">
                      <span class="minus" onclick="this.parentNode.querySelector('input[type=number]').stepDown();">-</span>
                      <input value="<?= $item['quantity'] ?>" min="1" type="number" name="quantity" class="quantity__input" aria-label="Quantity">
                      <span class="plus" onclick="this.parentNode.querySelector('input[type=number]').stepUp();">+</span>
                    </div>
                    <input class="cart_btn" type="submit" name="update_quantity" value="Update">
                  </div>
                </form>
              </td>
              <td>
                $<?= number_format($item['price'] * $item['quantity'], 2) ?>
              </td>
              <td>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?>">
                  <input class="cart_btn" type="submit" name="delete_item" value="Delete">
                </form>
              </td>
            </tr>
            <?php $subtotal += ($item['price'] * $item['quantity']); ?>
          <?php endforeach; ?>
          <tr>
            <td></td>
            <td></td>
            <td>Subtotal: <span class="subtotal">$<?= number_format($subtotal, 2) ?></span></td>
            <td></td>
          </tr>
        </tbody>
      </table>

      <div class="cart_checkout">
        <a href="checkout.php" class="checkoutbtn">
          CHECKOUT
        </a>
      </div>
    <?php endif; ?>
  </main>
  <?php
  include('common/footer.php');
  ?>
</body>

</html>