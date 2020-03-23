<?php
require('sync/Repo.php');
require('sync/RepoCloner.php');
require('sync/Base.php');
require('sync/Auth.php');

$repos = [
    Base::class,
    Auth::class
];

foreach ($repos as $repo) {
    RepoCloner::clone($repo);
}