/** Remove collapse buttons from last elements **/
(function() {
    const categoryList = document.querySelectorAll('.categoryList');
    if (!categoryList) {
        return;
    }

    categoryList.forEach(el => {
        if (el.childElementCount === 1) {
            let child = el.firstElementChild;
            el.removeChild(child);
        }
    })
})();
