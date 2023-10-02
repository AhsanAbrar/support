<?php

namespace AhsanDev\Support;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class Vite
{
    protected $isDevelopmentServerRunning;

    /**
     * Get the path to a versioned Vite file.
     *
     * @param  string  $manifestDirectory
     * @param  string  $port
     * @return \Illuminate\Support\HtmlString|string
     *
     * @throws \Exception
     */
    public function __invoke($manifestDirectory, $port, $file)
    {
        if ($this->isDevelopmentServerRunning($port)) {
            return new HtmlString(
                sprintf(
                    '<script type="module" src="%s"></script>',
                    '//localhost:'.$port.'/@vite/client'
                ).
                sprintf(
                    '<script type="module" src="%s"></script>',
                    '//localhost:'.$port.'/js/'.$file
                )
            );
        }

        static $manifests = [];

        $manifestPath = public_path($manifestDirectory.'/manifest.json');

        if (! isset($manifests[$manifestPath])) {
            if (! is_file($manifestPath)) {
                throw new Exception('The Vite manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        return new HtmlString(
            $this->getScripts($manifest['js/'.$file], $manifestDirectory).
            $this->getVendors($manifest, $manifestDirectory).
            $this->getStyles($manifest['js/'.$file], $manifestDirectory)
        );
    }

    protected function getScripts($manifest, $manifestDirectory)
    {
        return sprintf(
            '<script type="module" crossorigin src="%s"></script>',
            '/'.$manifestDirectory.'/'.$manifest['file']
        );
    }

    protected function getStyles($manifest, $manifestDirectory)
    {
        if (! isset($manifest['css'])) {
            return;
        }

        $styles = '';

        foreach ($manifest['css'] as $css) {
            $styles .= sprintf(
                '<link rel="stylesheet" type="text/css" href="%s">',
                '/'.$manifestDirectory.'/'.$css
            );
        }

        return $styles;
    }

    protected function getVendors($manifest, $manifestDirectory)
    {
        $manifestItems = array_values($manifest)[0];

        if (! isset($manifestItems['imports'])) {
            return;
        }

        $imports = '';

        foreach ($manifestItems['imports'] as $import) {
            $imports .= sprintf(
                '<link rel="modulepreload" href="%s">',
                '/'.$manifestDirectory.'/'.$manifest[$import]['file']
            );
        }

        return $imports;
    }

    /**
     * Checks if the development server is running.
     */
    public function isDevelopmentServerRunning($port): bool
    {
        try {
            if ($this->isDevelopmentServerRunning) {
                return $this->isDevelopmentServerRunning;
            }

            // First, try to check if the development server is running over HTTPS
            $response = Http::withOptions([
                'connect_timeout' => 0.1,
            ])->get('https://localhost:' . $port . '/@vite/client');

            if ($response->successful()) {
                return $this->isDevelopmentServerRunning = true;
            } else {
                return $this->isDevelopmentServerRunning = Http::withOptions([
                    'connect_timeout' => .1,
                ])->get('http://localhost:' . $port . '/@vite/client')->successful();
            }
        } catch (\Throwable $th) {
            // Handle any exceptions or errors here if needed
        }

        return false;
    }
}
