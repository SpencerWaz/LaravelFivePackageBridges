<?php namespace Morrislaptop\LaravelFivePackageBridges;

use Illuminate\Support\Facades\App;
use ReflectionClass;

/**
 * Class LaravelFivePackageBridgeTrait
 *
 * Provide functions to backport methods removed from
 *
 * - https://github.com/laravel/framework/commit/3a0afc20f25ad3bed640ff1a14957f972d123cf7#commitcomment-8863884
 * - https://github.com/laravel/framework/commit/64c15a35d9578748671639748b83b21d16dbd6c2#diff-2
 *
 * @package Morrislaptop\LaravelFivePackageBridges
 */
trait LaravelFivePackageBridgeTrait {

	public function package($package, $namespace = null, $path = null)
	{
		$namespace = $this->getPackageNamespace($package, $namespace);
		$path = $path ?: $this->guessPackagePath();

		$this->loadConfigsFrom($namespace, $path . '/config/config.php');
		$this->loadViewsFrom($namespace, $path . '/views');
		$this->loadTranslationsFrom($namespace, $path . '/lang');

	}

	/**
	 * Guess the package path for the provider.
	 *
	 * @return string
	 */
	protected function guessPackagePath() {
		$path = (new ReflectionClass(get_parent_class()))->getFileName();

		return realpath(dirname($path).'/../../');
	}

	/**
	 * Determine the namespace for a package.
	 *
	 * @param  string  $package
	 * @param  string  $namespace
	 * @return string
	 */
	protected function getPackageNamespace($package, $namespace)
	{
		if (is_null($namespace))
		{
			list($vendor, $namespace) = explode('/', $package);
		}

		return $namespace;
	}

	protected function loadConfigsFrom($namespace, $path) {
		if ( $this->app['files']->exists($path) ) {
			$config = require $path;
			$this->setConfigs($namespace, $config);
		}
	}

	protected function setConfigs($namespace, $config) {
		foreach ($config as $key => $value) {
			$this->app['config']->set($namespace . '::' . $key, $value);
		}
	}

}
