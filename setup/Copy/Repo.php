<?php


namespace Setup\Copy;


interface Repo
{
    public function getFiles(): array;
    public function getContent(string $file): string;
}