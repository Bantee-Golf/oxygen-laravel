<?php

// Global functions with oxygen package

if (!function_exists('has_feature'))
{
	/**
	 *
	 * Check if a given feature is enabled in the application
	 * To edit features, see `config/features.php`
	 *
	 * @param $featureSlug
	 *
	 * @return \Illuminate\Config\Repository|mixed
	 */
	function has_feature($featureSlug)
	{
		// if the string doesn't start with `features.`, append it
		if (strpos($featureSlug, 'features.') !== 0) {
			$featureSlug = 'features.' . $featureSlug;
		}

		return (config($featureSlug, false));
	}
}