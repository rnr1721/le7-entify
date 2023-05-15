<?php

declare(strict_types=1);

namespace Core\Entify\Interfaces;

/**
 * Entity renderer interface. You can write your own renderer
 * This interface for render repository entities
 */
interface RenderRepoInterface
{

    /**
     * Render entity
     * @param array|null $data
     * @param array|null $info
     * @param array $errors
     * @return mixed
     */
    public function generate(
            array|null $data,
            array|null $info,
            array $errors
    ): mixed;
}
