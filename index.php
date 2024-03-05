<!DOCTYPE html>
<html lang="en">
<?php
$title = "Commiters";
include('common/head.php');
?>

<body>
  <?php
  require('db_conn.php');
  session_start();
  include('common/nav.php');
  ?>
  <header class="index">
    <img src="imgs/banner.png" alt="Homepage Banner" class="banner" />
  </header>
  <main>
    <section class="cls-home-links">
      <form method="POST" action="dbinit.php">
        <input type="submit" value="Initialize Database">
        <?php
        if (isset($_GET["init"])) {
          echo "Database Initialized";
        } ?>
      </form>
    </section>
    <section class="img_txt">
      <div class="main_content">
        <div class="content">
          <p class="about_txt">
            Over the past 49 years, Committers has grown from a small-town fastener shop to a thriving regional business, becoming a significant player in the distribution of OEM, MRO, and construction products worldwide. Along the way, we've maintained a steadfast commitment to exceptional service, understanding that proximity to our customers is key. Today, Committers operates nearly 2,700 branches across all 50 states and more than 20 nations.
          </p>
          <p class="about_txt">
            You'll find Committers branches in cities like Houston, Indianapolis, Atlanta, Toronto, and Shanghai. What truly matters is that you'll discover a Committers branch near you prepared to stock your products, manage your inventory, respond urgently, and deliver the unparalleled customer service that goes beyond what you find in a catalog or through a 1-800 number.
          </p>
          <p class="about_txt">
            From our humble beginnings to a network of nearly 2,700 branches and counting. We Are Where You Are.
          </p>
        </div>
        <img src="imgs/IMG1.png" alt="about us" class="about" />
      </div>
    </section>
    <section class="review">
      <h2 class="heading card_heading">CUSTOMER REVIEWS</h2>
      <div class="cards reviews_card">
        <div class="card">
          <img src="imgs/user1.jpg" alt="customer1 image" />
          <p class="review">
            Committers' impressive growth from a local shop to a global distributor is evident in their extensive branch network. The proximity to customers is a game-changer, providing quick responses, efficient inventory management, and exceptional service.
          </p>
          <p class="reviewer">CHAVEZ PROCOPE</p>
        </div>
        <div class="card">
          <img src="imgs/user2.jpg" alt="customer2 image" />
          <p class="review">
            Committers' commitment to customer satisfaction shines through. With branches spanning major cities globally, they're always ready to meet my needs. The personalized service sets them apart from the standard 1-800 number experience. Highly recommended.
          </p>
          <p class="reviewer">JOHN S</p>
        </div>
      </div>
    </section>

  </main>
  <?php
  include('common/footer.php');
  ?>

</body>

</html>