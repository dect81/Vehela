<?php

class Tools
{

    public static function getIncludedFiles()
    {
        $included_files = get_included_files();
        $included = [];
        foreach ($included_files as $filePath) {
            $fileExtension = Tools::getFileExtensionFromFilePath($filePath);
            $fileName = Tools::getFileNameFromFilePath($filePath);
            $included[] = [
                'name' => $fileName,
                'extension' => $fileExtension
            ];
        }
        unset($included_files);
        return $included;
    }

    public static function checkForIncluded($fileName)
    {
        $includedFiles = Tools::getIncludedFiles();
        foreach ($includedFiles as $file) {
            if ($file['name'] == $fileName)
                return true;
        }
        return false;
    }

    public static function includeFile($filePath)
    {
        require_once($filePath);
    }

    private static function getFileNameFromFilePath($filePath)
    {
        $slashPosition = strrpos($filePath, '\\');
        $FileName = substr($filePath, $slashPosition + 1);
        $FileName = substr($FileName, 0, strlen($FileName) - 4);
        unset($filePath, $slashPosition);
        return $FileName;
    }

    private static function getFileExtensionFromFilePath($FilePath)
    {
        $extPosition = strrpos($FilePath, '.');
        $FileExtension = substr($FilePath, $extPosition + 1);
        unset($extPosition);
        return $FileExtension;
    }

}

?>