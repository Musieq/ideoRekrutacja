<?php
require_once 'includes/header.php';

$category = new Category();

?>


<div class="container">
    <div class="row">


        <div class="col-lg-7">
            <h3>Kategorie</h3>


            <?php
            $category->displayCategoryList(false);
            ?>


        </div>
    </div>
</div>





<?php
require_once 'includes/footer.php';
?>

