<?php
require_once 'includes/header.php';

$category = new Category();

if (isset($_POST['categoryReorder'])) {
    $catOrder = $_POST['categoryOrder'];

    $category->changeCategoryOrder($catOrder);
}
?>


<div class="container">
    <div class="row">


        <div class="col-lg-7">
            <h3>Zmień kolejność kategorii (przenieś i upuść)</h3>


            <?php
            $category->displayCategoryList(false);
            ?>
            <form action="category_reorder.php" method="post">
                <input type="hidden" name="categoryOrder" id="categoryOrder">
                <button type="submit" class="btn btn-primary" name="categoryReorder" id="categoryReorder">Submit</button>
            </form>

        </div>
    </div>
</div>





<?php
require_once 'includes/footer.php';
?>

