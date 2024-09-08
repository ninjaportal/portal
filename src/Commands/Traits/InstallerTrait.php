<?php

namespace NinjaPortal\Portal\Commands\Traits;

use Illuminate\Console\Command;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @mixin Command
 */
trait InstallerTrait
{

    /**
     * Updates the Vite configuration file's input array with new entries.
     *
     * This method reads the existing Vite configuration file (`vite.config.js`),
     * located at the base path of the project, to find the `input` array within it.
     * It then adds the specified inputs to this array if they are not already present.
     * After updating the array, the modified configuration is written back to the file.
     *
     * @param mixed ...$inputs Variable number of input entries to add to the Vite config.
     * @return false|int Returns false if the `input` array could not be found or updated,
     *                   or the number of bytes that were written to the file otherwise.
     */
    private function updateViteInputs(...$inputs): false|int
    {
        $configPath = base_path('vite.config.js');
        $config = file_get_contents($configPath);

        // Find the input array in the config
        $pattern = '/input: \[(.*?)\]/s';
        preg_match($pattern, $config, $matches);

        if (isset($matches[1])) {
            $currentInputs = explode(',', $matches[1]);
            $currentInputs = array_map('trim', $currentInputs);

            // Add new inputs
            foreach ($inputs as $input) {
                $inputString = "'$input'";
                if (!in_array($inputString, $currentInputs)) {
                    $currentInputs[] = $inputString;
                }
            }

            // Update the config
            $newInputs = implode(', ', $currentInputs);
            $newConfig = preg_replace($pattern, "input: [$newInputs]", $config);

            return file_put_contents($configPath, $newConfig);
        }
        return false;
    }


    /**
     * Replace a given string within a given file.
     *
     * @param string $search
     * @param string $replace
     * @param string $path
     * @return false|int
     */
    protected function replaceInFile(string $search, string $replace, string $path): false|int
    {
        return file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }


    /**
     * Determine if the given Composer package is installed.
     *
     * @param string $package
     * @return bool
     */
    protected function hasComposerPackage(string $package): bool
    {
        $packages = json_decode(file_get_contents(base_path('composer.json')), true);

        return array_key_exists($package, $packages['require'] ?? [])
            || array_key_exists($package, $packages['require-dev'] ?? []);
    }


    /**
     * Update the "package.json" file.
     *
     * @param callable $callback
     * @param bool $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, bool $dev = true): void
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * Install the given Composer Packages as "dev" dependencies.
     *
     * @param string|array<string> $packages
     * @return bool
     */
    protected function requireComposerDevPackages(string|array $packages): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = [$this->phpBinary(), $composer, 'require', '--dev'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require', '--dev'],
            is_array($packages) ? $packages : func_get_args()
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
                ->setTimeout(null)
                ->run(function ($type, $output) {
                    $this->output->write($output);
                }) === 0;
    }


    /**
     * Removes the given Composer Packages as "dev" dependencies.
     *
     * @param string|array<string> $packages
     * @return bool
     */
    protected function removeComposerDevPackages(array|string $packages): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = [$this->phpBinary(), $composer, 'remove', '--dev'];
        }

        $command = array_merge(
            $command ?? ['composer', 'remove', '--dev'],
            is_array($packages) ? $packages : func_get_args()
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
                ->setTimeout(null)
                ->run(function ($type, $output) {
                    $this->output->write($output);
                }) === 0;
    }

    /**
     * Get the path to the appropriate PHP binary.
     *
     * @return string
     */
    protected function phpBinary(): string
    {
        return (new PhpExecutableFinder())->find(false) ?: 'php';
    }


}
