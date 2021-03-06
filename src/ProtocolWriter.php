<?php
declare(strict_types = 1);

namespace Genius257\ViewFileLanguageServer;

use Genius257\ViewFileLanguageServer\Message;
use Sabre\Event\Promise;

interface ProtocolWriter
{
    /**
     * Sends a Message to the client
     *
     * @param Message $msg
     * @return Promise Resolved when the message has been fully written out to the output stream
     */
    public function write(Message $msg): Promise;
}
