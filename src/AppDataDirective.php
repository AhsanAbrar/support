<?php

namespace AhsanDev\Support;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Laranext\Span\Span;

class AppDataDirective
{
    /**
     * Handle the invocation of the class.
     */
    public function __invoke(): HtmlString
    {
        $className = $this->getAppDataClassName();

        if (!class_exists($className)) {
            throw new \RuntimeException("Class {$className} does not exist.");
        }

        $data = json_encode(new $className);

        return new HtmlString(
            sprintf('<script>window.AppData = %s</script>', $data)
        );
    }

    /**
     * Get the fully qualified class name for AppData.
     */
    protected function getAppDataClassName(): string
    {
        return sprintf('\\%s\\Support\\AppData', $this->getNamespace());
    }

    /**
     * Get the namespace based on the package name.
     */
    protected function getNamespace(): string
    {
        $packageName = Span::prefix();
        $providerClass = config('span.providers')[$packageName] ?? '';

        if (empty($providerClass)) {
            throw new \RuntimeException("Provider class for package {$packageName} not found in configuration.");
        }

        return Str::beforeLast($providerClass, '\\');
    }
}
