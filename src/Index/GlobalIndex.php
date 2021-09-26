<?php
declare(strict_types = 1);

namespace Genius257\ViewFileLanguageServer\Index;

/**
 * Aggregates definitions of the project and stubs
 */
class GlobalIndex extends AbstractAggregateIndex
{
    /**
     * @var ProjectIndex
     */
    private $projectIndex;

    /**
     * @param StubsIndex   $stubsIndex
     * @param ProjectIndex $projectIndex
     */
    public function __construct(ProjectIndex $projectIndex)
    {
        $this->projectIndex = $projectIndex;
        parent::__construct();
    }

    /**
     * @return ReadableIndex[]
     */
    protected function getIndexes(): array
    {
        return [$this->projectIndex];
    }
}
