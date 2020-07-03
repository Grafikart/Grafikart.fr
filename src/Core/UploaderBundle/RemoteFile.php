<?php

namespace App\Core\UploaderBundle;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

/**
 * Représente un fichier qui sera uploadé à partir d'une URL
 */
class RemoteFile extends UploadedFile
{

    public function __construct(string $url)
    {
        $originalName = pathinfo($url, PATHINFO_BASENAME);
        parent::__construct($url, $originalName, null, UPLOAD_ERR_CANT_WRITE, false);
    }

    public function move(string $directory, string $name = null): File
    {
        if ($this->isValid()) {
            $targetFile = $this->getTargetFile($directory, $name);
            // On copie la source vers la sortie
            $source = fopen($this->getPathname(), 'r');
            if ($source === false) {
                throw new FileException(sprintf("Impossible d'ouvrir le fichier %s en lecture", $this->getPathname()));
            }
            $target = fopen($targetFile->getPathname(), 'w+');
            if ($target === false) {
                throw new FileException(sprintf("Impossible d'ouvrir le fichier %s en écriture", $targetFile->getPathname()));
            }
            $copied = stream_copy_to_stream($source, $target);
            fclose($source);
            fclose($target);
            unset($source, $target);
            restore_error_handler();
            if (!$copied) {
                throw new FileException(sprintf('Could not move the file "%s" to "%s"', $this->getPathname(), $targetFile->getPathname()));
            }
            return $targetFile;
        }

        throw new FileException($this->getErrorMessage());
    }

    public function isValid(): bool
    {
        return true;
    }

    public function getMimeType(): string
    {
        $ext = pathinfo($this->getPathname(), PATHINFO_EXTENSION);
        return MimeTypes::getDefault()->getMimeTypes($ext)[0];
    }

    public function getSize(): int
    {
        return 100; // On ne cherche pas à obtenir une valeur exacte
    }
}
