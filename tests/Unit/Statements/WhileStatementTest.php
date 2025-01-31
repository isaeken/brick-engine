<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Statements\WhileStatement;

test('can be compile to php', function () {
    $content = 'while (x < 10) { result = true; }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseStatement()->compile())
        ->toBe('while ($x < 10) {$result = true;}');
});

test('can parse basic while loop', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('x', 0);

    $content = 'while (x < 10) { x = x + 1; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(WhileStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(10);
});

test('can parse while loop with complex condition', function () {
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('x', 0)
        ->setVariable('y', 10);

    $content = 'while (x < 10 && y > 0) { x = x + 1; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(WhileStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(10);
});

test('can parse while loop with multiple statements', function () {
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('x', 0)
        ->setVariable('y', 0)
        ->setVariable('z', 0);

    $content = 'while (x < 10) { 
        x = x + 1;
        y = x + 2;
        z = y + 1;
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(WhileStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(10)
        ->and($engine->context->variables['y']->data)
        ->toBe(12)
        ->and($engine->context->variables['z']->data)
        ->toBe(13);
});

test('can parse nested while loops', function () {
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('x', 0)
        ->setVariable('y', 0);

    $content = 'while (x < 10) { 
        while (y < 5) {
            y = y + 1;
        }
        x = x + 1;
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement()->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ExecutionResult::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(10)
        ->and($engine->context->variables['y']->data)
        ->toBe(5);
});
