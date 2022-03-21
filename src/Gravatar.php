<?php

declare(strict_types=1);

namespace Baraja\Gravatar;


use Baraja\EmailType\Email;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;
use Nette\Utils\Validators;

class Gravatar
{
	private ?string $defaultIcon = null;

	private ?Cache $cache = null;


	public function __construct(?string $defaultIcon = null, ?string $cacheDir = null)
	{
		if ($defaultIcon !== null && Validators::isUrl($defaultIcon) === false) {
			throw new \InvalidArgumentException(sprintf('URL "%s" is not in valid format.', $defaultIcon));
		}

		$this->defaultIcon = $defaultIcon;

		if (class_exists(Cache::class)) {
			$cacheDir ??= sprintf('%s/gravatar/%s', sys_get_temp_dir(), md5(__DIR__));
			FileSystem::createDir($cacheDir);
			$this->cache = new Cache(new FileStorage($cacheDir), 'gravatar');
		}
	}


	public function getIcon(string $email, ?int $size = null): string
	{
		$email = Email::normalize($email);
		$hash = md5($email);

		$params = [];
		if ($size !== null) {
			if ($size < 1 || $size > 5000) {
				throw new \InvalidArgumentException('Size must be in interval <1, 5000>.');
			}
			$params['s'] = $size;
		}
		if ($this->defaultIcon !== null) {
			$params['d'] = $this->defaultIcon;
		}

		return sprintf(
			'https://www.gravatar.com/avatar/%s%s',
			urlencode($hash),
			$params !== [] ? sprintf('?%s', http_build_query($params)) : '',
		);
	}


	public function getUserInfo(string $email): GravatarResponse
	{
		$email = Email::normalize($email);
		$hash = md5($email);

		$cache = $this->cache?->load($hash);
		if ($cache === null) {
			try {
				$payload = FileSystem::read(sprintf('https://en.gravatar.com/%s.php', urlencode($hash)));
				$response = unserialize($payload);
				if (is_array($response) === false) {
					throw new \InvalidArgumentException('Invalid response format.');
				}
			} catch (\Throwable $e) {
				throw new \InvalidArgumentException(sprintf('User "%s" does not exist.', $email), $e->getCode(), $e);
			}

			$this->cache?->save(
				$hash,
				$response,
				[
					Cache::EXPIRE => '60 minutes',
					Cache::TAGS => [$email, 'user', 'gravatar'],
				]
			);
			$cache = $response;
		}

		return new GravatarResponse($cache);
	}
}
