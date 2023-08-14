$(document).ready(function () {
    // SideNav Button Initialization
    $('.button-collapse').sideNav({
        edge: 'left',
        closeOnClick: false,
        breakpoint: 1440,
        menuWidth: 270,
        timeDurationOpen: 300,
        timeDurationClose: 200,
        timeDurationOverlayOpen: 50,
        timeDurationOverlayClose: 200,
        easingOpen: 'easeOutQuad',
        easingClose: 'easeOutCubic',
        showOverlay: true,
        showCloseButton: false
    });

    // SideNav Scrollbar Initialization
    const sideNavScrollbar = document.querySelector('.custom-scrollbar');
    const ps = new PerfectScrollbar(sideNavScrollbar);

    // Material select 
    $('.mdb-select').materialSelect();
    $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
        $(this).closest('.select-outline').find('label').toggleClass('active');
        $(this).closest('.select-outline').find('.caret').toggleClass('active');
    });

    tableCheckbox(); 
    addEquipmentValue();

    modalSingleConfirmed($('.entity-delete-btn'));
    modalSingleConfirmed($('.entity-booking-confirm'));
    modalSingleConfirmed($('.entity-booking-cancel'));
    modalMultipleConfirmed($('#entity-bulk-delete-btn'));
    modalMultipleConfirmed($('#entity-bulk-confirm-btn'));
    modalMultipleConfirmed($('#entity-bulk-cancel-btn'));
    flashes($('.notify'));
    passwordGenerate($('#password-generate-btn'));
    readImage($('#entity-image'));
    viewPassword($('.input-prefix.fa-eye'));


});

const addEquipmentValue = function () {
    const $wrapper = $('#equipment-form-value-wrapper'), $equipment_add_btn = $('#add_new_equipment');
    let $equipment_state = parseInt($wrapper.data('index'));

    if ($wrapper.length) {
        if ($equipment_state === 0) {
            addEquipmentFormToCollection($wrapper);
        }
    }

    $equipment_add_btn .click(function(e) {
        e.preventDefault();

        addEquipmentFormToCollection($wrapper);
    });

    $wrapper.on('click', '.delete_equipment', function(e) {
        e.preventDefault();
        const $this = $(this);

        $this.closest('#equipment_values_'+ $this.attr('id')).fadeOut().remove();
        $wrapper.data('index', $wrapper.data('index') - 1);
    });

    $('a.delete_equipment').click(function(e) {
        e.preventDefault();

        const $this = $(this), $parent = $this.parents('.equipment-data-row');

        $parent.fadeOut().remove();
        $wrapper.data('index', $wrapper.data('index') - 1);
    });
}

const addEquipmentFormToCollection = (container) => {

    let index = container.data('index'),
        prototype = container.data('prototype');

    container.append(prototype.replace(/__name__/g, index));

    let $equipment_content =  $('#equipment_values_' + index),
        $equipment_delete_btn = $('<div class="col-2 col-md-1"><a id="' + index + '" class="delete_equipment btn-floating btn-danger btn-sm"><i class="fas fa-trash"></i></a></div>');

    $equipment_content.addClass('form-row');
    $equipment_content.wrapInner('<div class="col-10 col-md-4 col-lg-3"></div>');

    if (index > 0) {
        $equipment_content.append($equipment_delete_btn);
    }

    container.data('index', index + 1);
}

const passwordGenerate = function (element) {
    element.click(function (e) {
        e.preventDefault();

        generatePassword($('#password-bulk').find('input'));
    });
}

const readImage = function (element) {
    element.change(function () {readURL(this)});
}

const viewPassword = function (element) {
    element.click(function () {
        passwordView($(this));
    });
}




