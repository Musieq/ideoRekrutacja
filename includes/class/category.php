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
            // Get current parentID
            global $db;
            $currentParent = $db->query("SELECT parent_id FROM tree WHERE id = ?", $this->ID)->fetchAll();
            $currentParent = $currentParent[0]['parent_id'];

            // Check if category has children. If it has, change their parent_id to $currentParent
            if ($db->query("SELECT id FROM tree WHERE parent_id = ? LIMIT 1", $this->ID)->fetchAll()) {
                $db->query("UPDATE tree SET parent_id = ? WHERE parent_id = ?", $currentParent, $this->ID);
            }

            // Update order
            $this->reorderCategories($this->parentID, $currentParent, false);

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


    public function changeCategoryOrder($catOrder) {
        global $db;

        $catOrderArr = $this->createCategoryArray($catOrder);

        foreach ($catOrderArr as $value) {
            $orderIDs = $value[1];
            $orderArr = explode(',', $orderIDs);

            foreach ($orderArr as $order => $ID) {
                $db->query("UPDATE tree SET cat_order = $order + 1 WHERE id = $ID");
            }
        }

        $this->displaySuccessMsg('reorder');
    }

    private function createCategoryArray($catOrder): array {
        $catOrder = explode(', ',$catOrder);

        $catOrderArr = [];
        foreach ($catOrder as $value) {
            $catOrderArr[] = explode(' => ',$value);
        }

        return $catOrderArr;
    }


    public function displayCategoryTreeInSelectField($currentParentID = false, $parentID = 0, $hierarchy = '') {
        global $db;

        $categories = $db->query("SELECT id, name FROM tree WHERE parent_id = $parentID ORDER BY cat_order")->fetchAll();
        foreach ($categories as $values) {
            if ($currentParentID != $values['id']) {
                echo "<option value='{$values['id']}'>{$hierarchy} {$values['name']}</option>";
            } else {
                echo "<option selected value='{$values['id']}'>{$hierarchy} {$values['name']}</option>";
            }


            $this->displayCategoryTreeInSelectField($currentParentID, $values['id'], $hierarchy . '—');
        }
    }


    public function displayCategoryList($printBtn) {
        global $db;

        $this->printListRecursive($db->query("SELECT * FROM tree ORDER BY cat_order")->fetchAll(), $printBtn);
    }


    private function printListRecursive($list, $printBtn = false, $parentID = 0) {
        $foundSome = false;
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]['parent_id'] == $parentID) {
                if ($foundSome == false) {
                    if ($i == 0) {
                        echo "<ul id='categoryContainer' class='categorySort' data-parent-id='" . $list[$i]['parent_id'] . "'>";
                    } else {
                        echo "<ul class='collapse show categorySort' data-parent-id='" . $list[$i]['parent_id'] . "' id='collapse_id_" . $list[$i]['parent_id'] . "'>";
                    }

                    $foundSome = true;
                }
                echo "<li class='categoryList' data-id='" . $list[$i]['id'] . "'>" . $list[$i]['name'];
                echo "<span data-bs-toggle='collapse' data-bs-target='#collapse_id_" . $list[$i]['id'] . "'>
                        <div class='categoryTreeArrow'>
                        <i class='bi bi-arrow-up'></i>
                        </div>                    
                        </span>";
                if ($printBtn) {
                    echo"   <div class='btnContainer float-end'>
                                <div class='d-inline-block'><a href='category_edit.php?id=" . $list[$i]['id'] . "&name=" . $list[$i]['name'] . "&parent_id=" . $list[$i]['parent_id'] . "'>Edit</a></div>
                                <div class='d-inline-block'><a class='link-danger categoryRemove' data-bs-toggle='modal' data-bs-target='#modalDeleteCategory' href='index.php?delete=1&id=" . $list[$i]['id'] . "'>Delete</a></div>
                            </div>";
                }

                $this->printListRecursive($list, $printBtn, $list[$i]['id']);
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

        if ($lastOrder = $this->getLastOrder($this->parentID)) {
            $lastOrder = $lastOrder[0]['cat_order'] + 1;
            $db->query("INSERT INTO tree(name, parent_id, cat_order) VALUES (?, ?, ?)", $this->name, $this->parentID, $lastOrder);
        } else {
            $db->query("INSERT INTO tree(name, parent_id) VALUES (?, ?)", $this->name, $this->parentID);
        }
    }


    private function updateDB() {
        global $db;

        $db->query("UPDATE tree SET name = ?, parent_id = ? WHERE id = ?", $this->name, $this->parentID, $this->ID);
    }


    private function getLastOrder($parentID) {
        global $db;

        return $db->query("SELECT cat_order FROM tree WHERE parent_id = ? ORDER BY cat_order DESC LIMIT 1", $parentID)->fetchAll();
    }


    private function deleteCategoryFromDB() {
        global $db;

        // Get parent_id of this category
        $parentID = $db->query("SELECT parent_id FROM tree WHERE id = ?", $this->ID)->fetchAll();
        $parentID = $parentID[0]['parent_id'];
        // Delete category
        $db->query("DELETE FROM tree WHERE id = ?", $this->ID);

        $this->reorderCategories($parentID, $this->ID);

        // Update parent_id for children
        $db->query("UPDATE tree SET parent_id = $parentID WHERE parent_id = $this->ID");
    }

    private function reorderCategories($newParentID, $currentParentID, $bulk = true) {
        global $db;

        if ($lastOrder = $this->getLastOrder($newParentID)){
            $lastOrder = $lastOrder[0]['cat_order'];

            if ($bulk) {
                $db->query("UPDATE tree SET cat_order = cat_order + ? WHERE parent_id = ?", $lastOrder, $currentParentID);
            } else {
                $db->query("UPDATE tree SET cat_order = $lastOrder + 1 WHERE id = ?", $this->ID);
            }
        }
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
            'edit' => ['Kategoria edytowana pomyślnie. <a href="index.php">Powrót do zarządzania kategoriami.</a>'],
            'delete' => ['Kategoria została usunięta pomyślnie.'],
            'reorder' => ['Kolejność kategorii zmieniona pomyślnie.']
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