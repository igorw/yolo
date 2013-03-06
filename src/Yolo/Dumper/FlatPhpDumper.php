<?php

namespace Yolo\Dumper;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Dumper\Dumper;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FlatPhpDumper extends Dumper
{
    public function dump(array $options = array())
    {
        return implode("\n", array(
            $this->startFile(),
            $this->addParameters(),
            $this->addServices(),
        ));
    }

    private function startFile()
    {
        return <<<EOF
<?php
EOF;
    }

    private function addParameters()
    {
        if (!$this->container->getParameterBag()->all()) {
            return '';
        }

        $parameters = $this->dumpValue($this->container->getParameterBag()->all(), 4);

        $code = <<<EOF

\$container->getParameterBag()->add($parameters);
EOF;

        return $code;
    }

    private function addService($id, $definition)
    {
        $code = <<<EOF

\$container

EOF;

        $code .= sprintf("    ->register(%s, %s)\n", $this->dumpValue($id), $this->dumpValue($definition->getClass()));
        $code .= $this->addDefinition($definition);

        $code = trim($code).";\n\n";

        return $code;
    }

    private function addDefinition(Definition $definition)
    {
        $code = '';

        foreach ($definition->getTags() as $name => $tags) {
            foreach ($tags as $attributes) {
                $code .= sprintf("    ->addTag(%s, %s)\n", $this->dumpValue($name), $this->dumpValue($attributes));
            }
        }

        if ($definition->getFile()) {
            $code .= sprintf("    ->setFile(%s)\n", $this->dumpValue($definition->getFile()));
        }

        if ($definition->getFactoryMethod()) {
            $code .= sprintf("    ->setFactoryMethod(%s)\n", $this->dumpValue($definition->getFactoryMethod()));
        }

        if ($definition->getFactoryService()) {
            $code .= sprintf("    ->setFactoryService(%s)\n", $this->dumpValue($definition->getFactoryService()));
        }

        if ($definition->getArguments()) {
            foreach ($definition->getArguments() as $argument) {
                $code .= sprintf("    ->addArgument(%s)\n", $this->dumpValue($argument));
            }
        }

        if ($definition->getProperties()) {
            foreach ($definition->getProperties() as $name => $value) {
                $code .= sprintf("    ->setProperty(%s, %s)\n", $this->dumpValue($name), $this->dumpValue($value));
            }
        }

        if ($definition->getMethodCalls()) {
            foreach ($definition->getMethodCalls() as $call) {
                list($method, $arguments) = $call;
                $code .= sprintf("    ->addMethodCall(%s, array(%s))\n",
                    $this->dumpValue($method),
                    implode(', ', array_map([$this, 'dumpValue'], $arguments)));
            }
        }

        if (ContainerInterface::SCOPE_CONTAINER !== $scope = $definition->getScope()) {
            $code .= sprintf("    ->setScope(%s)\n", $this->dumpValue($scope));
        }

        if ($callable = $definition->getConfigurator()) {
            if (is_array($callable)) {
                if ($callable[0] instanceof Reference) {
                    $callable = array($this->getServiceCall((string) $callable[0], $callable[0]), $callable[1]);
                } else {
                    $callable = array($callable[0], $callable[1]);
                }
            }

            $code .= sprintf("    ->setConfigurator(%s)\n", $this->dumpValue($callable));
        }

        return $code;
    }

    private function addServiceAlias($alias, $id)
    {
        if ($id->isPublic()) {
            return sprintf("    %s: @%s\n", $alias, $id);
        } else {
            return sprintf("    %s:\n        alias: %s\n        public: false", $alias, $id);
        }
    }

    private function addServices()
    {
        if (!$this->container->getDefinitions()) {
            return '';
        }

        $code = '';
        foreach ($this->container->getDefinitions() as $id => $definition) {
            $code .= $this->addService($id, $definition);
        }

        foreach ($this->container->getAliases() as $alias => $id) {
            $code .= $this->addServiceAlias($alias, $id);
        }

        $code = rtrim($code)."\n";

        return $code;
    }

    private function dumpValue($value, $indent = 8)
    {
        if (is_array($value)) {
            if ([] === $value) {
                return 'array()';
            }

            $php = [];

            foreach ($value as $key => $val) {
                $val = $this->dumpValue($val, $indent + 4);
                $php[] = sprintf('%s%s => %s,', str_repeat(' ', $indent), var_export($key, true), $val);
            }

            return sprintf("array(\n%s\n%s)", implode("\n", $php), str_repeat(' ', $indent - 4));
        } elseif ($value instanceof Variable) {
            // todo
        } elseif ($value instanceof Parameter) {
            return $this->getParameterCall((string) $value);
        } elseif ($value instanceof Definition) {
            // todo
            return $this->getDefinitionCall((string) $value, $value);
        } elseif ($value instanceof Reference) {
            return $this->getServiceCall((string) $value, $value);
        }

        return var_export($value, true);
    }

    private function getServiceCall($id, Reference $reference = null)
    {
        if (null !== $reference && ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE !== $reference->getInvalidBehavior()) {
            return sprintf("new Reference(%s, ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)", $this->dumpValue($id));
        }

        return sprintf("new Reference(%s)", $this->dumpValue($id));
    }

    private function getParameterCall($id)
    {
        return sprintf('%%%s%%', $id);
    }
}
