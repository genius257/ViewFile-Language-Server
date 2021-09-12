<?php
declare(strict_types = 1);

namespace Genius257\ViewFileLanguageServer\Index;

class StubsIndex extends Index
{
    /**
     * Reads the serialized StubsIndex from disk
     *
     * @return self
     */
    public static function read()
    {
        return unserialize(file_get_contents(__DIR__ . '/../../stubs'));
    }

    /**
     * Serializes and saves the StubsIndex
     *
     * @return void
     */
    public function save()
    {
        file_put_contents(__DIR__ . '/../../stubs', serialize($this));
    }
}
