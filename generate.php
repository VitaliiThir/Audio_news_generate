<?

use Bitrix\Main\Application;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');
?>

<?php
$request = Application::getInstance()->getContext()->getRequest();

if (is_ajax() && $request->getPost('action') == 'audio-news' && $request->getPost('required') == 'success') {

    $iblock_id = 9;
    $news_id = trim($request->getPost('news_id'));
    $required_id = false;
    $news_link = '';

    $required_news_id = CIBlockElement::GetList(
        array(),
        array('IBLOCK_ID' => $iblock_id, 'ID' => $news_id),
        false, false,
        array('ID', 'DETAIL_PAGE_URL')
    );
    while ($required_news_el = $required_news_id->GetNext()) {
        if (isset($required_news_el)) {
            $required_id = true;
            $news_link = $required_news_el['DETAIL_PAGE_URL'];
        }
    }

    if ($required_id) {
        $auth = 'AQVNzaGSSw31_-LMWHeijYrOMIph4O9jw8enKrOd';
        $text = $request->getPost('news_text');
        $voice = $request->getPost('voice');
        $gender = $request->getPost('gender');
        $url = "https://tts.api.cloud.yandex.net/speech/v1/tts:synthesize";
        $post = "text=" . urlencode($text) . "&lang=ru-RU&voice=" . $gender . "&emotion=" . $voice;
        $headers = ['Authorization: Api-Key ' . $auth, 'Cache-Control: no-cache, no-store, must-revalidate, max-age=0'];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if ($post != false) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            print "Error: " . curl_error($ch);
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            $decodedResponse = json_decode($response, true);
            echo "<pre>";
            echo "Error code: " . $decodedResponse["error_code"] . "\r\n";
            echo "Error message: " . $decodedResponse["error_message"] . "\r\n";
            echo "</pre>";
        } else {
            $abs_path = $_SERVER['DOCUMENT_ROOT'];
            $base_folder = '/upload/audio';
            $file_name = "news_$news_id.ogg";
            $tmp_path = $base_folder . '/tmp/';
            $tmp_file = 'news_' . time() . '.ogg';

            if (file_exists($abs_path . $tmp_path)) {
                foreach (glob($abs_path . $tmp_path . '*') as $file) {
                    unlink($file);
                }
            }

            if ($request->getPost('news-listen') == 'listen') {
                file_put_contents($abs_path . $tmp_path . $tmp_file, $response);
                echo json_encode([
                    'STATUS' => 'listen',
                    'TEXT' => $tmp_path . $tmp_file
                ]);
            } else {
                $month = date('m_Y');
                $folder = $base_folder . '/' . $month;
                $file_value = $folder . '/' . $file_name;

                if (!is_dir($abs_path . $folder)) {
                    mkdir($abs_path . $folder);
                }

                file_put_contents($abs_path . $file_value, $response);
                $set_text = CIBlockElement::SetPropertyValues($news_id, $iblock_id, $file_value, "AUDIO_FILE");

                echo json_encode([
                    'STATUS' => 'success',
                    'TEXT' => "Файл успешно создан!<br>Перейти к <a href="$news_link">новости</a>"
                ]);

            }
        }
        curl_close($ch);
    } else {
        echo json_encode([
            'STATUS' => 'error',
            'TEXT' => '<b>Ошибка!</b><br>Новости с таким <b>ID</b> не существует.'
        ]);
    }
} else {
    return false;
}