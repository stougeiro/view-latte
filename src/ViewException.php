<?php declare(strict_types=1);

    namespace STDW\View\Latte;

    use Exception;


    class ViewException extends Exception
    {
        public static function temporaryDirectoryNotFound(string $temporary_directory): object
        {
            return new static("View: Directory '{$temporary_directory}' not found");
        }

        public static function fileNotFound(string $filepath): object
        {
            return new static("View: '{$filepath}' not found");
        }
    }
