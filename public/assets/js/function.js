// Time js
const terms = [
    { time: 45, divide: 60, text: "moins d'une minute" },
    { time: 90, divide: 60, text: 'environ une minute' },
    { time: 45 * 60, divide: 60, text: '%d minutes' },
    { time: 90 * 60, divide: 60 * 60, text: 'environ une heure' },
    { time: 24 * 60 * 60, divide: 60 * 60, text: '%d heures' },
    { time: 42 * 60 * 60, divide: 24 * 60 * 60, text: 'environ un jour' },
    { time: 30 * 24 * 60 * 60, divide: 24 * 60 * 60, text: '%d jours' },
    { time: 45 * 24 * 60 * 60, divide: 24 * 60 * 60 * 30, text: 'environ un mois' },
    { time: 365 * 24 * 60 * 60, divide: 24 * 60 * 60 * 30, text: '%d mois' },
    { time: 365 * 1.5 * 24 * 60 * 60, divide: 24 * 60 * 60 * 365, text: 'environ un an' },
    { time: Infinity, divide: 24 * 60 * 60 * 365, text: '%d ans' }
];

let $dataTime = $('[data-time]');

$dataTime.each(function (index, element) {
    const timestamp = parseInt(element.getAttribute('data-time'), 10) * 1000;
    const date = new Date(timestamp);

    updateText(date, element, terms);
});

const updateText = function (date, element, terms) {
    const seconds = (new Date().getTime() - date.getTime()) / 1000;
    let term = null;
    const prefix = element.getAttribute('prefix');

    for (term of terms) {
        if (Math.abs(seconds) < term.time) {
            break
        }
    }

    if (seconds >= 0) {
        element.innerHTML = `${prefix || 'Il y a'} ${term.text.replace('%d', Math.round(seconds / term.divide))}`
    } else {
        element.innerHTML = `${prefix || 'Dans'} ${term.text.replace('%d', Math.round(Math.abs(seconds) / term.divide))}`
    }
}

const showLoading = function () {
    $(document).find('#loader').show();
}

const hideLoading = function () {
    $(document).find('#loader').hide();
}

const flashes = function (selector) {
    selector.each(function (index, element) {
        if ($(element).html() !== undefined) {
            toastr[$(element).attr('data-label')]($(element).html());
        }
    });
}

const radioCheck = function (element) {
    element.click(function () {
        const $this = $(this);

        $this.parents('.parent-data').find('.form-check.data').removeClass('active');
        $this.find('input[type="radio"]').prop('checked', true);
        $this.addClass('active');
    });
}

const tableCheckbox = function () {
    const $main_checkbox = $('#principal-checkbox');
    const $checkbox_list = $('.list-checkbook');
    let checkbox_list_length = $checkbox_list.length,
        checkbox_number = 0,
        $btn_bulk_delete = $('#entity-list-delete-bulk-btn'),
        $btn_class_bulk_delete = $('.entity-list-delete-bulk-btn');

    $main_checkbox.on('click', function () {
        const $this = $(this);

        if ($this.prop('checked')) {
            $checkbox_list.prop('checked', true);

            checkbox_number = checkbox_list_length;

            if (checkbox_list_length > 0) {
                $btn_bulk_delete.removeClass('d-none');
                $btn_class_bulk_delete.removeClass('d-none');
            }
        } else {
            $checkbox_list.prop('checked', false);
            $btn_bulk_delete.addClass('d-none');
            $btn_class_bulk_delete.addClass('d-none');

            checkbox_number = 0;
        }
    });

    $checkbox_list.on('click', function () {
        const $this = $(this);

        if ($this.prop('checked')) {
            checkbox_number++;
            $btn_bulk_delete.removeClass('d-none');
            $btn_class_bulk_delete.removeClass('d-none');

            if (checkbox_number === checkbox_list_length) {
                $main_checkbox.prop('checked', true)
            }
        } else {
            checkbox_number--;

            if (checkbox_number === 0) {
                $btn_bulk_delete.addClass('d-none');
                $btn_class_bulk_delete.addClass('d-none');
            }

            if (checkbox_number < checkbox_list_length) {
                $main_checkbox.prop('checked', false)
            }
        }
    });
}

const modalSingleConfirmed = function () {
    $('.entity-delete-btn').click(function (e) {
        e.preventDefault();

        showLoading();

        const $this = $(this);

        $.ajax({
            url: $this.attr('data-url'),
            type: 'GET',
            error: function () {
                hideLoading();
            },
            success: function(data) {
                hideLoading();

                $('#modal-container').html(data.html);
                $('#confirm' + $this.attr('data-id')).modal();
            }
        });
    });
}

const modalMultipleConfirmed = function () {
    $('#entity-bulk-delete-btn').click(function (e) {
        e.preventDefault();

        const $this = $(this);

        showLoading();

        const ids = [];

        $('#list-checkbook-container').find('.list-checkbook').each(function () {
            const $this = $(this);

            if ($this.prop('checked')) {
                ids.push($this.val());
            }
        });

        if (ids.length) {
            $.ajax({
                url: $this.attr('data-url'),
                data: {'data': JSON.stringify(ids)},
                type: 'GET',
                error: function () {
                    hideLoading();
                },
                success: function(data) {
                    hideLoading();

                    $('#modal-container').html(data.html);
                    $('#confirmMulti' + ids.length).modal();
                },
            });
        }
    });
}

const passwordView = function (element) {
    if (element.hasClass('fa-eye')) {
        element.removeClass('fa-eye').addClass('fa-eye-slash view');
        element.siblings('input').get(0).type = 'text';
    } else {
        element.removeClass('fa-eye-slash view').addClass('fa-eye');
        element.siblings('input').get(0).type = 'password';
    }
}

const generatePassword = function ($elements) {
    showLoading();

    let $request = new Request('https://api.motdepasse.xyz/create/?include_digits&include_lowercase&include_uppercase&password_length=8&quantity=1');

    fetch($request)
        .then((response) => response.json())
        .then(function(json_response) {
            json_response.passwords.forEach((password) => {
                $.each($elements, function(index, element){
                    $(element).val(password);
                })

                hideLoading();
            });
        });
}

const newsletter = function (element) {
    element.submit(function (e) {
        e.preventDefault();

        showLoading();

        $.ajax({
            url: $(element).attr('action'),
            type: $(element).attr('method'),
            data: element.serialize(),
            success: function(data) {
                hideLoading();

                if (data.success) {
                    notification('Newsletter', data.message, {}, 'success')
                } else {
                    let errors = $.parseJSON(data.errors);

                    $(errors).each(function (key, value) {
                        notification('Newsletter', value, {}, 'error')
                    });
                }
            }
        })
    });
}

const readURL = function (input) {

    let url = input.value;
    let ext = url.substring(url.lastIndexOf('.')+1).toLowerCase();

    if (input.files && input.files[0] && (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg')) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#image-view').attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0])
    }
}

const getMonthsShort = function (month) {
    let monthShortLists = ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'];

    return monthShortLists[month];
}

const silverCarousel = function () {
    $('.carousel .carousel-inner.vv-3 .carousel-item').each(function () {
        let next = $(this).next();

        if (!next.length) {
            next = $(this).siblings(':first');
        }

        next.children(':first-child').clone().appendTo($(this));

        for (let i = 0; i < 4; i++) {
            next = next.next();
            if (!next.length) {
                next = $(this).siblings(':first');
            }

            next.children(':first-child').clone().appendTo($(this));
        }

        $('.carousel').carousel('cycle');
    });
}

