$(document).ready(function() {

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

    //  Notification
    let options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "md-toast-top-left",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $('.toast').toast(options);


    tableCheckbox();
    modalSingleConfirmed();
    modalMultipleConfirmed();
    addEquipmentValue();

    flashes($('.notify'));
    password($('.input-prefix.fa-eye'));
    passwordGenerate($('#password-generate-btn'))
    readImage($('#entity-image'));

    /*modalSingleConfirmed($('.entity-booking-delete'), 'app_admin_booking_delete');
    modalMultipleConfirmed($('.entity-booking-delete-bulk-btn a.btn-danger'), 'app_admin_booking_bulk_delete')

    modalSingleConfirmed($('.entity-booking-confirm'), 'app_admin_booking_confirmed');
    modalMultipleConfirmed($('.entity-booking-confirm-bulk-btn a.btn-success'), 'app_admin_booking_bulk_confirmed')

    modalSingleConfirmed($('.entity-booking-cancel'), 'app_admin_booking_cancelled');
    modalMultipleConfirmed($('.entity-booking-cancel-bulk-btn a.btn-danger'), 'app_admin_booking_bulk_cancelled')

    modalSingleConfirmed($('.entity-room-delete'), 'app_admin_room_delete');
    modalMultipleConfirmed($('.entity-room-delete-bulk-btn a.btn-danger'), 'app_admin_room_bulk_delete');

    modalSingleConfirmed($('.entity-roomEquipment-delete'), 'app_admin_room_equipment_delete');
    modalMultipleConfirmed($('.entity-roomEquipment-delete-bulk-btn a.btn-danger'), 'app_admin_room_equipment_bulk_delete');

    modalSingleConfirmed($('.entity-room-gallery-delete'), 'app_admin_room_gallery_delete');
    modalMultipleConfirmed($('.entity-room-gallery-delete-bulk-btn a.btn-danger'), 'app_admin_room_gallery_bulk_delete');

    modalSingleConfirmed($('.entity-supplement-delete'), 'app_admin_supplement_delete');
    modalMultipleConfirmed($('.entity-supplement-delete-bulk-btn a.btn-danger'), 'app_admin_supplement_bulk_delete');

    modalSingleConfirmed();
    modalMultipleConfirmed();

    modalSingleConfirmed();
    modalMultipleConfirmed();
    */

















    // Time js
    /*const terms = [
        {
            time: 45,
            divide: 60,
            text: "moins d'une minute"
        },
        {
            time: 90,
            divide: 60,
            text: 'environ une minute'
        },
        {
            time: 45 * 60,
            divide: 60,
            text: '%d minutes'
        },
        {
            time: 90 * 60,
            divide: 60 * 60,
            text: 'environ une heure'
        },
        {
            time: 24 * 60 * 60,
            divide: 60 * 60,
            text: '%d heures'
        },
        {
            time: 42 * 60 * 60,
            divide: 24 * 60 * 60,
            text: 'environ un jour'
        },
        {
            time: 30 * 24 * 60 * 60,
            divide: 24 * 60 * 60,
            text: '%d jours'
        },
        {
            time: 45 * 24 * 60 * 60,
            divide: 24 * 60 * 60 * 30,
            text: 'environ un mois'
        },
        {
            time: 365 * 24 * 60 * 60,
            divide: 24 * 60 * 60 * 30,
            text: '%d mois'
        },
        {
            time: 365 * 1.5 * 24 * 60 * 60,
            divide: 24 * 60 * 60 * 365,
            text: 'environ un an'
        },
        {
            time: Infinity,
            divide: 24 * 60 * 60 * 365,
            text: '%d ans'
        }
    ];

    let $dataTime = $('[data-time]');

    $dataTime.each(function (index, element) {
        const timestamp = parseInt(element.getAttribute('data-time'), 10) * 1000;
        const date = new Date(timestamp);

        updateText(date, element, terms);
    });*/

    // Gestion des checkbox dans la liste

    /*let $principalCheckbox = $('#principal-checkbox'),
        $listCheckbook = $('.list-checkbook'),
        $listCheckbookLength = $listCheckbook.length,
        $listCheckbookNumber = 0,
        $btnBulkDelete = $('#entity-list-delete-bulk-btn'),
        $btnClassBulkDelete = $('.entity-list-delete-bulk-btn');

    $principalCheckbox.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $('.list-checkbook').prop('checked', true);

            $listCheckbookNumber = $listCheckbookLength;

            if ($listCheckbookLength > 0) {
                $btnBulkDelete.removeClass('d-none');
                $btnClassBulkDelete.removeClass('d-none');
            }

        } else {
            $('.list-checkbook').prop('checked', false);
            $btnBulkDelete.addClass('d-none');
            $btnClassBulkDelete.addClass('d-none');

            $listCheckbookNumber = 0;
        }
    });

    $listCheckbook.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $listCheckbookNumber++;
            $btnBulkDelete.removeClass('d-none');
            $btnClassBulkDelete.removeClass('d-none');

            if ($listCheckbookNumber === $listCheckbookLength)
                $principalCheckbox.prop('checked', true)
        } else {
            $listCheckbookNumber--;

            if ($listCheckbookNumber === 0) {
                $btnBulkDelete.addClass('d-none');
                $btnClassBulkDelete.addClass('d-none');
            }

            if ($listCheckbookNumber < $listCheckbookLength)
                $principalCheckbox.prop('checked', false)
        }
    });

    let $container = $('#modal-container'),
        $checkbookContainer = $('#list-checkbook-container');*/

    // Gestion des validation
    /*simpleModals($('.entity-advert-validate'), 'app_admin_advert_validate', $container);
    bulkModals($('.entity-advert-validate-bulk-btn a.btn-success'), $checkbookContainer,
        'app_admin_advert_bulk_validate', $container);

    // Gestion des refus
    simpleModals($('.entity-advert-denied'), 'app_admin_advert_denied', $container);
    bulkModals($('.entity-advert-denied-bulk-btn a.btn-amber'), $checkbookContainer,
        'app_admin_advert_bulk_denied', $container);

    // Gestion des suppression partielle
    simpleModals($('.entity-advert-soft-delete'), 'app_admin_advert_soft_delete', $container);

    // Gestion des bannissement
    simpleModals($('.entity-advert-bannir'), 'app_admin_advert_bannir', $container);
    */

    // Gestion des suppression

    // Booking
    /*simpleModals($('.entity-booking-delete'), 'app_admin_booking_delete', $container);
    bulkModals($('.entity-booking-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_booking_bulk_delete', $container);

    // Gestion des confirmations
    simpleModals($('.entity-booking-confirm'), 'app_admin_booking_confirmed', $container);
    bulkModals($('.entity-booking-confirm-bulk-btn a.btn-success'), $checkbookContainer,
        'app_admin_booking_bulk_confirmed', $container);

    // Gestion des annulation
    simpleModals($('.entity-booking-cancel'), 'app_admin_booking_cancelled', $container);
    bulkModals($('.entity-booking-cancel-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_booking_bulk_cancelled', $container);

    // Equipment group
    simpleModals($('.entity-equipment-group-delete'), 'app_admin_equipment_group_delete', $container);
    bulkModals($('.entity-equipment-group-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_equipment_group_bulk_delete', $container);

    // Equipment
    simpleModals($('.entity-equipment-delete'), 'app_admin_equipment_delete', $container);
    bulkModals($('.entity-demand-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'entity-equipment-delete-bulk-btn', $container);

    // Gallery
    simpleModals($('.entity-gallery-delete'), 'app_admin_gallery_delete', $container);
    bulkModals($('.entity-gallery-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_gallery_bulk_delete', $container);

    // Room
    simpleModals($('.entity-room-delete'), 'app_admin_room_delete', $container);
    bulkModals($('.entity-room-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_room_bulk_delete', $container);

    // Room equipment
    simpleModals($('.entity-roomEquipment-delete'), 'app_admin_room_equipment_delete', $container);
    bulkModals($('.entity-roomEquipment-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_room_equipment_bulk_delete', $container);

    // Room gallery
    simpleModals($('.entity-room-gallery-delete'), 'app_admin_room_gallery_delete', $container);
    bulkModals($('.entity-room-gallery-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_room_gallery_bulk_delete', $container);

    // Supplement
    simpleModals($('.entity-supplement-delete'), 'app_admin_supplement_delete', $container);
    bulkModals($('.entity-supplement-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_supplement_bulk_delete', $container);

    // Administrateur
    simpleModals($('.entity-admin-delete'), 'app_admin_admin_delete', $container);
    bulkModals($('.entity-admin-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_admin_bulk_delete', $container);

    // User
    simpleModals($('.entity-user-delete'), 'app_admin_user_delete', $container);
    bulkModals($('.entity-user-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_user_bulk_delete', $container);

    // Discount
    simpleModals($('.entity-discount-delete'), 'app_admin_discount_delete', $container);
    bulkModals($('.entity-discount-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_discount_bulk_delete', $container);

    // Order
    simpleModals($('.entity-order-delete'), 'app_admin_commande_delete', $container);
    bulkModals($('.entity-order-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_commande_bulk_delete', $container);

    // Payment
    simpleModals($('.entity-payment-delete'), 'app_admin_payment_delete', $container);
    bulkModals($('.entity-payment-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'entity-payment-delete-bulk-btn', $container);

    // Emailing
    simpleModals($('.entity-emailing-delete'), 'app_admin_emailing_delete', $container);
    bulkModals($('.entity-emailing-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_emailing_bulk_delete', $container);

    // Taxe
    simpleModals($('.entity-taxe-delete'), 'app_admin_taxe_delete', $container);
    bulkModals($('.entity-taxe-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_taxe_bulk_delete', $container);

    // Option
    simpleModals($('.entity-option-delete'), 'app_admin_option_delete', $container);
    bulkModals($('.entity-option-delete-bulk-btn a.btn-danger'), $checkbookContainer,
        'app_admin_option_bulk_delete', $container);*/





});

const password = function (element) {
    element.click(function () {
        passwordView($(this));
    });
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

/*Centre d'affaires		1

Equipement audio visuel		2

Salle de conférence		3

Accès à internet		4

Accès à internet gratuit		5

Journal dans le hall		6

Boissons de bienvenu		7

Casino		8

Boite de nuit		9

Karaoké		10

Service de blanchisserie		11

Alimentation de secours		12

GAB/Banque		13

Echange de devise		14

Salle de fete*/




