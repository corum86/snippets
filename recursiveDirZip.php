<?php $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('mediafiles'));
foreach($iter as $file) {
    if ($file->getFilename() == '.') {
        $path = substr($file->getPath(), 0, strripos($file->getPath(), '/'));
        $file_name = snake_case(end(explode('/', $file->getPath()))) . '.zip';
        $path_file = $file->getPath() . '/' . $file_name;
        zipData($file->getPath() . '/', $path_file);
    }
}

/**
 * @param string $source
 * @param string $destination
 * @return bool
 */
function zipData($source, $destination) {
    if (extension_loaded('zip') === true) {
        if (file_exists($source) === true) {
            $zip = new ZipArchive();
            if ($zip->open($destination, ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE) === true) {
                $source = realpath($source);
                if (is_dir($source) === true) {
                    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($files as $file) {
                        $file = realpath($file);
                        if (is_dir($file) === true) {
                            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                        } else if (is_file($file) === true && strpos($file, '.zip') === FALSE) {
                            $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                        }
                    }
                } else if (is_file($source) === true && strpos($source, '.zip') === FALSE) {
                    $zip->addFromString(basename($source), file_get_contents($source));
                }
            }
            return $zip->close();
        }
    }
    echo "extension not loaded";
    return false;
}
