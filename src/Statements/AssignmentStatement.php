<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Expressions\IdentifierExpression;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;

class AssignmentStatement extends Node implements StatementInterface
{
    public function __construct(ExpressionInterface $left, ExpressionInterface $right)
    {
        parent::__construct([
            'type' => 'ASSIGNMENT',
            'left' => $left,
            'right' => $right,
        ]);
    }

    public function run(Context $context): ExecutionResult
    {
        $this->assertType($this->left, IdentifierExpression::class);
        $identifier = $this->left->value;
        $value = $this->right->run($context);
        $context->variables[$identifier] = $value;
        return new ExecutionResult($value);
    }
}
