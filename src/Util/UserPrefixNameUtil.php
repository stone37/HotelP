<?php

namespace App\Util;

class UserPrefixNameUtil
{
    public function prefix(string $data): string
    {
        $name = explode(' ', $data);
        $prefix = substr($name[count($name)-1],0,1);

        return strtoupper($prefix);
    }
}