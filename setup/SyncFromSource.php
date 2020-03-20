<?php
require('copy/Repo.php');
require('copy/RepoCloner.php');
require('copy/LaravelBase.php');
require('copy/LaravelUI.php');

$repos = [
    LaravelBase::class,
    LaravelUI::class
];

foreach ($repos as $repo) {
    RepoCloner::clone($repo);
}