<?php

declare(strict_types=1);
namespace Baraja\Gravatar;
use Nette\Utils\Validators;

class Gravatar
{
	private string $email;
	private string $hash;

	public function getIcon(string $email): string
	{

		if (!Validators::isEmail($email)) {
			throw new InvalidArgumentException("invalid email");
		}

		$email = trim( $email );
		$email = strtolower( $email );

		$this->email = $email;
		$this->hash = md5( $email );

		return "https://www.gravatar.com/avatar/".$this->hash;
	}

	public function getUserInfo(string $email): void
	{
		if (!Validators::isEmail($email)) {
			throw new InvalidArgumentException("invalid email");
		}

		$email = trim( $email );
		$email = strtolower( $email );

		$this->email = $email;
		$this->hash = md5( $email );

		$url = 'https://en.gravatar.com/'.$this->hash.'.php';
		$data = unserialize((string) file_get_contents($url));

		print_r($data);
	}
}

class GravatarResponse
{
	private string $id;
	private string $hash;
	private string $preferredUsername;
	private string $thumbnailUrl;
	private string $givenName;
	private string $familyName;
	private string $formatted;
	private string $displayName;
	private string $aboutMe;
	private array $urls;
	//urls (array<string, string> ve tvaru URL => popis)

}
