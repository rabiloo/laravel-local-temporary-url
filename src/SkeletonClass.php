<?php

namespace VendorNamespace\PackageNamespace;

class SkeletonClass
{
    /**
     * Create a new Skeleton Instance.
     */
    public function __construct()
    {
        // constructor body
    }

    /**
     * Hello method.
     *
     * Print hello string.
     *
     * @param string $name
     * @return void
     */
    public function hello(string $name): void
    {
        echo "Hello, $name";
    }
}
