<?php
declare(strict_types = 1);

namespace Genius257\ViewFileLanguageServer\ContentRetriever;

use Sabre\Event\Promise;
use function Genius257\ViewFileLanguageServer\uriToPath;

/**
 * Retrieves document content from the file system
 */
class FileSystemContentRetriever implements ContentRetriever
{
    /**
     * Retrieves the content of a text document identified by the URI from the file system
     *
     * @param string $uri The URI of the document
     * @return Promise <string> Resolved with the content as a string
     */
    public function retrieve(string $uri): Promise
    {
        return Promise\resolve(file_get_contents(uriToPath($uri)));
    }
}
