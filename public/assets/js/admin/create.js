$(document).ready(function() {
    viewImage();
});

const viewImage = function () {
    $('.entity-image').change(function () {
        readURL(this)
    });
};
