<!--<div class="col-md-offset-4 col-md-4" >-->
<!--      <form id="search-form" method="get" action="search.php">-->
<!--            <div class="input-group">-->
<!--              <input class="form-control" type="text" name="search-query" placeholder="Search">-->
<!--              <span class="input-group-btn">-->
<!--                <button type="submit" name="search-button" class="btn btn-default">Search</button>-->
<!--              </span>-->
<!--            </div>-->
<!--      </form>-->
<!--</div>-->

<!--<div class="col-md-4 text-right">-->
<!--          <a href="shopping_cart.php" class="btn btn-default">-->
<!--            <span class="glyphicon glyphicon-shopping-cart"></span>-->
<!--            <span class="badge">2</span>-->
<!--          </a>-->
<!--          <a href="wishlist.php" class="btn btn-default">-->
<!--            <span class="glyphicon glyphicon-heart"></span>-->
<!--            <span class="badge">5</span>-->
<!--          </a>-->
<!--        </div>-->
<div class="container">
    
      <div class="row">
        <div class="col-md-6">
          <form id="search-form" method="get" action="search.php">
            <div class="input-group">
              <input class="form-control" type="text" name="search-query" placeholder="Search">
              <span class="input-group-btn">
                <button type="submit" name="search-button" class="btn btn-default">Search</button>
              </span>
            </div>
          </form>
        </div>
        <div class="col-md-6 text-right">
          <a href="shopping_cart.php" class="btn btn-default">
            <span class="glyphicon glyphicon-shopping-cart"></span>
            <span class="badge"><?php if($cart_count){ echo $cart_count; } else{ echo "";}?></span>
          </a>
          <a href="wishlist.php" class="btn btn-default">
            <span class="glyphicon glyphicon-heart"></span>
            <span class="badge">5</span>
          </a>
        </div>
      </div>
   
  </div>