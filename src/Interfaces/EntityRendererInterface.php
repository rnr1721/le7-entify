<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * Entity renderer interface. You can write your own renderer
 */
interface EntityRendererInterface
{

    /**
     * Render entity
     * @param array|null $data Incoming data
     * @return mixed
     */
    public function generate(array|null $data): mixed;
}
