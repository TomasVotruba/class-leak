<?php

namespace ClassLeak202411\Illuminate\Contracts\Filesystem;

interface Cloud extends Filesystem
{
    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     */
    public function url($path);
}
