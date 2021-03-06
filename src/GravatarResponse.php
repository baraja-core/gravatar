<?php

declare(strict_types=1);

namespace Baraja\Gravatar;


final class GravatarResponse
{
	private int $id;

	private string $hash;

	private string $preferredUsername;

	private string $thumbnailUrl;

	private ?string $givenName;

	private ?string $familyName;

	private ?string $formatted;

	private ?string $displayName;

	private ?string $aboutMe;

	/** @var array<string, string> */
	private array $urls = [];


	/**
	 * @param array<string, mixed> $profile
	 */
	public function __construct(array $profile)
	{
		$this->id = (int) $profile['entry'][0]['id'];
		$this->hash = $profile['entry'][0]['hash'];
		$this->preferredUsername = $profile['entry'][0]['preferredUsername'];
		$this->thumbnailUrl = $profile['entry'][0]['thumbnailUrl'];
		$this->givenName = $profile['entry'][0]['name']['givenName'] ?? null;
		$this->familyName = $profile['entry'][0]['name']['familyName'] ?? null;
		$this->formatted = $profile['entry'][0]['name']['formatted'] ?? null;
		$this->displayName = $profile['entry'][0]['displayName'] ?? null;
		$this->aboutMe = $profile['entry'][0]['aboutMe'] ?? null;

		foreach ($profile['entry'][0]['urls'] ?? [] as $url) {
			$this->urls[(string) $url['value']] = (string) $url['title'];
		}
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function getHash(): string
	{
		return $this->hash;
	}


	public function getPreferredUsername(): string
	{
		return $this->preferredUsername;
	}


	public function getThumbnailUrl(): string
	{
		return $this->thumbnailUrl;
	}


	public function getGivenName(): ?string
	{
		return $this->givenName;
	}


	public function getFamilyName(): ?string
	{
		return $this->familyName;
	}


	public function getFormatted(): ?string
	{
		return $this->formatted;
	}


	public function getDisplayName(): ?string
	{
		return $this->displayName;
	}


	public function getAboutMe(): ?string
	{
		return $this->aboutMe;
	}


	/**
	 * @return array<string, string>
	 */
	public function getUrls(): array
	{
		return $this->urls;
	}
}
