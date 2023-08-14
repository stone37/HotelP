$(document).ready(function() {

    let $checkin_date = $('.checkin-datepicker'),
        $checkout_date = $('.checkout-datepicker'),
        $booking_checkin_btn = $('#booking-checkin-btn .checkin'),
        $booking_checkout_btn = $('#booking-checkout-btn .checkout'),
        $booking_booker_btn = $('#booking-booker-btn .user'),
        $booking_booker_modal = $('#booking-booker-modal'),
        $booking_checkin_date = $booking_checkin_btn.find('.date-content'),
        $booking_checkout_date = $booking_checkout_btn.find('.date-content'),
        $booking_adult_field = $('input.booking_data_adult'),
        $booking_children_field = $('input.booking_data_children'),
        $booking_room_field = $('input.booking_data_room'),
        $booking_booker_adult_field = $('#booking-booker-modal .modal-body .adults a'),
        $booking_booker_children_field = $('#booking-booker-modal .modal-body .children a'),
        $booking_booker_room_field = $('#booking-booker-modal .modal-body .room a'),
        $booking_booker_button = $('#booking-booker-modal button'),
        $adults = window.hostel.DEFAULT_ADULT,
        $children = window.hostel.DEFAULT_CHILDREN,
        $rooms = window.hostel.DEFAULT_ROOM;

    let checkin_datepicker = $checkin_date.pickadate({
            min: window.hostel.BOOKING_CHECKIN,
            selectMonths: false,
            selectYears: false,
            weekdaysFull: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthsShort: ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
            weekdaysShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
            format: 'dd mmm yyyy',
            formatSubmit: 'yyyy-mm-dd',
            today: '',
            clear: '',
            close: 'Fermer',
            onClose: function () {
                $booking_checkin_btn.removeClass('active');
            },
            onSet: function () {
                if ($checkin_picker.get('select')) {
                    const selected = $checkin_picker.get('select');

                    $checkout_picker.set('min', new Date(selected.obj.getFullYear(), selected.obj.getMonth(),selected.obj.getDate() + 1));
                    $booking_checkin_date.text($checkin_picker.get('value'));
                }
            }
        }),
        $checkin_picker = checkin_datepicker.pickadate('picker');

    let checkout_datepicker = $checkout_date.pickadate({
            min: window.hostel.BOOKING_CHECKOUT,
            selectMonths: false,
            selectYears: false,
            weekdaysFull: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthsShort: ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
            weekdaysShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
            format: 'dd mmm yyyy',
            formatSubmit: 'yyyy-mm-dd',
            today: '',
            clear: '',
            close: 'Fermer',
            onClose: function() {
                $booking_checkout_btn.removeClass('active');
            },
            onSet: function() {
                if (!$checkout_picker.get('select') && $checkin_picker.get('select')) {
                    const selected = $checkin_picker.get('select'),
                          d = new Date(selected.obj.getFullYear(), selected.obj.getMonth(),selected.obj.getDate() + 1);

                    $booking_checkout_date.text(d.getDate() + ' ' + getMonthsShort(d.getMonth()) + ' ' + d.getFullYear());
                }

                if ($checkout_picker.get('select')) {
                    const selected = $checkout_picker.get('select'),
                        d = new Date(selected.obj.getFullYear(), selected.obj.getMonth(),selected.obj.getDate());

                    $booking_checkout_date.text(d.getDate() + ' ' + getMonthsShort(d.getMonth()) + ' ' + d.getFullYear());
                }
            }
        }),
        $checkout_picker = checkout_datepicker.pickadate('picker');

    $booking_checkin_btn.click(function (e) {
        e.preventDefault();

        $(this).addClass('active');

        $checkin_date.trigger('click');
    });

    $booking_checkout_btn.click(function (e) {
        e.preventDefault();

        $(this).addClass('active');

        $checkout_date.trigger('click');
    });

    $booking_booker_btn.click(function(e){
        e.preventDefault();

        $(this).addClass('active');

        $booking_booker_modal.modal()
    });

    $booking_booker_modal.on('show.bs.modal', function () {
        $('body').css('overflow', 'hidden');
    });

    $booking_booker_modal.on('hide.bs.modal', function () {
        $booking_booker_btn.removeClass('active');

        $('body').css('overflow', 'auto');
    });

    $booking_booker_adult_field.click(function (e) {
        e.preventDefault();

        const $this = $(this);

        if ($this.hasClass('soustraction')) {
            const $sibling = $($this.siblings('.addition')[0]),
                $element = $this.parents('.data').find('span');

            $adults--;

            if ($adults === 1) {
                $this.addClass('disabled');
            }

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($adults)
        } else {
            const $sibling = $($this.siblings('.soustraction')[0]),
                $element = $this.parents('.data').find('span');

            $adults++;

            if ($adults === 4) {
                $this.addClass('disabled');
            }

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($adults)
        }
    });

    $booking_booker_children_field.click(function (e) {
        e.preventDefault();

        const $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling =  $($this.siblings('.addition')[0]),
                $element = $this.parents('.data').find('span');

            $children--;

            if ($children === 0) {
                $this.addClass('disabled');
            }

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($children)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]),
                $element = $this.parents('.data').find('span');

            $children++;

            if ($children === 4) {
                $this.addClass('disabled');
            }

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($children)
        }
    });

    $booking_booker_room_field.click(function (e) {
        e.preventDefault();

        const $this = $(this);

        if ($this.hasClass('soustraction')) {
            let $sibling = $($this.siblings('.addition')[0]),
                $element = $this.parents('.data').find('span');

            $rooms--;

            if ($rooms === 1) {
                $this.addClass('disabled');
            }

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($rooms)
        } else {
            let $sibling = $($this.siblings('.soustraction')[0]),
                $element = $this.parents('.data').find('span');

            $rooms++;

            if ($rooms === 10) {
                $this.addClass('disabled');
            }

            if ($sibling.hasClass('disabled')) {
                $sibling.removeClass('disabled');
            }

            $($element[0]).text($rooms)
        }
    });

    $booking_booker_button.click(function (e) {
        e.preventDefault();

        const $room = $($booking_booker_btn.find('.room')),
            $customer = $($booking_booker_btn.find('.customer')),
            $child = $($booking_booker_btn.find('.children'));

        $booking_adult_field.val($adults);
        $booking_children_field.val($children);
        $booking_room_field.val($rooms);

        $room.text($rooms + ' chambre, ');
        $customer.text($adults + ' adultes, ');
        $child.text($children + ' enfants');

        $('#booking-booker-modal').modal('hide')
    });

    bookingSelectData();
});



