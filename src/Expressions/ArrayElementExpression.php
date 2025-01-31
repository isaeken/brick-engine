<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class ArrayElementExpression extends Node implements ExpressionInterface
{
    public function __construct(bool $spread, ExpressionInterface|null $key, ExpressionInterface $value)
    {
        parent::__construct([
            'type' => 'ARRAY_ELEMENT',
            'spread' => $spread,
            'key' => $key,
            'value' => $value,
        ]);
    }

    public function run(Runtime $runtime, Context $context): Value
    {
        parent::run($runtime, $context);

        $key = $this->key ? $this->key->run($runtime, $context) : null;
        $value = $this->value->run($runtime, $context);

        return new Value($context, ValueType::ArrayElement, [
            'spread' => $this->spread,
            'key' => $key,
            'value' => $value,
        ]);
    }

    public function compile(): string
    {
        $prefix = '';
        $key = $this->key ? $this->key->compile() : null;
        $value = $this->value->compile();

        if ($this->spread) {
            $prefix .= '...';
        }

        if ($key) {
            return "{$prefix}{$key} => $value";
        }

        return $prefix.$value;
    }
}
