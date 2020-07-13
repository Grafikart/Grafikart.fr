<?php

namespace App\Infrastructure\Importer;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportedFile extends UploadedFile
{
    public function __construct(string $path)
    {
        $originalName = pathinfo($path, PATHINFO_BASENAME);
        parent::__construct($path, $originalName, null, null, false);
    }

    /**
     * Moves the file to a new location.
     *
     * @return File A File object representing the new file
     *
     * @throws FileException if, for any reason, the file could not have been moved
     */
    public function move(string $directory, string $name = null)
    {
        if ($this->isValid()) {
            $target = $this->getTargetFile($directory, $name);
            set_error_handler(function ($type, $msg) use (&$error) {
                $error = $msg;
            });
            $moved = copy($this->getPathname(), $target);
            restore_error_handler();
            if (!$moved) {
                throw new FileException(sprintf('Could not move the file "%s" to "%s"', $this->getPathname(), $target));
            }
            @chmod($target, 0666 & ~umask());

            return $target;
        }

        throw new FileException($this->getErrorMessage());
    }

    public function isValid()
    {
        return true;
    }
}
