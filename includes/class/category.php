<?php


class Category {

    private int $ID;
    private string $name;
    private int $parentID;
    private array $errors = [];



    public function getAllCategories() {

    }


    public function displayCategoryTree() {

    }


    public function addCategory() {
        global $db;

        $db->query("INSERT INTO tree(name, parent_id) VALUES (?, ?)", $this->name, $this->parentID);
    }


    public function displayErrorMsg() {
        ?>
        <div class="alert alert-danger" role="alert">
            <?php
            foreach ($this->errors as $msg) {
                echo "<p class='mb-0'>" . $msg . "</p>";
            }
            ?>
        </div>
        <?php
    }


    public function validateData($name, $parentID): bool {
        // Check length of category name
        if (is_string($name)) {
            if (strlen($name) < 1) {
                $this->errors += ['Nazwa kategorii nie może być pusta.'];
            } elseif (strlen($name) > 255) {
                $this->errors += ['Nazwa kategorii jest dłuższa niż 255 znaków.'];
            }
        } else {
            $this->errors += ['Nazwa kategorii nie jest tekstem.'];
        }


        // Check if parent ID is numeric and if it exists (if different than 0)
        if (is_numeric($parentID)) {
            if ($parentID != 0) {
                if (!$this->categoryExist($parentID)) {
                    $this->errors += ['Kategoria nadrzędna o podanym ID nie istnieje.'];
                }
            }
        } else {
            $this->errors += ['Błędne ID kategorii nadrzędnej.'];
        }


        // Return true if no errors
        if (empty($this->errors)) {
            $this->name = $name;
            $this->parentID = $parentID;
            return true;
        } else {
            return false;
        }
    }


    private function categoryExist($parentID): bool {
        global $db;

        $numRows = $db->query("SELECT id FROM tree WHERE id = ?", $parentID)->numRows();

        if ($numRows == 1) {
            return true;
        } else {
            return false;
        }
    }
}