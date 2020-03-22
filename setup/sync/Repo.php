<?php

interface Repo
{
    public static function getFiles(): array;
    public static function getContent(string $file): string;
}