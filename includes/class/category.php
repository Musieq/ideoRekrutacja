<?php


class Category {

    private int $ID;
    private string $name;
    private int $parentID;
    private array $errors = [];
    private array $sortedTreeArr;


    public function getAllCategories() {
        global $db;

        return $db->query("SELECT * FROM tree")->fetchAll();
    }


    public function displayCategoryTreeInSelectField($parentID = 0, $hierarchy = '') {
        global $db;

        $categories = $db->query("SELECT id, name FROM tree WHERE parent_id = $parentID")->fetchAll();
        foreach ($categories as $values) {
            echo "<option value='{$values['id']}'>{$hierarchy} {$values['name']}</option>";

            $this->displayCategoryTreeInSelectField($values['id'], $hierarchy . '—');
        }
    }


    public function displayCategoryList() {
        global $db;

        $this->makeListRecursive($db->query("SELECT * FROM tree")->fetchAll());

        $this->printListRecursive($this->sortedTreeArr);
    }

    private function makeListRecursive($list, $parentID = 0) {
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['parent_id'] == $parentID) {
                $this->makeListRecursive($list, $list[$i]['id']);
                $this->sortedTreeArr[] = $list[$i];
            }
        }
    }

    private function printListRecursive($list, $parentID = 0) {
        $foundSome = false;
        for ($i = 0, $c = count($list); $i < $c; $i++) {
            if ($list[$i]['parent_id'] == $parentID) {
                if ($foundSome == false) {
                    echo '<ul>';
                    $foundSome = true;
                }
                echo '<li>' . $list[$i]['name'] . '</li>';
                $this->printListRecursive($list, $list[$i]['id']);
            }
        }
        if ($foundSome) {
            echo '</ul>';
        }
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
        // Check if category name is string and it's length
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


        // Return true if no errors and assign values to variables
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