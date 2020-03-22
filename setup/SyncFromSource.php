<?php
require('sync/Repo.php');
require('sync/RepoCloner.php');
require('sync/LaravelBase.php');
require('sync/LaravelUI.php');

$repos = [
    LaravelBase::class,
    LaravelUI::class
];

foreach ($repos as $repo) {
    RepoCloner::clone($repo);
}