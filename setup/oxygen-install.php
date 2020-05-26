<?php

/**
 *
 *	Setup the engine.
 *
 */
function initialize() {
	$args = getopt(null, ["run:", 'path:']);

	$allowedCommands = [
		'add-repositories',
		'set-local-repo',
	];

	// set default command
	$command = 'add-repositories';

	if (isset($args['run'])) {
		if (in_array($args['run'], $allowedCommands, true)) {
			$command = $args['run'];
		}
	}

	// run the commands
	switch ($command) {
		case 'add-repositories':
			add_repositories_to_composer_json();
			break;
		case 'set-local-repo':
			$path = '../../Oxygen';
			if (!empty($args['path'])) {
				$path = $args['path'];
			}
			if (!file_exists($path)) {
				throw new \Exception("File {$path} doesn't exist. Give a valid path with `--path` flag.");
			}
			replace_oxygen_with_a_local_repo($path);
			break;
		default:
			echo 'Nothing to do. Give a command to run with `--run` flag';
			break;
	}
}

/**
 *
 * Get an array of composer.json
 *
 * @return array
 */
function get_composer_json () {

	if (!file_exists('composer.json')) {
		throw new \Exception("composer.json not found in path " . getcwd());
	}

	$localConfig = file_get_contents('composer.json');

	return json_decode($localConfig, true);
}

/**
 *
 * Write composer.json file
 *
 * @param $jsonArray
 */
function write_composer_json($jsonArray) {
	file_put_contents('composer.json', json_encode($jsonArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}

/**
 *
 *	Get the list of repository locations and merge to composer.json
 *
 */
function add_repositories_to_composer_json()
{
	// merge the existing composer.json contents with a stub file
	$remoteConfig = file_get_contents('https://bitbucket.org/elegantmedia/oxygen-installer/raw/1bc5fbcbff7f3402e54d4a419de7d2324483a2d1/src/oxygen-composer.json');

	$localJson  = get_composer_json();
	$remoteJson = json_decode($remoteConfig, true);

	$mergedJson = array_merge($localJson, $remoteJson);

	write_composer_json($mergedJson);
}

/**
 * @param $localRepoPath
 * @return bool
 */
function replace_oxygen_with_a_local_repo($localRepoPath)
{
	$json = get_composer_json();
	if (!isset($json['repositories'])) {
		return false;
	}

	$repos = [];

	foreach ($json['repositories'] as $item)
	{
		if (isset($item['url']))
		{
			if ($item['url'] === 'git@bitbucket.org:elegantmedia/oxygen-laravel.git')
			{
				$item['type'] = 'path';
				$item['url'] = $localRepoPath;
				$item['symlink'] = true;
			}
		}
		$repos[] = $item;
	}
	$json['repositories'] = $repos;

	// set the branch to dev master
	$json['require']['emedia/oxygen'] = '@dev';

	write_composer_json($json);

	return true;
}


// fire the engine
initialize();