<?php

namespace AppBundle\Service;

class HashGenerator
{
    /**
     * Generate hash of given length
     *
     * @param $length
     *
     * @return string
     */
    public function generate($length)
    {
        return bin2hex(random_bytes($length/2));
    }
}
