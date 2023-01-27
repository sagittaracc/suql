<?php

namespace suql\syntax;

class PhpInput
{
    final public function get()
    {
        return file_get_contents('php://input');
    }
}