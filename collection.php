<!DOCTYPE html>
<html lang="en">
<?php
$title = "Commiters - Collection";
include('common/head.php');
?>

<body>
  <?php
  require('db_conn.php');

  class Collection
  {
    private $pdo;

    public function __construct($pdo)
    {
      $this->pdo = $pdo;
    }

    public function getCategories()
    {
      $query = 'SELECT * FROM categories';
      $stmt = $this->pdo->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTools($categoryFilter, $priceFilter)
    {
      $query = 'SELECT * FROM tools WHERE 1 ';

      if (!empty($categoryFilter)) {
        $placeholders = implode(',', array_fill(0, count($categoryFilter), '?'));
        $query .= "AND category_id IN ({$placeholders})";
      }

      $stmt = $this->pdo->prepare($query);

      if (!empty($categoryFilter)) {
        foreach ($categoryFilter as $key => $value) {
          $stmt->bindValue(($key + 1), $value, PDO::PARAM_INT);
        }
      }

      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  }

  $collection = new Collection($pdo);
  $categoryFilter = isset($_POST['category_id']) ? $_POST['category_id'] : [];
  $priceFilter = isset($_POST['price']) ? $_POST['price'] : [];
  $tools = $collection->getTools($categoryFilter, $priceFilter);
  $categories = $collection->getCategories();

  include('common/nav.php');
  ?>
  <header class="collection">
  </header>
  <main>
    <div class="wrapper">
      <aside>
        <p class="main_title">Filterd By</p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <div class="list">
            <p class="title">Category</p>
            <?php foreach ($categories as $category) { ?>
              <?php
              $checked = (in_array($category['category_id'], $categoryFilter)) ? 'checked' : '';
              ?>
              <li><input type="checkbox" name="category_id[]" id="<?= $category['category_id'] . '_id' ?>" value="<?= $category['category_id'] ?>" <?= $checked ?> /> <label for="<?= $category['category_id'] . '_id' ?>"><?= $category['category_name'] ?></label></li>
            <?php } ?>
          </div>

          <!-- <div class="list">
            <p class="title">Price</p>
            <ul>
              <li>
                <input type="radio" name="price" value="Under $5" id="under_5" />
                <label for="under_5">Under $5</label>
              </li>
              <li>
                <input type="radio" name="price" value="$5 to $10" id="5_to_10" />
                <label for="5_to_10">$5 to $10</label>
              </li>
              <li>
                <input type="radio" name="price" value="$10 to $20" id="10_to_20" />
                <label for="10_to_20">$10 to $20</label>
              </li>
            </ul>
          </div> -->

          <input class="apply_filter" type="submit" value="Apply Filters">
          <div ><a class="clear_filter" href="collection.php"> Clear Filter</a></div>

        </form>
      </aside>
      <div class="collection_list">
        <?php foreach ($tools as $tool) { ?>
          <div class="item">
            <a href="tool.php?tool_id=<?= $tool['tool_id'] ?>" class="card">
              <div class="up">
                <img src="imgs/tools/<?= $tool['image_name'] ?>" alt="<?= $tool['tool_name'] . ' Picture' ?>" />
                <div class="overlay">
                  <div class="bottom">
                    <div class="square">
                      <img src="imgs/tools/explore.png" alt="explore" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="down">

                <div class="desc">
                  <p class="hover">
                    <?= $tool['tool_name'] ?>
                  </p>
                  <div class="add flex-aic-jcc">
                    <p>View</p>
                  </div>
                </div>
                <div class="price">
                  <p>$ <?= $tool['price'] ?></p>
                  <br>
                </div>
              </div>
              <img src="imgs/tools/sale.png" alt="sale" class="sale" />
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  </main>
  <?php
  include('common/footer.php');
  ?>
</body>

</html>