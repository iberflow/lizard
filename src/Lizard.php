<?php

namespace Iber\Lizard;

use Iber\Lizard\Syntax\ArrayFormatter;

/**
 * Class Lizard
 *
 * @package  Iber\Lizard
 */
class Lizard
{
    /**
     * @var
     */
    protected $packages = [];

    /**
     * @var
     */
    protected $projectDir;

    /**
     * Lizard constructor.
     */
    public function __construct($projectDir)
    {
        $this->packages = $this->readPackages();
        $this->projectDir = $projectDir;
    }

    /**
     * @return array
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @return array
     */
    public function readPackages()
    {
        $content = file_get_contents(__DIR__ . '/config/packages.json');
        $json = json_decode($content, true);

        if (!isset($json['packages'])) {
            return [];
        }

        return $json['packages'];
    }

    /**
     * @param $package
     * @return bool
     */
    public function hasPackage($package)
    {
        return isset($this->packages[$package]);
    }

    /**
     * @param $package
     * @return null
     */
    public function getPackage($package)
    {
        if ($this->hasPackage($package)) {
            return $this->packages[$package];
        }

        return null;
    }

    /**
     * @param $dependencies
     * @param $selection
     *
     * @return array
     */
    public function getDiff($dependencies, $selection)
    {
        $diff = ['added' => [], 'removed' => []];

        foreach ($selection as $package) {
            if (!in_array($package, $dependencies)) {
                $diff['added'][] = $package;
            }
        }

        foreach ($dependencies as $package) {
            if (!in_array($package, $selection)) {
                $diff['removed'][] = $package;
            }
        }

        return $diff;
    }

    /**
     * @param $addable
     * @param $removable
     */
    public function updateAppConfig($addable, $removable = [])
    {
        $configPath = $this->projectDir . '/config/app.php';

        $parser = new ArrayFormatter(file_get_contents($configPath));

        $providers = [];
        $facades = [];

        foreach ($addable as $package) {
            $details = $this->getPackage($package);

            $providers = array_merge($providers, isset($details['providers']) ? $details['providers'] : []);
            $facades = array_merge($facades, isset($details['facades']) ? $details['facades'] : []);
        }

        $parser->append('providers', $providers);
        $parser->append('aliases', $facades);

        file_put_contents($configPath, $parser->toString());
    }

    public function addBowerTemplate()
    {
        $directory = __DIR__ . '/stubs/bower/';

        file_put_contents($this->projectDir . '/bower.json', file_get_contents($directory . 'bower.json'));
        file_put_contents($this->projectDir . '/.bowerrc', file_get_contents($directory . '.bowerrc'));
    }

    public function addElixirTemplate()
    {
        $directory = __DIR__ . '/stubs/elixir/';

        file_put_contents($this->projectDir . '/gulpfile.js', file_get_contents($directory . 'gulpfile.js'));
    }
}
