<?php


class Category {

    private int $ID;
    private string $name;
    private int $parentID;
    public array $errors = [];


    public function __construct($name, $parentID) {
        $this->name = $name;
        $this->parentID = $parentID;
    }

    public function getAllCategories() {

    }


    public function displayCategoryTree() {

    }


    public function addCategory() {

    }


    public function validateData() {
        if (strlen($this->name) < 1) {
            $this->errors += ['Nazwa kategorii nie może być pusta.'];
        } elseif (strlen($this->name) > 255) {
            $this->errors += ['Nazwa kategorii jest dłuższa niż 255 znaków.'];
        }

        if (empty($this->errors)) {
            return true;
        } else {
            return false;
        }
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


    private function categoryExist() {


    }
}