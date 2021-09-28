<?php

namespace Genius257\ViewFileLanguageServer\ViewFile;

//use \App\ArrayTokenScanner;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\File\LocalFile;

class ComponentReflection {
    protected $fqsen;
    protected $workspace;
    protected $projectFactory;
    protected $arrayTokenScanner;

    /**
     * @param string $fqsen     component class fqsen
     * @param string $workspace the workspace path
     */
    public function __construct(string $fqsen, string $workspace) {
        $this->fqsen = $fqsen;
        $this->workspace = $workspace;
        $this->projectFactory = ProjectFactory::createInstance();
        $this->arrayTokenScanner = new ArrayTokenScanner();
    }

    public function fqsenToClass(string $fqsen) {
        $fqsen = trim($fqsen, '\\');
        $file = Autoload::resolveFqsen($fqsen, $this->workspace);
        if (!$file) {
            throw new \Exception("$fqsen was not found");
        }

        //do {
            $projectFiles = [new LocalFile($file)];
            /** @var \phpDocumentor\Reflection\Php\Project */
            $project = $this->projectFactory->create('Component match', $projectFiles);
            $files = $project->getFiles() ?? null;
            if (!array_key_exists($file, $files)) {
                throw new \Exception("Resolved file not found: $file");
            }
            $classes = $files[$file]->getClasses();
            $fqsen = '\\'.$fqsen;
            if (!array_key_exists($fqsen, $classes)) {
                throw new \Exception("Could not find class \"$fqsen\" in file: \"$file\"");
            }
            $class = $classes[$fqsen];
            //var_dump($class);
        //} while ($class);
        return $class;
    }

    /**
     * Get available implied and actual setters available on the Component
     */
    public function getSetters() {
        $fqsen = $this->fqsen;
        $setters = [];
        $propertiesPropertyFound = false;
        do {
            //var_dump($fqsen);
            $class = $this->fqsenToClass($fqsen);
            $methods = $class->getMethods();
            foreach ($methods as $method) {
                if (preg_match('/^set([A-Z0-9].*)$/', $method->getName(), $matches) && !array_key_exists($matches[1], $setters)) {
                    $setters[lcfirst($matches[1])] = null;
                }
            }
            if (!$propertiesPropertyFound) {
                $properties = $this->classGetProperty($class, "properties");
                if ($properties) {
                    $properties = $properties->getDefault();
                    $properties = $this->arrayTokenScanner->scan($properties);
                    foreach ($properties as $propertyKey => $propertyValue) {
                        if (!array_key_exists($propertyKey, $setters)) {
                            $setters[$propertyKey] = $propertyValue;
                        }
                    }
                    $propertiesPropertyFound = true;
                }
            }
            $fqsen = strval($class->getParent());
        } while ($fqsen);
        return $setters;
    }

    protected function classHasProperty() {
        //
    }

    protected function classGetProperty($class, string $name, bool $static = false) {
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            if ($property->getName() === $name) {
                return $property;
            }
        }

        return null;
    }
}