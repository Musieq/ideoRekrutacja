<?php
require_once 'includes/header.php';

$category = new Category();
?>


<div class="container">
    <div class="row">
        <div class="col-lg-5">
            <h3>Dodaj kategorię</h3>

            <?php
            if (isset($_POST['categoryAdd'])) {
                $categoryName = $_POST['categoryName'];
                $categoryParent = $_POST['categoryParent'];


                $category->addCategory($categoryName, $categoryParent);

            }
            ?>

            <form action="category_add.php" method="post">
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Nazwa kategorii</label>
                    <input type="text" class="form-control" id="categoryName" name="categoryName" minlength="1" maxlength="255" required>
                    <div class="invalid-feedback">
                        Please choose a username.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="categoryParent" class="form-label">Kategoria nadrzędna</label>
                    <select class="form-select" aria-label="Kategorie nadrzędne" id="categoryParent" name="categoryParent" required>
                        <option selected value="0">Wybierz kategorię nadrzędną</option>

                        <?php
                        $category->displayCategoryTreeInSelectField();
                        ?>

                    </select>
                </div>

                <button type="submit" class="btn btn-primary" name="categoryAdd" id="categoryAdd">Submit</button>
            </form>
        </div>

        <div class="col-lg-7">
            <h3>Kategorie</h3>


                <?php
                $category->displayCategoryList();
                ?>


        </div>
    </div>
</div>


<?php
require_once 'includes/footer.php';
?>

