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