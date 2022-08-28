$(function () {
    let form_audio     = $('.audio-form'),
        request_url    = '/audio/generate.php',
        input          = $('<input type="hidden" name="required" value="success">'),
        btn            = $('.audio-submit'),
        test           = $('.audio-test'),
        btns           = $('.audio-btns'),
        btnListen      = $('.btn-listen'),
        response_block = $('.server-response'),
        ajaxFormSubmit = function (form, event) {
            let string = $(form).serialize();

            loader(true);

            setTimeout(() => {
                if (form_audio.find('[name="required"]').length >= 1) {
                    $.ajax({
                        url    : request_url,
                        type   : "POST",
                        data   : string,
                        success: (data) => {

                            if (data.STATUS === 'listen') {
                                test.attr('src', data.TEXT).fadeIn(200).trigger("play");
                            }

                            if (data.STATUS === 'success') {
                                form.reset();
                                response_block.html(alertNTF('success', data.TEXT));
                                goToResponseBlock(response_block);
                            }

                            if (data.STATUS === 'error') {
                                response_block.html(alertNTF('danger', data.TEXT));
                                goToResponseBlock(response_block);
                            }

                            loader(false);
                        },
                        error  : (err) => {
                            response_block.html(alertNTF(
                                'danger',
                                'Ошибка ответа сервера!<br>' +
                                'Возможно, превышено кол-во символов (более 5 тыс.)<br>' +
                                '<a href="https://involta.ru/tools/length-chars/" target="_blank">Сайт для подсчёта символов в тексте.</a>' +
                                '<br>Повторите попытку!'));
                            goToResponseBlock(response_block);
                            loader(false);
                        }
                    });
                } else {
                    return false
                }
            }, 300)
        };

    btn.on('click', function () {
        form_audio.append(input);
    });

    form_audio.validate({
        rules        : {
            news_id  : {
                required: true,
                digits  : true
            },
            news_text: {
                required: true
            }
        },
        messages     : {
            news_id  : {
                required: 'ID новости обязателен к заполнению',
                digits  : 'Только цифры'
            },
            news_text: {
                required: 'Текст новости обязателен к заполнению'
            }
        },
        focusCleanup : false,
        focusInvalid : true,
        submitHandler: ajaxFormSubmit
    });
});

function loader(state) {
    let body         = $('body'),
        loader       = '<div class="audio-loader">\n' +
                    '        <div class="audio-loader__item">\n' +
                    '            <img class="audio-loader__item-loader" src="/images/loader.gif" alt="Наше время">\n' +
                    '            <p class="audio-loader__item-text h4">Генерация...</p>\n' +
                    '        </div>\n' +
                    '   </div>',
        loader_class = '.audio-loader',
        input        = '[value="success"]';

    body.css('overflow', 'hidden');
    if (state) {
        body.prepend(loader);
        $(loader_class).fadeIn(200);
    } else {
        $(loader_class).fadeOut(200);
        setTimeout(() => {
            $(loader_class).remove();
            body.removeAttr('style');
        }, 200)
    }
}

function goToResponseBlock(block, offset = 70) {
    $(window).scrollTop(block.offset().top - offset);
}

function alertNTF(type, text) {
    return '<div class="alert alert-' + type + '">' + text + '</div>';
}