<?php

namespace AhsanDev\Support;

class Flash
{
    /**
     * Get the path to a versioned Vite file.
     *
     * @param  string  $message
     * @param  string  $type
     * @return \Illuminate\Support\HtmlString|string
     *
     * @throws \Exception
     */
    public function __invoke($message, $type = 'success')
    {
        // dd('invoke');

        echo '<script type="text/javascript">jsFunction();</script>';

        info('Invoke '.__CLASS__."\n");

        return new static;
    }

    public function error()
    {
        // dd('error');
    }

    public function __destruct()
    {
        info('Destroying '.__CLASS__."\n");
    }
}
