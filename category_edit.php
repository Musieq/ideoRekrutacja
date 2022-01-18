<?php
require_once 'includes/header.php';

if (!isset($_GET['id']) || !isset($_GET['name']) || !isset($_GET['parent_id'])) {
    header('Location: category_add.php');
    exit;
}

$category = new Category();
?>


<div class="container">
    <div class="row">
        <div class="col-lg-5">
            <h3>Edytuj kategorię</h3>

            <?php
            if (isset($_POST['categoryEdit'])) {
                $categoryName = $_POST['categoryName'];
                $categoryParent = $_POST['categoryParent'];


            }
            ?>

            <form action="category_edit.php" method="post">
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Nazwa kategorii</label>
                    <input type="text" class="form-control" value="<?=$_GET['name']?>" id="categoryName" name="categoryName" minlength="1" maxlength="255" required>
                    <div class="invalid-feedback">
                        Please choose a username.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="categoryParent" class="form-label">Kategoria nadrzędna</label>
                    <select class="form-select" aria-label="Kategorie nadrzędne" id="categoryParent" name="categoryParent" required>
                        <option selected value="0">Wybierz kategorię nadrzędną</option>

                        <?php
                        $category->displayCategoryTreeInSelectField($_GET['parent_id']);
                        ?>

                    </select>
                </div>
                <div class="mb-3">
                    <input type="hidden" name="categoryID" value="<?=$_GET['id']?>">
                </div>
                <button type="submit" class="btn btn-primary" name="categoryEdit" id="categoryEdit">Submit</button>
            </form>
        </div>
    </div>
</div>


<?php
require_once 'includes/footer.php';
?>

