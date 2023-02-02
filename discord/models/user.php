<?php
namespace models;
class user
{
    public int $id;
    public string $username;
    public ?string $display_name;
    public string $avatar;
    public ?string $avatar_decoration;
    public int $discriminator;
    public ?int $public_flags;
    public int $flags;
    public ?string $banner;
    public string $banner_color;
    public int $accent_color;
    public string $locale;
    public bool $mfa_enabled;
    public int $premium_type;
}
