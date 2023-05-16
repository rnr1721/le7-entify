<?php

declare(strict_types=1);

namespace Core\Entify;

use Core\Entify\Interfaces\RenderSingleInterface;
use Core\Entify\Interfaces\RenderRepoInterface;
use Core\Entify\Interfaces\EntityInterface;
use Core\Entify\Interfaces\EntityHandlerInterface;
use Core\Entify\Interfaces\EntityHandlersInterface;
use function count,
             array_merge;

/**
 * Entity. This class we get from DataProvider by running getEntity() method.
 * Inside this class launched DefaultHandler and own handlers
 */
class EntityMain implements EntityInterface
{

    /**
     * Entity handlers list object
     * It give entity handlers list for processing.
     * You can add own handlers
     * @var EntityHandlersInterface
     */
    protected EntityHandlersInterface $entityHandlers;

    /**
     * Data - the data from DataProvider
     * @var array|null
     */
    protected array|null $data = [];

    /**
     * Some info from DataProvider
     * @var array|null
     */
    protected array|null $info = null;

    /**
     * Store first info state
     * @var array|null
     */
    protected array|null $infoBackup = null;

    /**
     * Error messages list
     * @var array
     */
    protected array $errors = [];

    /**
     * Store first errors state
     * @var array
     */
    protected array $errorsBackup = [];

    /**
     * Current result
     * @var array|null
     */
    protected array|null $result = null;

    /**
     * EntityMain class created inside DataProvider.
     * All arguments from this constructor - from DataProviderInterface.
     * @param EntityHandlersInterface $entityHandlers Prepared list of handlers
     * @param array $data Entity data
     * @param array|null $info Some info
     * @param array|null $errors Errors from DataProvider
     */
    public function __construct(
            EntityHandlersInterface $entityHandlers,
            array $data,
            array|null $info = null,
            array|null $errors = null
    )
    {
        $this->data = $data;
        $this->info = $info;
        $this->infoBackup = $info;
        if ($errors) {
            $this->errorsBackup = $errors;
            $this->mergeErrors($errors);
        }

        $this->entityHandlers = $entityHandlers;
        $this->result = $this->process($this->data);
    }

    /**
     * @inheritdoc
     */
    public function refresh(): self
    {
        $this->errors = $this->errorsBackup;
        $this->info = $this->infoBackup;
        $this->result = $this->process($this->data);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function renderRepo(RenderRepoInterface $renderer): mixed
    {
        $data = $this->export();
        return $renderer->generate($data, $this->info, $this->errors);
    }

    /**
     * @inheritdoc
     */
    public function renderOne(RenderSingleInterface $renderer, int $index = 0): mixed
    {
        $data = $this->exportOne($index);
        return $renderer->generate($data, $this->info, $this->errors);
    }

    /**
     * @inheritdoc
     */
    public function export(): array|null
    {
        return $this->result;
    }

    /**
     * @inheritdoc
     */
    public function exportOne(int $index = 0): array|null
    {
        $result = $this->export();
        if (is_array($result) && isset($result[$index])) {
            return $result[$index];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getErrors(): array|null
    {
        if (count($this->errors) === 0) {
            return null;
        }
        return $this->errors;
    }

    private function mergeErrors(array $errors): void
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    private function process(array|null $data): array|null
    {

        if (!$data || $this->errors) {
            return null;
        }

        $handlers = $this->entityHandlers->getHandlers();

        /** @var EntityHandlerInterface $handler */
        foreach ($handlers as $handler) {
            $data = ($data === null ? null : $handler->handle($data, $this->info));
            if ($handler->isNeedRefreshInfo()) {
                $this->info = $handler->getInfo();
            }
            if ($handler->getErrors() || $data === null) {
                /** @var array $errors */
                $errors = $handler->getErrors();
                $this->mergeErrors($errors);
                $handler->clearErrors();
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getInfo(): array|null
    {
        return $this->info;
    }

    /**
     * @inheritdoc
     */
    public function getHandlers(): EntityHandlersInterface
    {
        return $this->entityHandlers;
    }

}
