<?php

declare(strict_types=1);

namespace Baraja\Gravatar;


use Nette\Utils\FileSystem;
use Nette\Utils\Validators;

class Gravatar
{
	public function getIcon(string $email): string
	{
		$email = $this->normalizeEmail($email);
		$hash = md5($email);

		return 'https://www.gravatar.com/avatar/' . urlencode($hash);
	}


	public function getUserInfo(string $email): GravatarResponse
	{
		$email = $this->normalizeEmail($email);
		$hash = md5($email);

		try {
			$payload = FileSystem::read('https://en.gravatar.com/' . urlencode($hash) . '.php');
			$response = unserialize($payload);
			if (is_array($response) === false) {
				throw new \InvalidArgumentException('Invalid response format.');
			}
		} catch (\Throwable $e) {
			throw new \InvalidArgumentException('User "' . $email . '" does not exist.', $e->getCode(), $e);
		}

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
