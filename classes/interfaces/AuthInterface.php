<?php

namespace ProFixS\Forms\Classes\Interfaces;

interface AuthInterface
{
    public function init();
    public function getAuthUrl(string $redirectUrl): string;
    public function getKyividAuthUrl(): string;
    public static function setRedirectUrl(string $url): void;
    public static function getRedirectUrl();
    public static function check(): bool;
    public static function getUser();
    public static function logout();
    public function auth(string $code): array;
    public static function get($key);
}
