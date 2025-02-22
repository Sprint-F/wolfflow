<?php

namespace SprintF\Bundle\Wolfflow\Context;

/**
 * Типовая реализация класса контекста действия.
 */
abstract class ContextAbstract implements ContextInterface
{
    public function jsonSerialize(): array
    {
        $vars = get_object_vars($this);

        foreach ($vars as $key => &$value) {
            $property = new \ReflectionProperty($this, $key);
            $isSensitive = !empty($property->getAttributes(\SensitiveParameter::class));
            if (!$isSensitive) {
                $constructorParams = (new \ReflectionClass($this))->getConstructor()->getParameters();
                foreach ($constructorParams as $param) {
                    if ($param->isPromoted() && $key == $param->getName()) {
                        $isSensitive = !empty($param->getAttributes(\SensitiveParameter::class));
                        break;
                    }
                }
            }
            if ($isSensitive) {
                $value = '***';
            }
        }

        return $vars;
    }
}
