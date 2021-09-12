<?php
declare(strict_types = 1);
namespace Genius257\ViewFileLanguageServer\Server;

use Genius257\ViewFileLanguageServerProtocol\FileChangeType;
use Genius257\ViewFileLanguageServerProtocol\FileEvent;
use Psalm\Codebase;
use Genius257\ViewFileLanguageServer\LanguageServer;

/**
 * Provides method handlers for all workspace/* methods
 */
class Workspace
{
    /**
     * @var LanguageServer
     */
    protected $server;

    /**
     * @var Codebase
     */
    protected $codebase;

    /** @var ?int */
    protected $onchange_line_limit;

    public function __construct(
        LanguageServer $server,
        Codebase $codebase,
        ?int $onchange_line_limit
    ) {
        $this->server = $server;
        $this->codebase = $codebase;
        $this->onchange_line_limit = $onchange_line_limit;
    }

    /**
     * The watched files notification is sent from the client to the server when the client
     * detects changes to files and folders watched by the language client (note although
     * the name suggest that only file events are sent it is about file system events
     * which include folders as well). It is recommended that servers register for these
     * file system events using the registration mechanism. In former implementations clients
     * pushed file events without the server actively asking for it.
     *
     * @param FileEvent[] $changes
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function didChangeWatchedFiles(array $changes): void
    {
        foreach ($changes as $change) {
            $file_path = LanguageServer::uriToPath($change->uri);

            if ($change->type === FileChangeType::DELETED) {
                $this->codebase->invalidateInformationForFile($file_path);
                return;
            }

            if (!$this->codebase->config->isInProjectDirs($file_path)) {
                return;
            }

            if ($this->onchange_line_limit === 0) {
                return;
            }

            //If the file is currently open then dont analyse it because its tracked by the client
            if (!$this->codebase->file_provider->isOpen($file_path)) {
                $this->server->queueFileAnalysis($file_path, $change->uri);
            }
        }
    }
}
