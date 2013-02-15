<?php

namespace Yolo\Config;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    private $treeBuilder;

    public function __construct(TreeBuilder $treeBuilder)
    {
        $this->treeBuilder = $treeBuilder;
    }

    public function getConfigTreeBuilder()
    {
        return $this->treeBuilder;
    }
}
