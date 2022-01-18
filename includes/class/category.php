<?php


class Category {

    private int $ID;
    private string $name;
    private int $parentID;
    private array $errors = [];
    private array $success = [];


    public function editCategory($categoryName, $categoryParent, $categoryID) {
        // Validate data
        if ($this->validateData($categoryName, $categoryParent) && $this->validateID($categoryID)) {
            // Edit category
            $this->updateDB();

            $this->displaySuccessMsg('edit');
        } else {
            // Display errors
            $this->displayErrorMsg();
        }
    }


    public function deleteCategory($categoryID) {
        // Validate ID
        if ($this->validateID($categoryID)) {
            // Delete category
            $this->deleteCategoryFromDB();

            $this->displaySuccessMsg('delete');
        } else {
            // Display errors
            $this->displayErrorMsg();
        }
    }


    public function displayCategoryTreeInSelectField($currentParentID = false, $parentID = 0, $hierarchy = '') {
        global $db;

        $categories = $db->query("SELECT id, name FROM tree WHERE parent_id = $parentID")->fetchAll();
        foreach ($categories as $values) {
            if ($currentParentID != $values['id']) {
                echo "<option value='{$values['id']}'>{$hierarchy} {$values['name']}</option>";
            } else {
                echo "<option selected value='{$values['id']}'>{$hierarchy} {$values['name']}</option>";
            }


            $this->displayCategoryTreeInSelectField($currentParentID, $values['id'], $hierarchy . '—');
        }
    }


    public function displayCategoryList() {
        global $db;

        $this->printListRecursive($db->query("SELECT * FROM tree")->fetchAll());
    }


    private function printListRecursive($list, $parentID = 0) {
        $foundSome = false;
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['parent_id'] == $parentID) {
                if ($foundSome == false) {
                    if ($i == 0) {
                        echo "<ul id='categoryContainer' class='list-group list-group-flush'>";
                    } else {
                        echo "<ul class='collapse show' id='collapse_id_" . $list[$i]['parent_id'] . "'>";
                    }

                    $foundSome = true;
                }
                echo "<li class='categoryList'>" . $list[$i]['name'];
                echo "<span data-bs-toggle='collapse' data-bs-target='#collapse_id_" . $list[$i]['id'] . "'>
                        <div class='categoryTreeArrow'>
                        <i class='bi bi-arrow-up'></i>
                        </div>                    
                        </span>
                        <div class='btnContainer float-end'>
                            <div class='d-inline-block'><a href='category_edit.php?id=" . $list[$i]['id'] . "&name=" . $list[$i]['name'] . "&parent_id=" . $list[$i]['parent_id'] . "'>Edit</a></div>
                            <div class='d-inline-block'><a class='link-danger categoryRemove' data-bs-toggle='modal' data-bs-target='#modalDeleteCategory' href='category_add.php?delete=1&id=" . $list[$i]['id'] . "'>Delete</a></div> 
                        </div> 
                        ";
                $this->printListRecursive($list, $list[$i]['id']);
                echo "</li>";
            }
        }
        if ($foundSome) {
            echo "</ul>";
        }
    }


    public function addCategory($categoryName, $categoryParent) {
        // Validate data
        if ($this->validateData($categoryName, $categoryParent)) {
            // Add category
            $this->addCategoryToDB();

            $this->displaySuccessMsg('add');
        } else {
            // Display errors
            $this->displayErrorMsg();
        }
    }


    private function addCategoryToDB() {
        global $db;

        $db->query("INSERT INTO tree(name, parent_id) VALUES (?, ?)", $this->name, $this->parentID);

    }


    private function updateDB() {
        global $db;

        $db->query("UPDATE tree SET name = ?, parent_id = ? WHERE id = ?", $this->name, $this->parentID, $this->ID);
    }


    private function deleteCategoryFromDB() {
        global $db;

        // Get parent_id of this category
        $parentID = $db->query("SELECT parent_id FROM tree WHERE id = ?", $this->ID)->fetchAll();
        $parentID = $parentID[0]['parent_id'];
        // Delete category
        $db->query("DELETE FROM tree WHERE id = ?", $this->ID);

        // Update parent_id for children
        $db->query("UPDATE tree SET parent_id = $parentID WHERE parent_id = $this->ID");
    }


    private function displayErrorMsg() {
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


    private function displaySuccessMsg($success) {
        $this->success += match ($success) {
            'add' => ['Kategoria dodana pomyślnie.'],
            'edit' => ['Kategoria edytowana pomyślnie. <a href="category_add.php">Powrót do zarządzania kategoriami.</a>'],
            'delete' => ['Kategoria została usunięta pomyślnie.'],
        };

        ?>
        <div class="alert alert-info" role="alert">
            <?php
            foreach ($this->success as $msg) {
                echo "<p class='mb-0'>" . $msg . "</p>";
            }
            ?>
        </div>
        <?php
    }


    private function validateData($name, $parentID): bool {
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


    private function validateID($categoryID): bool {

        // Check if category ID is numeric and if it exists
        if (is_numeric($categoryID)) {
            if (!$this->categoryExist($categoryID)) {
                $this->errors += ['Kategoria o podanym ID nie istnieje.'];
            }
        } else {
            $this->errors += ['Błędne ID kategorii.'];
        }


        // Return true if no errors and assign values to variables
        if (empty($this->errors)) {
            $this->ID = $categoryID;
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