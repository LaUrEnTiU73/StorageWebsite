<?php
require_once('config/db.php');

if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
  $pageNumber = abs($_GET['page']);
else
  $pageNumber = 1;


if (isset($_GET['search']))
  $searchMode = $_GET['search'];
else
  $searchMode = "";


if (isset($_GET['filter']))
  $filterMode = $_GET['filter'];
else
  $filterMode = "allFilter";


if (isset($_GET['sort']))
  $orderMode = $_GET['sort'];
else
  $orderMode = "nameASC";


$nextNameSort = 'nameDESC';
$nextPriceSort = 'priceDESC';

$parametri = [];

$searchCondition = "SELECT * FROM products WHERE 1=1 ";

if ($searchMode != "") {
  $searchCondition = $searchCondition . "AND Name LIKE :searchMode ";
  $parametri[':searchMode'] = '%' . $searchMode . '%';
}

if ($filterMode == 'allFilter')
  $classAll = "products-toolbar__btn-filter-on";
else
  $classAll = "products-toolbar__btn-filter-off";

if ($filterMode == 'stockFilter')
  $classStock = "products-toolbar__btn-filter-on";
else
  $classStock = "products-toolbar__btn-filter-off";

if ($orderMode == 'nameASC' || $orderMode == 'nameDESC')
  $className = "products-toolbar__btn-sort-on";
else
  $className = "products-toolbar__btn-sort-off";

if ($orderMode == 'priceASC' || $orderMode == 'priceDESC')
  $classPrice = "products-toolbar__btn-sort-on";
else
  $classPrice = "products-toolbar__btn-sort-off";

switch ($filterMode) {
  case 'stockFilter':
    $filterby = " AND InStock = 1";
    break;
  default:
    $filterby = "";
    break;
}

switch ($orderMode) {
  case 'nameDESC':
    $orderby = " ORDER BY Name DESC";
    $nextNameSort = 'nameASC';
    break;
  case 'priceASC':
    $orderby = " ORDER BY Price ASC";
    $nextPriceSort = 'priceDESC';
    break;
  case 'priceDESC':
    $orderby = " ORDER BY Price DESC";
    $nextPriceSort = 'priceASC';
    break;
  default:
    $orderby = " ORDER BY Name ASC";
    break;
}


$total_product_query = "SELECT COUNT(*) FROM products ";
$result_total_products = $pdo->query($total_product_query)->fetchColumn();


$total_product_filtered_query = $searchCondition . $filterby;
$resultFiltered = $pdo->prepare($total_product_filtered_query);
$resultFiltered->execute($parametri);
$countFiltered = $resultFiltered->rowCount();


$limit = 10;
$totalPageNumber = ceil($countFiltered / $limit);
if ($totalPageNumber < 1)
  $totalPageNumber = 1;
if ($pageNumber >= $totalPageNumber)
  $pageNumber = $totalPageNumber;
$offset = ($pageNumber - 1) * $limit;


$limit_product_query = " LIMIT $limit OFFSET $offset";
$searchCondition = $searchCondition . $filterby . $orderby . $limit_product_query;
$result = $pdo->prepare($searchCondition);
$result->execute($parametri);


$stock_query = "SELECT COUNT(*) FROM products WHERE InStock=1";
$StockNumber = $pdo->query($stock_query)->fetchColumn();
if ($filterMode == 'stockFilter')
  $totalProductValue = "of " . $StockNumber . " (Stock)";
else
  $totalProductValue = "of " . $result_total_products . " (Total)";


?>
<!doctype html>
<html>

<body>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Storage</title>

    <link rel="stylesheet" href="assets/css/crudModal.css" />
    <link rel="stylesheet" href="assets/css/productsTable.css" />
    <link rel="stylesheet" href="assets/css/productsToolbar.css" />
    <link rel="stylesheet" href="assets/css/statusModal.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/variables.css" />
  </head>

  <header>
    <div class="center-container-header">
      <div id="header-website">
        <div class="header-website__logo">Storage</div>
      </div>
      <button type="button" id="btn-add-product" data-action='add' onclick="crudModal(this)">
        Add Product
      </button>
    </div>
  </header>

  <main>
    <div class="center-container-main">
      <?php if (isset($_GET['status'])): ?>
        <script>
          function showError() {
            <?php if ($_GET['status'] === 'succesUpdate'): ?>
              openStatusModal("Product updated successfully", "Succes");

            <?php elseif ($_GET['status'] === 'succesAdd'): ?>
              openStatusModal("Product added successfully", "Succes");

            <?php elseif ($_GET['status'] === 'succesDeleted'): ?>
              openStatusModal("Product deleted successfully", "Succes");

            <?php elseif ($_GET['status'] === 'errorAdd'): ?>
              openStatusModal("", "Add error");

            <?php elseif ($_GET['status'] === 'errorEdit'): ?>
              openStatusModal("", "Edit error");

            <?php endif; ?>
            const indexpath = window.location.pathname;
            window.history.replaceState({}, document.title, indexpath);
          }
          document.addEventListener('DOMContentLoaded', showError);
        </script>
      <?php endif; ?>



      <div id="products-toolbar">

        <form action="index.php" method="GET" class="products-toolbar__search-form">
          <input type="text" id="products-toolbar__search-box" name="search" value="<?php echo $searchMode; ?>"
            placeholder="Search the product..." />
          <button type="submit" class="products-toolbar__btn-search">Cauta</button>
        </form>

        <div id="products-toolbar__filter-box">

          <div id="products-toolbar__filter-buttons">

            <a href="index.php?<?php echo "sort=$orderMode"; ?>&filter=<?php echo "allFilter"; ?>&search=<?php echo $searchMode; ?>"
              class="<?php echo $classAll; ?>">
              ALL
            </a>

            <a href="index.php?<?php echo "sort=$orderMode"; ?>&filter=<?php echo "stockFilter"; ?>&search=<?php echo $searchMode; ?>"
              class="<?php echo $classStock; ?>">
              STOCK
            </a>

          </div>

          <div id="products-toolbar__sort-buttons">

            <a href="index.php?sort=<?php echo $nextNameSort; ?>&filter=<?php echo $filterMode; ?>&search=<?php echo $searchMode; ?>"
              class="<?php echo $className; ?>">
              Name
              <?php if ($orderMode == "nameDESC")
                echo "<span style='color:var(--second-color);padding-left:5px;'>&#8595</span>";
              elseif ($orderMode == "nameASC")
                echo "<span style='color:var(--second-color);padding-left:5px;'>&#8593</span>";
              else
                echo "<span style='color:var(--second-color);padding-left:5px;'>&#8597</span>";
              ?>
            </a>

            <a href="index.php?sort=<?php echo $nextPriceSort; ?>&filter=<?php echo $filterMode; ?>&search=<?php echo $searchMode; ?>"
              class="<?php echo $classPrice; ?>">
              Price
              <?php
              if ($orderMode == "priceDESC")
                echo "<span style='color:var(--second-color);padding-left:5px;'>&#8595</span>";
              elseif ($orderMode == "priceASC")
                echo "<span style='color:var(--second-color);padding-left:5px;'>&#8593</span>";
              else
                echo "<span style='color:var(--second-color); padding-left:5px;'>&#8597</span>";
              ?>
            </a>
          </div>
        </div>
      </div>
      <?php $productsNumber = $result->rowCount() ?>

      <div id="filter-sort-result-count">
        <?php echo $productsNumber; ?>
        <?php if ($productsNumber > 1)
          echo "products $totalProductValue";
        else
          echo "product $totalProductValue";
        ?>
      </div>


      <table id="products-table">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Avaibility</th>
            <th>InStock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->rowCount() > 0) {
            while ($product = $result->fetch(PDO::FETCH_ASSOC)) {
              ?>
              <tr>
                <td class="col-img"><img class="products-table__image"
                    src="assets/uploads/<?= htmlspecialchars($product['Image']); ?>"></td>
                <td class="col-name"> <?= htmlspecialchars($product['Name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="col-price"> $ <?= htmlspecialchars($product['Price'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="col-description"> <?= htmlspecialchars($product['Description'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="col-data"> <?= htmlspecialchars($product['Avaibility']); ?></td>
                <td class="col-stock"> <?php if ($product['InStock'])
                  echo "Is in stock";
                else
                  echo "Is NOT in stock"; ?></td>

                <td class="col-buttons">
                  <div class="products-table__buttons">
                    <button class=" products-table__btn-action" id="products-table__btnDelete" type="button"
                      data-id="<?= $product['Id']; ?>"
                      data-name='<?= htmlspecialchars($product['Name'], ENT_QUOTES, 'UTF-8'); ?>'
                      data-price='<?= htmlspecialchars($product['Price'], ENT_QUOTES, 'UTF-8'); ?>'
                      data-description='<?= htmlspecialchars($product['Description'], ENT_QUOTES, 'UTF-8'); ?>'
                      data-avaibility='<?= $product['Avaibility']; ?>' data-stock=<?= $product['InStock']; ?>
                      data-image='<?= $product['Image']; ?>' data-action='delete' onclick="crudModal(this)">Delete</button>

                    <button class="products-table__btn-action" id="products-table__btnEdit" type="button"
                      data-id="<?= $product['Id']; ?>"
                      data-name='<?= htmlspecialchars($product['Name'], ENT_QUOTES, 'UTF-8'); ?>'
                      data-price='<?= htmlspecialchars($product['Price'], ENT_QUOTES, 'UTF-8'); ?>'
                      data-description='<?= htmlspecialchars($product['Description'], ENT_QUOTES, 'UTF-8'); ?>'
                      data-avaibility='<?= $product['Avaibility']; ?>' data-stock=<?= $product['InStock']; ?>
                      data-image='<?= $product['Image']; ?>' data-action='edit' onclick="crudModal(this)"> Edit
                    </button>
                  </div>
                </td>

              </tr>
              <?php
            }
          } else {
            echo "Database is empty";
          }
          ?>
        </tbody>
      </table>
      <div id="pagination">

        <?php if ($pageNumber > 1): ?>
          <a href="index.php?<?php echo "sort=$orderMode"; ?>&filter=<?php echo "allFilter"; ?>&search=<?php echo $searchMode; ?>&page=<?php echo $pageNumber - 1; ?>"
            class="pagination__btn-nav">
            &#8678
          </a>
        <?php endif; ?>

        <?php
        if ($totalPageNumber - $pageNumber >= 3):
          for ($i = $pageNumber; $i <= $pageNumber + 3; $i++): ?>
            <a href="index.php?<?php echo "sort=$orderMode"; ?>&filter=<?php echo $filterMode; ?>&search=<?php echo $searchMode; ?>&page=<?php echo $i; ?>"
              class="pagination__btn-nav">
              <?php if ($i == $pageNumber)
                echo "<span style='color:var(--second-color);'>$i</span>";
              else
                echo $i; ?>
            </a>
          <?php endfor; ?>


        <?php elseif ($totalPageNumber <= 4):
          for ($i = 1; $i <= $totalPageNumber; $i++): ?>
            <a href="index.php?<?php echo "sort=$orderMode"; ?>&filter=<?php echo $filterMode; ?>&search=<?php echo $searchMode; ?>&page=<?php echo $i; ?>"
              class="pagination__btn-nav">
              <?php if ($i == $pageNumber)
                echo "<span style='color:var(--second-color);'>$i</span>";
              else
                echo $i; ?>
            </a>
          <?php endfor; ?>



        <?php else:
          for (
            $i = max($totalPageNumber - 3, $totalPageNumber - $pageNumber);
            $i <= $totalPageNumber;
            $i++
          ): ?>

            <a href="index.php?
            <?php echo "sort=$orderMode"; ?>&filter=<?php echo $filterMode; ?>&search=<?php echo $searchMode; ?>&page=<?php echo $i; ?>"
              class="pagination__btn-nav">
              <?php if ($i == $pageNumber)
                echo "<span style='color:var(--second-color);'>$i</span>";
              else
                echo $i; ?>
            </a>
          <?php endfor; ?>
        <?php endif ?>


        <?php if ($pageNumber < $totalPageNumber): ?>

          <a href="index.php?<?php echo "sort=$orderMode"; ?>&filter=<?php echo $filterMode; ?>&search=<?php echo $searchMode; ?>&page=<?php echo $pageNumber + 1; ?>"
            class="pagination__btn-nav">
            &#8680
          </a>

        <?php endif; ?>
      </div>
    </div>
  </main>

  <footer>
    <div class="center-container-footer">
      <div class="footer-col">
        <div id="header-website">
          <div class="header-website__logo">Storage</div>
        </div>
        <p>Designed & developed with care.</p>
      </div>


      <div class="footer-bottom">
        <p>&copy; 2026 Storage. All rights reserved.</p>
        <a>|</a>
        <a href="index.php">Privacy Policy</a>
        <a>|</a>
        <a href="index.php">Terms of Service</a>
      </div>
    </div>
  </footer>

  <div id="status-modal__overlay">
    <div id="status-modal__container">
      <div id="status-modal__header">
        <span id="status-modal__error-type"></span>
        <button onclick="closeStatusModal()">X</button>
      </div>
      <div id="status-modal__message"> </div>
      <div id="status-modal__general-error">
        <span>Action failed. Please check the entered fields:</span>
        <ul>
          <li>All fields (*) required.</li>
          <li>Name (3-50 chars)</li>
          <li>Price (numbers only)</li>
          <li>Description (3-2000 chars)</li>
          <li>Date (valid date format)</li>
          <li>Image (JPG or PNG only)</li>
        </ul>

      </div>
    </div>
  </div>

  <dialog id="crud-modal-overlay">
    <div id="crud-modal">
      <div id="crud-modal__header">
        <h2 id="crud-modal__title">Modal Title</h2>
        <button id="crud-modal__btn-close" onclick="closeAddEditModal()">X</button>
      </div>

      <form id="crud-modal-form" action="" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="ProductId" id="crud-modal__product-id" value="-1" />

        <div class="crud-modal__input-block" id="crud-modal__input-name">
          <label for="crud-modal__product-name">Product Name <span style="color:var(--delete-color);">*</span></label>
          <input type="text" name="ProductName" id="crud-modal__product-name" placeholder="ex. Jeans" />
        </div>

        <div class="crud-modal__input-block" id="crud-modal__input-price">
          <label for="crud-modal__product-price">
            Product Price ($) <span style="color:var(--delete-color);">*</span>
          </label>
          <input type="text" inputmode="decimal" name="ProductPrice" id="crud-modal__product-price"
            placeholder="0.00" />
        </div>

        <div class="crud-modal__input-block" id="crud-modal__input-description">
          <label for="crud-modal__product-description">
            Product Description <span style="color:var(--delete-color);">*</span>
          </label>
          <textarea type="text" name="ProductDescription" id="crud-modal__product-description"
            placeholder="Short product description..."></textarea>
        </div>

        <div class="crud-modal__input-block" id="crud-modal__input-stock">
          <label for="crud-modal__product-stock">Is in stock?</label>
          <span id="stock-answer"></span>

          <label for="crud-modal__product-stock" class="switch"><input type="checkbox" name="ProductStock"
              id="crud-modal__product-stock" />
            <div class="slider"></div>
          </label>
        </div>

        <div class="crud-modal__input-block" id="crud-modal__input-data">
          <label for="crud-modal__product-date">Product Date<span style="color:var(--delete-color);"> * </span>:
          </label>
          <input type="date" id="crud-modal__product-date" name="ProductAvaibility" />
        </div>

        <div class="crud-modal__input-block" id="crud-modal__input-image">
          <label>Product Image <span style="color:var(--delete-color);">*</span></label>
          <div id="crud-modal__image-space">
            <input type="file" name="ProductImage" id="crud-modal__product-image" />
            <label for="crud-modal__product-image" id="crud-modal__btn-product-image"> Choose File</label>
            <div id="crud-modal__image-preview">
              <input type="hidden" id="crud-modal__curent-image-name" name="OldProductImage" value="">
              <img id="crud-modal__image-current" src="" value="">
            </div>
          </div>
        </div>

        <div id="crud-modal__errorBlock">
          <span>Errors</span>
          <div id="crud-modal__errorList">
            <ul></ul>
          </div>
        </div>

        <div class="crud-modal__footer">
          <button type="button" id="crud-modal__btn-cancel" onclick="closeAddEditModal()">Cancel</button>
          <button type="submit" id="crud-modal__btn-submit">Button Name</button>
        </div>

      </form>
    </div>
  </dialog>

  <script src="assets/js/main.js"></script>

</body>

</html>