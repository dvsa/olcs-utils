<?php

namespace Dvsa\Olcs\Utils\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * AssetPath helper
 *
 * This helper generates a path for assets, allowing for a base path to be set.
 * It supports different cache busting strategies, including:
 * - None: No cache busting, just returns the asset path.
 * - Release: Appends a release version to the asset path.
 * - Timestamp: Appends the current Unix timestamp to the asset path.
 *
 * The configuration for this helper should include:
 * - `assets.base_url`: The base URL for assets.
 * - `assets.cache_busting_strategy`: The strategy to use for cache busting, which can be one of the following:
 *   - `none`: No cache busting.
 *   - `release`: Use the release version from the configuration.
 *   - `timestamp`: Use the current Unix timestamp.
 * If the `release` strategy is used, the configuration must also include:
 * - `version.release`: The hashed release version string to append to the asset path.
 */
class AssetPath extends AbstractHelper
{
    public const CACHE_BUSTING_STRATEGY_NONE = 'none';
    public const CACHE_BUSTING_STRATEGY_RELEASE = 'release';
    public const CACHE_BUSTING_STRATEGY_UNIX_TIMESTAMP = 'timestamp';

    private string $assetBasePath;
    private string $cacheBustingStrategy;
    private ?string $release;

    public function __construct(array $config)
    {
        $this->parseConfig($config);
    }

    private function parseConfig(array $config = []): void
    {
        $this->assetBasePath = $config['assets']['base_url'] ?? '';
        $cacheBustStrategy = $config['assets']['cache_busting_strategy'] ?? self::CACHE_BUSTING_STRATEGY_NONE;

        $this->cacheBustingStrategy = $this->parseCacheBustingStrategy($cacheBustStrategy);

        if ($this->cacheBustingStrategy === self::CACHE_BUSTING_STRATEGY_RELEASE) {
            $this->release = $config['version']['release'] ?? null;
            if (empty($this->release)) {
                throw new \InvalidArgumentException('Release version is required for cache busting strategy "release".');
            }
        }
    }

    private function parseCacheBustingStrategy(string $cacheBustStrategy): string
    {
        return match ($cacheBustStrategy) {
            self::CACHE_BUSTING_STRATEGY_NONE,
            self::CACHE_BUSTING_STRATEGY_RELEASE,
            self::CACHE_BUSTING_STRATEGY_UNIX_TIMESTAMP => $cacheBustStrategy,
            default => throw new \InvalidArgumentException("Invalid cache busting strategy: {$cacheBustStrategy}"),
        };
    }

    /**
     * Render asset path with optional cache busting
     *
     * @param string $path The asset path to append to the base URL
     * @param string $cacheBustingStrategy The cache busting strategy to use, if not provided the default will be used
     * @return string
     */
    public function __invoke(string $path = '', string $cacheBustingStrategy = null): string
    {
        if ($cacheBustingStrategy === null) {
            $cacheBustingStrategy = $this->cacheBustingStrategy;
        }

        $assetUrl = rtrim($this->assetBasePath, '/') . '/' . ltrim($path, '/');

        return match ($cacheBustingStrategy) {
            self::CACHE_BUSTING_STRATEGY_NONE => rtrim($assetUrl, '/'),
            self::CACHE_BUSTING_STRATEGY_RELEASE,
            self::CACHE_BUSTING_STRATEGY_UNIX_TIMESTAMP => $this->appendCacheBustingQuery($assetUrl),
            default => throw new \LogicException('Unhandled cache busting strategy'),
        };
    }

    /**
     * Append cache busting query to the asset URL
     *
     * @param string $assetUrl The asset URL to append the query to
     * @return string The asset URL with the cache busting query appended
     */
    private function appendCacheBustingQuery(string $assetUrl): string
    {
        if ($this->cacheBustingStrategy === self::CACHE_BUSTING_STRATEGY_UNIX_TIMESTAMP) {
            $versionString = (string) time();
        } else {
            $versionString = substr(hash('sha256', $this->release), 0, 12);
        }

        $query = http_build_query(['v' => $versionString]);
        $separator = (parse_url($assetUrl, PHP_URL_QUERY) === null) ? '?' : '&';

        return $assetUrl . $separator . $query;
    }
}
