<?php
require_once 'includes/header.php';
?>


<div class="container">
    <div class="row">
        <div class="col-lg-5">
            <h3>Dodaj kategorię</h3>

            <form action="index.php" method="post">
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Nazwa kategorii</label>
                    <input type="text" class="form-control" id="categoryName" name="categoryName">
                </div>
                <div class="mb-3">
                    <label for="categoryParent" class="form-label">Kategoria nadrzędna</label>
                    <select class="form-select" aria-label="idk" id="categoryParent">
                        <option selected value="0">Wybierz kategorię nadrzędną</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
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

