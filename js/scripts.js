/** Remove collapse arrows from last elements **/
(function() {
    const categoryList = document.querySelectorAll('.categoryList');
    if (!categoryList) {
        return;
    }

    categoryList.forEach(el => {
        let hasChild = el.querySelector("ul") != null;
        if (!hasChild) {
            let child = el.firstElementChild;
            el.removeChild(child);
        }
    })
})();



/** Handles deleting after confirming it in modal window. Pass delete button class and ID of modal confirmation button. Function passes href to button in modal window. **/
function deleteAndShowModal(deleteBtnClass, deleteBtnModalID) {
    const deleteLinkSelector = document.querySelectorAll(`.${deleteBtnClass}`);
    const deleteModalConfirmSelector = document.getElementById(`${deleteBtnModalID}`);
    deleteLinkSelector.forEach(e => {
        e.addEventListener('click', function (){
            console.log(e.href);
            deleteModalConfirmSelector.addEventListener('click', function (){
                window.location.href = e.href;
            })
        })
    })
}


/** Sortable plugin init **/
(function () {
    const categoryContainer = document.getElementById('categoryContainer');
    // Hidden form input
    const categoryReorder = document.getElementById('categoryOrder');
    if (!categoryReorder || !categoryContainer) {
        return;
    }

    // Add listener for when user changes list order
    sortable('.categorySort').forEach(container => {
        container.addEventListener('sortupdate', function(e) {
            //console.log(e.detail);
            updateCategoryOrderInput();
        })
    })

    function updateCategoryOrderInput() {
        let orderArr = {};

        //.categorySort = ul element
        let categorySort = document.querySelectorAll('.categorySort');
        categorySort.forEach(container => {
            let parentID = container.getAttribute('data-parent-id');
            let temp = [];
            let children = container.children;
            for (let i = 0; i < children.length; i++) {
                let id = children[i].getAttribute('data-id')
                temp.push(id);
            }
            orderArr[parentID] = temp;
        })

        setInputValues(orderArr);
    }
    updateCategoryOrderInput();


    function setInputValues(orderArr) {
        // set value for input
        let arrLength = Object.entries(orderArr).length;
        let counter = 1;

        // Reset input value
        categoryReorder.value = '';
        for (const [key, value] of Object.entries(orderArr)) {
            categoryReorder.value += `${key} => ${value}`

            if (counter < arrLength) {
                categoryReorder.value += ', ';
            }
            counter++;
        }
    }
})();

