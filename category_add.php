<?php
require_once 'includes/header.php';
?>


<div class="container">
    <div class="row">
        <div class="col-lg-5">
            <h3>Dodaj kategorię</h3>

            <form action="index.php" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Nazwa kategorii</label>
                    <input type="text" class="form-control" id="categoryName" name="categoryName" minlength="1" maxlength="255" required>
                </div>
                <div class="mb-3">
                    <label for="categoryParent" class="form-label">Kategoria nadrzędna</label>
                    <select class="form-select" aria-label="Kategorie nadrzędne" id="categoryParent" required>
                        <option selected value="0">Wybierz kategorię nadrzędną</option>

                        <!-- TODO: list of categories with levels -->

                    </select>
                </div>

                <button type="submit" class="btn btn-primary" name="categoryAdd" id="categoryAdd">Submit</button>
            </form>
        </div>

        <div class="col-lg-7">
            <h3>Kategorie</h3>
        </div>
    </div>
</div>


<?php
require_once 'includes/footer.php';
?>

