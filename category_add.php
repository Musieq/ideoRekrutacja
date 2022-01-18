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

                // Validate data
                if ($category->validateData($categoryName, $categoryParent)) {
                    // Add category
                    $category->addCategory();
                } else {
                    // Display errors
                    $category->displayErrorMsg();
                }
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
                //$category->displayCategoryTree();
                $category->displayCategoryList();
                ?>


<!--            <ul>
                <li data-bs-toggle="collapse" data-bs-target="#test">
                    Coś tam
                    <ul class="collapse" id="test">
                        <li data-bs-toggle="collapse" data-bs-target="#test2">TEST2
                            <ul class="collapse" id="test2">
                                <li>TEST333</li>
                            </ul>
                        </li>
                    </ul>

                </li>
            </ul>-->
        </div>
    </div>
</div>


<?php
require_once 'includes/footer.php';
?>

