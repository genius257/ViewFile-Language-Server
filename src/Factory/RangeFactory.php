<?php

namespace Genius257\ViewFileLanguageServer\Factory;

use LanguageServerProtocol\Position;
use LanguageServerProtocol\Range;
use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\PositionUtilities;

class RangeFactory
{
    /**
     * Returns the range the node spans
     *
     * @param Node $node
     * @return self
     */
    public static function fromNode(Node $node)
    {
        $range = PositionUtilities::getRangeFromPosition(
            $node->getStart(),
            $node->getWidth(),
            $node->getFileContents()
        );

        return new Range(
            new Position($range->start->line, $range->start->character),
            new Position($range->end->line, $range->end->character)
        );
    }
}
