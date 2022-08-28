<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;
$create_request_id = $_REQUEST['audio_create_id'];
$create_request_txt = $_REQUEST['audio_create_txt'];
$create_request_url = $_REQUEST['audio_create_url'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_TEMPLATE_PATH ?>/assets/build/img/favicon/favicon-32x32.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <title>Наше время - Создание аудио-подкаста</title>
</head>
<body>
<main class="audio pt-5 pb-5">
    <div class="container">
        <div class="audio-content">
            <?
            if (!$USER->IsAuthorized()) {
                echo '<div class="alert alert-warning">У вас нет доступа к этому разделу.<br><a href="/">Перейти на сайт.</a></div>';
                die();
            }
            ?>
            <div class="title page-title mb-5 border-bottom">
                <h1>Создание аудио-новости</h1>
            </div>
            <a href="/" type="button" class="btn btn-success mb-3">Перейти на главную</a>
            <a href="/news/" type="button" class="btn btn-success mb-3">В раздел новостей</a>
            <a href="<?= $create_request_url ?>" type="button" class="btn btn-success mb-3">Вернуться к этой новости</a>
            <hr>
            <div class="server-response"></div>
            <br>
            <form class="audio-form">
                <input type="hidden" name="action" value="audio-news">
                <div class="form-element mb-4">
                    <label for="news_id"
                           class="form-label h4">ID новости:<span class="star-required">*</span></label>
                    <input type="text"
                           name="news_id"
                           class="form-control"
                           id="news_id"
                           style="max-width: 100px"
                        <? if ($create_request_id != '') echo 'value="' . $create_request_id . '"' ?>
                    >
                </div>
                <p><b>Качество переданного текста:</b></p>
                <ul>
                    <li>Для передачи слов-омографов, используйте <b>«+»</b> перед ударной гласной: з<b>+</b>амок, зам<b>+</b>ок;</li>
                    <li>Чтобы отметить паузу между словами используйте <b>«-»</b>;</li>
                    <li>Для лучшего качества расставляйте точки и запятые.</li>
                </ul>
                <div class="form-element mb-4">
                    <label for="news_text" class="form-label h4">Текст новости:<span class="star-required">*</span></label>
                    <textarea name="news_text"
                              class="form-control"
                              id="news_text"
                              rows="15"
                              placeholder="*Пример:
В 2022-ом году в Ростовской области намерены отремонтировать в многоквартирных домах более 400 подъездов."
                    ><? if ($create_request_txt != '') echo $create_request_txt ?></textarea>
                </div>
                <div class="mb-4" style="max-width: 200px">
                    <label class="form-label h4" for="gender">Имя (пол)</label>
                    <select class="form-select" name="gender" id="gender">
                        <option selected value="filipp">Филипп</option>
                        <option value="alena">Алёна</option>
                    </select>
                </div>
                <div class="mb-4" style="max-width: 200px">
                    <label class="form-label h4" for="voice">Голос (тон)</label>
                    <select class="form-select" name="voice" id="voice">
                        <option selected value="neutral">Нейтральный</option>
                        <option value="good">Радостный</option>
                        <option value="evil">Раздражённый</option>
                    </select>
                </div>
                <p><span class="star-required"><b>*</b></span> <i>Поля, обязательные для заполнения.</i></p>
                <div class="d-flex justify-content-end audio-btns">
                    <audio src="#" class="audio-test me-3" style="display: none" controls>Ваш браузер не поддерживает аудио элементы.</audio>
                    <button class="btn btn-info audio-submit btn-listen me-2" name="news-listen" value="listen" type="submit">Протестировать</button>
                    <button class="btn btn-success audio-submit" type="submit">Создать аудио файл</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script src="<?= SITE_TEMPLATE_PATH ?>/assets/build/js/libs/jquery.min.js"></script>
<script src="<?= SITE_TEMPLATE_PATH ?>/assets/build/js/libs/jquery.validate.min.js"></script>
<script src="./script.js"></script>
</body>
</html>