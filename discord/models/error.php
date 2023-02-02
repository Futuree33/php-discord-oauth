<?php
namespace models;
class error
{
    public function __construct(
        public ?string $error,
        public ?string $error_description,
        public ?string $message,
        public ?int $code
    ) {}
}
