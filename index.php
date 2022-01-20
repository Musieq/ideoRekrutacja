<?php
require_once 'includes/header.php';

$category = new Category();

?>


<div class="container">
    <div class="row">
        <div class="col-lg-5">
            <h3>Dodaj kategorię</h3>


            <?php
            /** Delete category **/
            if (isset($_GET['delete']) || isset($_GET['id'])) {
                $category->deleteCategory($_GET['id']);
            }

            /** Add category **/
            if (isset($_POST['categoryAdd'])) {
                $categoryName = $_POST['categoryName'];
                $categoryParent = $_POST['categoryParent'];


                $category->addCategory($categoryName, $categoryParent);

            }
            ?>

            <form action="index.php" method="post">
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
                $category->displayCategoryList(true);
                ?>


        </div>
    </div>
</div>



<!-- Modal - delete category -->
<div class="modal fade" id="modalDeleteCategory" tabindex="-1" aria-labelledby="modalDeleteCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteCategoryLabel">Usuń kategorię</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Na pewno chcesz usunąć tę kategorię? Jeśli kategoria posiada podkategorie, zostaną one przypisane do kategorii nadrzędnej.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                <button type="button" class="btn btn-danger" id="deleteCategoryConfirm">Usuń kategorię</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() { deleteAndShowModal('categoryRemove', 'deleteCategoryConfirm') };
</script>

<?php
require_once 'includes/footer.php';
?>

