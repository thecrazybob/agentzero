<?php
namespace hexydec\agentzero;

class platforms {

	public static function get() {
		$fn = [
			'platformspace' => function (string $value) : array {
				$parts = \explode(' ', $value, 2);
				return [
					'platform' => $parts[0],
					'platformversion' => $parts[1] ?? null
				];
			},
			'platformlinux' => function (string $value) : array {
				$parts = \explode('/', $value, 2);
				return [
					'type' => 'human',
					'category' => 'desktop',
					'kernel' => 'Linux',
					'platform' => $parts[0],
					'platformversion' => $parts[1] ?? null
				];
			},
			'platformwindows' => function (string $value) : array {
				$mapping = [
					'5.0' => '2000',
					'5.1' => 'XP',
					'5.2' => 'XP',
					'6.0' => 'Vista',
					'6.1' => '7',
					'6.2' => '8',
					'6.3' => '8.1',
					'10.0' => '10'
				];
				$version = null;
				foreach (['Windows NT ', 'Windows '] AS $item) {
					if (($pos = \mb_stripos($value, $item)) !== false) {
						$version = \explode(' ', \mb_substr($value, $pos + \mb_strlen($item)))[0];
						break;
					}
				}
				return [
					'type' => 'human',
					'category' => 'desktop',
					'kernel' => 'Windows NT',
					'platform' => 'Windows',
					'platformversion' => $mapping[$version] ?? $version
				];
			}
		];

		return [

			// platforms
			'Windows NT ' => [
				'match' => 'any',
				'categories' => $fn['platformwindows']
			],
			'Windows Phone' => [
				'match' => 'start',
				'categories' => function (string $value) : array {
					$version = \mb_substr($value, 14);
					return [
						'type' => 'human',
						'category' => 'mobile',
						'platform' => 'Windows Phone',
						'platformversion' => $version,
						'kernel' => \intval($version) >= 8 ? 'Windows NT' : 'Windows CE'
					];
				}
			],
			'Win98' => [
				'match' => 'start',
				'categories' => fn () : array => [
					'type' => 'human',
					'category' => 'desktop',
					'architecture' => 'x86',
					'bits' => 32,
					'kernel' => 'MS-DOS',
					'platform' => 'Windows',
					'platformversion' => '98'
				]
			],
			'Windows' => [
				'match' => 'any',
				'categories' => $fn['platformwindows']
			],
			'Mac OS X ' => [
				'match' => 'any',
				'categories' => function (string $value) : array {
					$version = \str_replace('_', '.', \mb_substr($value, \mb_stripos($value, 'Mac OS X') + 9));
					$register = \intval(\explode('.', $version)[1]) >= 6 ? 64 : null;
					return [
						'type' => 'human',
						'category' => 'desktop',
						'kernel' => 'Linux',
						'platform' => 'MacOS',
						'platformversion' => $version,
						'bits' => $register
					];
				}
			],
			'CrOS' => [
				'match' => 'start',
				'categories' => function (string $value) : array {
					$parts = \explode(' ', $value);
					return [
						'type' => 'human',
						'category' => 'desktop',
						'platform' => 'Chrome OS',
						'platformversion' => $parts[2] ?? null
					];
				}
			],
			'Kindle/' => [
				'match' => 'start',
				'categories' => fn (string $value) : array => [
					'type' => 'human',
					'category' => 'ebook',
					'platform' => 'Kindle',
					'platformversion' => \mb_substr($value, 7)
				]
			],
			'Macintosh' => [
				'match' => 'start',
				'categories' => $fn['platformspace']
			],
			'Ubuntu/' => [
				'match' => 'start',
				'categories' => $fn['platformlinux']
			],
			'Mint/' => [
				'match' => 'start',
				'categories' => $fn['platformlinux']
			],
			'SUSE/' => [
				'match' => 'start',
				'categories' => $fn['platformlinux']
			],
			'Hat/' => [
				'match' => 'start',
				'categories' => function (string $value, int $i, array $tokens) use ($fn) : ?array {
					if ($tokens[--$i] === 'Red') {
						return $fn['platformlinux']('Red '.$value);
					}
					return null;
				}
			],
			'Darwin/' => [
				'match' => 'start',
				'categories' => $fn['platformlinux']
			],
			'Fedora/' => [
				'match' => 'start',
				'categories' => $fn['platformlinux']
			],
			'CentOS/' => [
				'match' => 'start',
				'categories' => $fn['platformlinux']
			],
			'Rocky/' => [
				'match' => 'start',
				'categories' => $fn['platformlinux']
			],
			'ArchLinux' => [
				'match' => 'exact',
				'categories' => fn () : array => [
					'type' => 'human',
					'category' => 'desktop',
					'kernel' => 'Linux',
					'platform' => 'Arch',
				]
			],
			'Arch' => [
				'match' => 'exact',
				'categories' => fn (string $value) : array => [
					'type' => 'human',
					'category' => 'desktop',
					'kernel' => 'Linux',
					'platform' => $value,
				]
			],
			'Fuchsia' => [
				'match' => 'exact',
				'categories' => function (string $value, int $i, array $tokens) : array {
					$os = \explode(' ', $tokens[++$i], 2);
					return [
						'type' => 'human',
						'category' => 'mobile',
						'kernel' => 'Zircon',
						'platform' => $value,
						'platformversion' => isset($os[1]) && \strspn($os[1], '0123456789.-_', \strlen($os[0])) === \strlen($os[1]) ? $os[1] : null
					];
				}
			],
			'Maemo' => [
				'match' => 'exact',
				'categories' => function (string $value, int $i, array $tokens) : array {
					$os = \explode(' ', $tokens[++$i], 2);
					return [
						'type' => 'human',
						'category' => 'mobile',
						'kernel' => 'Linux',
						'platform' => $value,
						'platformversion' => isset($os[1]) && \strspn($os[1], '0123456789.-_', \strlen($os[0])) === \strlen($os[1]) ? $os[1] : null
					];
				}
			],
			'Android' => [
				'match' => 'start',
				'categories' => function (string $value, int $i, array $tokens) : ?array {
					$os = \explode(' ', $value, 2);
					$device = \explode(' Build/', $tokens[++$i], 2);
					return [
						'platform' => $os[0],
						'platformversion' => $os[1] ?? null,
						'device' => $device[0] === '' ? null : $device[0],
						'build' => $device[1] ?? null
					];
				}
			],
			'Linux' => [
				'match' => 'start',
				'categories' => [
					'kernel' => 'Linux'
				]
			],
			'X11' => [
				'match' => 'exact',
				'categories' => function (string $value, int $i, array $tokens) : array {
					$os = \explode(' ', $tokens[++$i], 2);
					return [
						'type' => 'human',
						'category' => 'desktop',
						'kernel' => 'Linux',
						'platform' => $os[0],
						'platformversion' => isset($os[1]) && \strspn($os[1], '0123456789.-_', \strlen($os[0])) === \strlen($os[1]) ? $os[1] : null
					];
				}
			],
			'Version/' => [
				'match' => 'start',
				'categories' => fn (string $value) : array => [
					'platformversion' => \mb_substr($value, 8)
				]
			]
		];
	}
}