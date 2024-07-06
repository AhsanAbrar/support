<?php

namespace AhsanDev\Support;

use Illuminate\Foundation\ViteManifestNotFoundException;
use Illuminate\Support\HtmlString;
use Exception;

class ViteNew
{
    /**
     * The name of the package.
     */
    protected string $packageName;

    /**
     * The port used for the development server.
     */
    protected int $port = 5173;

    /**
     * The main file to be processed by Vite.
     */
    protected string $file = 'main.ts';

    /**
     * The IP address of the development server.
     */
    protected string $ip = 'localhost';

    /**
     * Handle the invocation of the class.
     */
    public function __invoke(?string $param1 = null, ?string $param2 = null): HtmlString
    {
        // Set the file and IP address based on parameters.
        $this->setFileAndIp($param1, $param2);

        // Get the package name.
        $this->packageName = $this->getPackageName();

        // Check if HMR server is running and return the appropriate response.
        if ($this->isRunningHot()) {
            $this->setIpAndPort($param1, $param2);
            return $this->development();
        }

        return $this->production();
    }

    /**
     * Set the file and IP address based on the provided parameters.
     */
    protected function setFileAndIp(?string $param1, ?string $param2): void
    {
        foreach ([$param1, $param2] as $param) {
            if ($param !== null) {
                $this->updateFileOrIp($param);
            }
        }
    }

    /**
     * Update the file or IP address based on the parameter value.
     */
    protected function updateFileOrIp(string $param): void
    {
        if ($this->isIpAddress($param)) {
            $this->ip = $param;
        } else {
            $this->file = $param;
        }
    }

    /**
     * Determine if the given parameter is an IP address.
     */
    protected function isIpAddress(?string $param): bool
    {
        return $param !== null && (filter_var($param, FILTER_VALIDATE_IP) !== false || $param === 'localhost');
    }

    /**
     * Generate the production HTML string.
     */
    protected function production(): HtmlString
    {
        static $manifests = [];

        // Define the path to the Vite manifest file.
        $manifestPath = public_path("vendor/{$this->packageName}/.vite/manifest.json");

        // Load and decode the manifest file if not already cached.
        if (!isset($manifests[$manifestPath])) {
            if (!is_file($manifestPath)) {
                throw new ViteManifestNotFoundException("Vite manifest not found at: $manifestPath");
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];
        $js = $manifest["resources/js/{$this->file}"]['file'];
        $cssFiles = $manifest["resources/js/{$this->file}"]['css'] ?? [];

        // Generate the CSS link tags.
        $cssLinks = array_map(fn($cssFile) => "<link rel=\"stylesheet\" type=\"text/css\" href=\"/vendor/{$this->packageName}/{$cssFile}\" />", $cssFiles);

        // Return the complete HTML string.
        return new HtmlString(
            implode('', $cssLinks) .
            sprintf('<script type="module" crossorigin src="/vendor/%s/%s"></script>', $this->packageName, $js)
        );
    }

    /**
     * Generate the development HTML string.
     */
    protected function development(): HtmlString
    {
        // Return the development script tags.
        return new HtmlString(
            sprintf('<script type="module" src="//%s:%s/@vite/client"></script>', $this->ip, $this->port) .
            sprintf('<script type="module" src="//%s:%s/resources/js/%s"></script>', $this->ip, $this->port, $this->file)
        );
    }

    /**
     * Set the IP address and port from the hot file, unless overridden by user input.
     */
    protected function setIpAndPort(?string $param1, ?string $param2): void
    {
        // Check if user-provided IP should override the hot file value.
        $userProvidedIp = $this->isIpAddress($param1) || $this->isIpAddress($param2);

        if (!$userProvidedIp) {
            $hotFilePath = base_path("packages/{$this->packageName}/hot");
            $fileContents = file_get_contents($hotFilePath);

            // Extract and set the IP address and port from the hot file.
            $this->ip = $this->extractValueFromHotFile($fileContents, 'ip') ?? $this->ip;
            $this->port = $this->extractValueFromHotFile($fileContents, 'port') ?? $this->port;
        }
    }

    /**
     * Extract a value from the hot file contents based on a key.
     */
    protected function extractValueFromHotFile(string $fileContents, string $key): ?string
    {
        foreach (explode("\n", $fileContents) as $line) {
            if (strpos($line, "{$key}:") !== false) {
                return trim(explode(':', $line)[1]);
            }
        }

        throw new Exception(ucfirst($key) . ' not found in the hot file.');
    }

    /**
     * Get the package name from the view paths.
     */
    protected function getPackageName(): string
    {
        // Fetch the view finder from the application container.
        $viewFinder = app('view')->getFinder();
        $viewPaths = $viewFinder->getPaths();

        // Extract the package name from the view path.
        foreach ($viewPaths as $path) {
            if (preg_match('/packages\/([^\/]*)\/resources\/views/', $path, $matches)) {
                return $matches[1];
            }
        }

        throw new Exception('Package name not found.');
    }

    /**
     * Determine if the HMR server is running.
     */
    protected function isRunningHot(): bool
    {
        return file_exists(base_path("packages/{$this->packageName}/hot"));
    }
}
