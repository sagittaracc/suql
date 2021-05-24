<?php

namespace suql\syntax;

interface Model
{
    public function query();
    public function table();
}