<?php

declare(strict_types=1);

namespace Baraja\Gravatar;

use Nette\Utils\Validators;

class Gravatar
{
	private function normalizeEmail(string $email): string
	{
		if (Validators::isEmail($email) === false) {
			throw new \InvalidArgumentException('E-mail "' . $email . '" is not valid.');
		}

		$email = trim($email);

		return strtolower($email);
	}


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

		$url = 'https://en.gravatar.com/' . urlencode($hash) . '.php';

		$data = @file_get_contents($url);
		if ($data === false) {
			throw new \InvalidArgumentException('User "' . $email . '" does not exist.');
		}

		$data = unserialize($data);

		return new GravatarResponse($data);
	}
}
