<?php

declare(strict_types=1);

namespace Core\Entify\DataProvider;

use Core\Entify\Interfaces\EntityInterface;
use Core\Entify\Interfaces\EntityOptionsInterface;
use Core\Entify\Interfaces\EntityHandlersInterface;
use Core\Entify\DataProvider\Interfaces\DataProviderFormInterface;
use Core\Entify\EntityMain;
use Psr\Http\Message\ServerRequestInterface;
use \RuntimeException;
use function ini_get,
             is_dir,
             file_exists,
             unlink,
             finfo_close,
             finfo_open,
             finfo_file,
             in_array,
             trim,
             strtolower,
             strlen;

/**
 * Data provider for forms
 */
class FormDataProvider implements DataProviderFormInterface
{

    /**
     * PSR ServerRequest
     * @var ServerRequestInterface
     */
    private ServerRequestInterface $request;

    /**
     * Entity handlers object for manage handlers queue
     * @var EntityHandlersInterface
     */
    private EntityHandlersInterface $entityHandlers;

    /**
     * Directory to copy uploaded files
     * @var string|null
     */
    private ?string $uploadDir = null;

    /**
     * Allowed mime types for uploaded files
     * @var array
     */
    private array $allowed = [];

    /**
     * Max file size in bytes
     * @var int
     */
    private int $maxSize = 0;

    public function __construct(
            EntityHandlersInterface $entityHandlers,
            ServerRequestInterface $request,
            ?string $uploadDir = null,
            array $allowed = ['image/jpeg', 'image/png'],
            string $maxSize = '2M'
    )
    {
        $this->entityHandlers = $entityHandlers;
        $this->request = $request;
        $this->uploadDir = $uploadDir;
        $this->allowed = $allowed;
        $this->maxSize = $this->convertToBytes($maxSize);
    }

    /**
     * Get entity from form
     * @return EntityInterface Form entity
     * @throws RuntimeException
     */
    public function getEntity(): EntityInterface
    {

        $data = $this->request->getParsedBody();

        if (!$data) {
            throw new RuntimeException(_('No data provided'));
        }

        if ($this->uploadDir) {

            $files = $this->request->getUploadedFiles();

            $info = [
                'files' => []
            ];

            if (count($files) !== 0) {
                if (!ini_get('file_uploads')) {
                    throw new RuntimeException('File uploads are not allowed in PHP configuration');
                }
                if (!is_writable($this->uploadDir)) {
                    throw new RuntimeException('Upload directory is not writable');
                }
            }

            foreach ($files as $name => $file) {
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $filename = $this->uploadDir . DIRECTORY_SEPARATOR . $file->getClientFilename();
                    if (!is_dir($this->uploadDir)) {
                        throw new RuntimeException('Upload directory not exists ' . $this->uploadDir);
                    }
                    if (file_exists($filename)) {
                        throw new RuntimeException('File exists in ' . $this->uploadDir);
                    }
                    $file->moveTo($filename);

                    if ($file->getSize() > ini_get('upload_max_filesize')) {
                        unlink($filename);
                        throw new RuntimeException('File size exceeds the maximum allowed size');
                    }

                    if ($file->getSize() > $this->maxSize) {
                        unlink($filename);
                        throw new RuntimeException('File size exceeds the maximum allowed size');
                    }

                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $filename);
                    finfo_close($finfo);
                    if (!in_array($mimeType, $this->allowed)) {
                        unlink($filename);
                        throw new RuntimeException('File type not allowed');
                    }

                    $info['files'][$name] = $filename;
                }
            }
        }
        return new EntityMain(
                $this->entityHandlers,
                (array) $data,
                $info ?? null
        );
    }

    /**
     * Convert to bytes of human readable params. 
     * @param string $value For example, 2M, 500K, 1G
     * @return int Size in bytes
     */
    private function convertToBytes(string $value): int
    {
        $valueTrimmed = trim($value);
        $last = strtolower($valueTrimmed[strlen($valueTrimmed) - 1]);
        $valueInt = (int) $valueTrimmed;
        switch ($last) {
            case 'g':
                $valueInt *= 1024;
            case 'm':
                $valueInt *= 1024;
            case 'k':
                $valueInt *= 1024;
        }
        return $valueInt;
    }

    public function getOptions(): EntityOptionsInterface
    {
        return $this->entityHandlers->getOptions();
    }

}
