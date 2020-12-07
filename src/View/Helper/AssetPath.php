<?php

namespace Dvsa\Olcs\Utils\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Asset path view helper
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class AssetPath extends AbstractHelper
{
    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Render base asset path
     *
     * @return string
     */
    public function __invoke($path = null)
    {
        $base = (!empty($this->config['asset_path']) ? $this->config['asset_path'] : '');

        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}
