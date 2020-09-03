function addSubForm($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    var $newForm = $(newForm);
    $newForm.find('a.btn-add-subform').on('click', actionAddSubForm);
    $newForm.find('a.action-remove').click(removeSubForm);

    $collectionHolder.append($newForm);
}

function actionAddSubForm(e) {
    // prevent the link from creating a "#" on the URL
    e.preventDefault();

    // add a new sub form
    addSubForm($('#' + $(this).data('subform')));
}

function removeSubForm(e) {
    // prevent the link from creating a "#" on the URL
    e.preventDefault();

    $(this).closest('li').remove();

    return false;
}

jQuery(document).ready(function() {
    // Get the ul that holds the subforms list
    var $collectionHolder = $('ul.form-subform-list');
    $collectionHolder.data('index', $collectionHolder.children('li').length);

    $('a.btn-add-subform').on('click', actionAddSubForm);

    $('a.action-remove').click(removeSubForm);
});