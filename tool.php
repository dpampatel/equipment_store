<!DOCTYPE html>
<html lang="en">
<?php
$title = "Commiters - Tool";
include('common/head.php');
?>

<body>
  <?php
  require('db_conn.php');
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  class Tool
  {
    private $pdo;

    public function __construct($pdo)
    {
      $this->pdo = $pdo;
    }

    public function getToolDetails($tool_id)
    {
      $query = "SELECT * FROM tools WHERE tool_id = :tool_id";
      $stmt = $this->pdo->prepare($query);
      $stmt->bindParam(':tool_id', $tool_id, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insertIntoCartItems($details, $quantity)
    {
      $cart_id = $_SESSION["cart_id"];
      $tool_id = $details["tool_id"];

      $stmt = $this->pdo->prepare('SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = :cart_id AND tool_id = :tool_id');
      $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
      $stmt->bindParam(':tool_id', $tool_id, PDO::PARAM_INT);
      $stmt->execute();
      $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($cart_item) {
        $updated_quantity = $cart_item['quantity'] + $quantity;

        $update_stmt = $this->pdo->prepare('UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id');
        $update_stmt->bindParam(':quantity', $updated_quantity, PDO::PARAM_INT);
        $update_stmt->bindParam(':cart_item_id', $cart_item['cart_item_id'], PDO::PARAM_INT);
        return $update_stmt->execute();
      } else {
        $stmt = $this->pdo->prepare('INSERT INTO cart_items (cart_id, tool_id, quantity, price) VALUES (:cart_id, :tool_id, :quantity, :price)');
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':tool_id', $details["tool_id"]);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $details["price"]);

        return $stmt->execute();
      }
    }
  }

  $toolHandler = new Tool($pdo);
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    redirectIfNotLoggedIn();
  }

  $success = "";
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tool_id']) && isset($_POST['quantity'])) {
      $tool_id = $_POST['tool_id'];
      $toolDetails = $toolHandler->getToolDetails($tool_id);
      if ($toolHandler->insertIntoCartItems($toolDetails, $_POST["quantity"]))
        $success = "Item added to Cart.";
    } else {
      header("Location: collection.php");
      exit();
    }
  }

  if (isset($_GET['tool_id'])) {
    $tool_id = $_GET['tool_id'];
    $toolDetails = $toolHandler->getToolDetails($tool_id);
  }
  include('common/nav.php');
  ?>

  <main class="pdp">
    <?php if (isset($toolDetails)) : ?>
      <div class="main_pdp">
        <div class="left">
          <div class="featured_img">
            <img src="imgs/tools/<?= $toolDetails['image_name'] ?>" alt="<?= $toolDetails['tool_name'] ?>">
          </div>
        </div>
        <div class="right">
          <p class="tool_title">
            <?= $toolDetails['tool_name'] ?>
          </p>
          <div class="desc">
            <p class="desc_"><?= $toolDetails['description'] ?></p>
          </div>

          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <p class="price">$<?= $toolDetails['price'] ?></p>
            <div class="qty">
              <input hidden name="tool_id" value="<?= $toolDetails['tool_id'] ?>" />
              <span class="minus" onclick="updateQuantity(-1)">-</span>
              <input type="number" name="quantity" id="quantityInput" value="1" min="1">
              <span class="plus" onclick="updateQuantity(1)">+</span>
            </div>
            <button type="submit" class="add_to_cart">ADD TO CART</button>
            <p class="pdp_success"><?= $success ?></p>
          </form>
        </div>
      </div>
      <div class="tool_desc">
        <!-- <h1 class="heading">TOOL INFORMATION</h1> -->
        <div class="desc">
          <div class="left">
            <h3>Tool Specification</h3>
            <table>
              <tr>
                <th>Specs</th>
                <th>Details</th>
              </tr>
              <tr>
                <td>Brand</td>
                <td><?= $toolDetails['brand'] ?></td>
              </tr>
              <tr>
                <td>Price</td>
                <td>$<?= $toolDetails['price'] ?></td>
              </tr>
              <tr>
                <td>Material</td>
                <td><?= $toolDetails['material'] ?></td>
              </tr>
              <tr>
                <td>Durability</td>
                <td><?= $toolDetails['durability'] ?></td>
              </tr>
              <tr>
                <td>Stock Quantity</td>
                <td><?= $toolDetails['stock_quantity'] ?></td>
              </tr>
            </table>
          </div>

        </div>
      </div>
    <?php else : ?>
      <p>Tool not found.</p>
    <?php endif; ?>

  </main>

  <?php
  include('common/footer.php');
  ?>
</body>

</html>