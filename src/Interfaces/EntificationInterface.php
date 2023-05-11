<?php

namespace Core\Entify\Interfaces;

use Core\Entify\DataProvider\Interfaces\DataProviderPaginatedInterface;
use Core\Entify\DataProvider\Interfaces\DataProviderFormInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface for Entification class
 * It can make new DataProviders.
 */
interface EntificationInterface
{

    /**
     * new Array data provider
     * @param array $data Array to process
     * @param string|array $rules Array with rules or rules name
     * @return DataProviderPaginatedInterface
     */
    public function getArrayProvider(
            array $data,
            string|array $rules
    ): DataProviderPaginatedInterface;

    /**
     * new Form data provider
     * @param ServerRequestInterface $request PSR request
     * @param string|array $rules Array with rules or rules name
     * @param string|null $uploadDir Directory for upload files
     * @param array $allowed Array with allowed mime for uploaded files
     * @param string $maxSize Max filesize as string: 2M, 10M 500K etc
     * @return DataProviderFormInterface
     */
    public function getFormProvider(
            ServerRequestInterface $request,
            string|array $rules,
            ?string $uploadDir = null,
            array $allowed = ['image/jpeg', 'image/png'],
            string $maxSize = '2M'
    ): DataProviderFormInterface;
}
