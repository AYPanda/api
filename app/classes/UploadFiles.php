<?php


namespace classes;

class UploadFiles
{
    public static function uploadToPath($files, $path = "/images/")
    {
        $currentDate = time();
        $path = $_SERVER["DOCUMENT_ROOT"] . $path;
        if (!is_null($files)) {
            $countFiles = count($files["name"]);
            $pathFiles = '';
            for ($i = 0; $i < $countFiles; $i++) {
                $fileName = $currentDate.'q'.$files["name"][$i];
                $filePath = $files["tmp_name"][$i];
                if (move_uploaded_file($filePath, $path . $fileName)) {
                    $pathFiles .= $fileName.'$#%#';
                }
            }
            return $pathFiles;
        } else {
            return "empty";
        }
    }
}