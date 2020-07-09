<?php

namespace App\Core;

interface OptionInterface
{
    public function get(string $key): ?string;

    public function set(string $key, string $value): void;

    public function delete(string $key): void;
}
