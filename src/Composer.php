<?php

namespace Iber\Lizard;

use InvalidArgumentException;
use Symfony\Component\Process\Process;

/**
 * Class Composer
 *
 * @package  Iber\Lizard
 */
class Composer
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $dependencies;

    /**
     * Project constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException($path . ' is not a directory');
        }

        if (!is_file($path . '/composer.json')) {
            throw new InvalidArgumentException($path . '/composer.json is not in project directory.');
        }

        $this->path = $path;
        $this->dependencies = $this->readDependencies();
    }

    /**
     * @param array|null $packages
     * @return array
     */
    public function getDependencies(array $packages = null)
    {
        if (null === $packages) {
            return $this->dependencies;
        }

        $dependencies = [];

        foreach ($packages as $package) {
            if (isset($this->dependencies[$package])) {
                $dependencies[] = $package;
            }
        }

        return $dependencies;
    }

    /**
     * @return mixed
     */
    public function readFile()
    {
        $content = file_get_contents($this->path . '/composer.json');
        $json = json_decode($content, true);

        return $json;
    }

    /**
     * @return array
     */
    public function readDependencies()
    {
        $json = $this->readFile();

        if (!isset($json['require'])) {
            return [];
        }

        return $json['require'];
    }

    /**
     * @param $package
     * @return bool
     */
    public function hasDependency($package)
    {
        return isset($this->dependencies[$package]);
    }

    /**
     * @param array $add
     * @param array $remove
     */
    public function update(array $add = [], array $remove = [])
    {
        $contents = $this->readFile();

        foreach ($add as $package => $version) {
            $contents['require'][$package] = $version;
        }
        
        foreach ($remove as $package) {
            unset($contents['require'][$package]);
        }

        $this->saveFile($contents);
        $this->run();
    }

    /**
     * @param $contents
     *
     * @return int
     */
    protected function saveFile($contents)
    {
        $content = file_put_contents(
            $this->path . '/composer.json',
            json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        return $content;
    }

    /**
     * @param string $command
     */
    protected function run($command = 'update')
    {
        $process = new Process(
            $this->findComposerExecutable() . ' ' . $command,
            $this->path,
            null,
            null,
            null
        );
        
        $process->run(function ($type, $line) {});
    }

    /**
     * @return string
     */
    protected function findComposerExecutable()
    {
        if (file_exists(getcwd() . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }

        return 'composer';
    }
}
