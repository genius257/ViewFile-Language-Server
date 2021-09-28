<?php

namespace Genius257\ViewFileLanguageServer\ViewFile;

use PHPHtmlParser\Dom;
//use PHPHtmlParser\Dom\Node\HtmlNode;
use Genius257\View\Dom\Node\HtmlNode;
use PHPHtmlParser\Options;
use Genius257\View\Dom\Parser;
use Genius257\View\View;

class ViewFileReflection {
    protected $workspace;
    protected $viewFile;

    public function __construct(string $viewFile, $workspace = null, string $viewFileContents = null) {
        $this->workspace = $workspace ?? \dirname($viewFile);
        $this->$viewFile = $viewFile;

        $this->getComponentTags($viewFileContents ?? file_get_contents($viewFile));
    }

    public function getComponentTags(string $html) {
        $dom = new Dom(new Parser());

        $options = new Options();
        $options->setCleanupInput(false);

        $dom->setOptions(
            // this is set as the global option level.
            $options
        );

        $dom->loadStr($html);
        $componentTagsChildren = $this->getComponentTagsChildren($dom->root);
        //var_dump(count($this->getComponentTagsChildren($dom->root)));
        //var_dump(get_class($componentTagsChildren[0]));

        return $dom;
    }

    protected function getComponentTagsChildren($node) {
        if (!($node instanceof HtmlNode)) {
            return [];
        }

        $componentTags = [];

        $className = $node->rawTag();

        $file = Autoload::resolveFqsen($className, $this->workspace);
        if ($file) {
            //var_dump($className);
            $componentTags[] = $node;
        }

        //$cr = ComponentReflection($className, $this->workspace);
        /*
        if (!class_exists($className)) {
            return $node;
        }
        */

        foreach($node->getChildren() as $child) {
            $componentTags = array_merge($componentTags, $this->getComponentTagsChildren($child));
            //$newChild = $this->processNode($child);
        }

        return $componentTags;
    }
}
