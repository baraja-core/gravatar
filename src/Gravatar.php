<?php

declare(strict_types=1);

namespace Baraja\Gravatar;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;
use Nette\Utils\Validators;


class Gravatar
{
	private ?string $defaultIcon;

	private Cache $cache;


	public function __construct(?string $defaultIcon = null, ?string $cacheDir = null)
	{
		if ($defaultIcon !== null && Validators::isUrl($defaultIcon) === true) {
			$this->defaultIcon = $defaultIcon;
		}

		$cacheDir ??= sys_get_temp_dir() . '/gravatar/' . md5(__DIR__);

		FileSystem::createDir($cacheDir);

		$storage = new FileStorage($cacheDir);

		$this->cache = new Cache($storage, 'gravatar');
	}


	public function getIcon(string $email, ?int $size = null): string
	{
		$email = $this->normalizeEmail($email);
		$hash = md5($email);

		$params = [];

		if ($size !== null) {
			if ($size < 1 || $size > 5000) {
				throw new \InvalidArgumentException('Size must be in interval <1, 5000>.');
			}

			$params['s'] = $size;
		}

		if ($this->defaultIcon !== null) {
			$params['d'] = urlencode($this->defaultIcon);
		}

		return 'https://www.gravatar.com/avatar/' . urlencode($hash) . '?' . urldecode(http_build_query($params));
	}


	public function getUserInfo(string $email): GravatarResponse
	{
		$email = $this->normalizeEmail($email);
		$hash = md5($email);

		$cachedResponse = $this->cache->load($hash);

		if ($cachedResponse !== null) {
			return new GravatarResponse($cachedResponse);
		}

		try {
			$payload = FileSystem::read('https://en.gravatar.com/' . urlencode($hash) . '.php');
			$response = unserialize($payload);
			if (is_array($response) === false) {
				throw new \InvalidArgumentException('Invalid response format.');
			}
		} catch (\Throwable $e) {
			throw new \InvalidArgumentException('User "' . $email . '" does not exist.', $e->getCode(), $e);
		}

		$this->cache->save($hash, $response, [
			Cache::EXPIRE => '60 minutes',
		]);

		return new GravatarResponse($response);
	}


	private function normalizeEmail(string $email): string
	{
		$email = strtolower(trim($email));
		if (Validators::isEmail($email) === false) {
			throw new \InvalidArgumentException('E-mail "' . $email . '" is not valid.');
		}

		return $email;
	}
}
