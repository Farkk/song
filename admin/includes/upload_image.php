<?php
/**
 * Image Upload Handler
 * Универсальная функция для загрузки изображений
 */

/**
 * Загрузка изображения
 *
 * @param array $file - массив $_FILES для загружаемого файла
 * @param string $type - тип изображения ('singer', 'song', 'album')
 * @param int $id - ID сущности (опционально, для обновления)
 * @return array - ['success' => bool, 'message' => string, 'path' => string]
 */
function uploadImage($file, $type, $id = null) {
    $response = [
        'success' => false,
        'message' => '',
        'path' => ''
    ];

    // Проверка на ошибки загрузки
    if (!isset($file['error']) || is_array($file['error'])) {
        $response['message'] = 'Ошибка загрузки файла.';
        return $response;
    }

    // Проверка кода ошибки
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            $response['message'] = 'Файл не был загружен.';
            return $response;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $response['message'] = 'Превышен максимальный размер файла.';
            return $response;
        default:
            $response['message'] = 'Неизвестная ошибка загрузки.';
            return $response;
    }

    // Проверка размера файла (максимум 5MB)
    if ($file['size'] > 5242880) {
        $response['message'] = 'Размер файла не должен превышать 5MB.';
        return $response;
    }

    // Проверка типа файла
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    $allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif'
    ];

    if (!in_array($mimeType, $allowedTypes)) {
        $response['message'] = 'Недопустимый формат файла. Разрешены только JPG, JPEG, PNG, GIF.';
        return $response;
    }

    // Определение расширения файла
    $extension = match($mimeType) {
        'image/jpeg', 'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        default => 'jpg'
    };

    // Определение директории для загрузки
    $uploadDir = match($type) {
        'singer' => '../images/singer/',
        'song' => '../images/song/',
        'album' => '../images/album/',
        default => '../images/'
    };

    // Создание директории, если не существует
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Генерация имени файла
    if ($id) {
        // Используем ID для имени файла
        $filename = $id . '.' . $extension;
    } else {
        // Генерируем уникальное имя
        $filename = uniqid() . '.' . $extension;
    }

    $filepath = $uploadDir . $filename;

    // Перемещение загруженного файла
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        $response['message'] = 'Не удалось сохранить файл.';
        return $response;
    }

    // Относительный путь для сохранения в БД
    $relativePath = str_replace('../', '', $filepath);

    $response['success'] = true;
    $response['message'] = 'Файл успешно загружен.';
    $response['path'] = $relativePath;

    return $response;
}

/**
 * Удаление изображения
 *
 * @param string $path - путь к файлу относительно корня проекта
 * @return bool
 */
function deleteImage($path) {
    if (empty($path)) {
        return false;
    }

    $fullPath = '../' . $path;

    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }

    return false;
}

/**
 * Проверка существования изображения
 *
 * @param string $path - путь к файлу
 * @return bool
 */
function imageExists($path) {
    if (empty($path)) {
        return false;
    }

    $fullPath = '../' . $path;
    return file_exists($fullPath);
}
?>
