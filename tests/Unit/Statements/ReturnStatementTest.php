<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Statements\ReturnStatement;

test('can be compile to php', function () {
    $content = 'return 42;';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);
    expect($parser->parseStatement()->compile())
        ->toBe('return 42;');

    $content = 'return;';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);
    expect($parser->parseStatement()->compile())
        ->toBe('return;');
});

test('can parse return without value', function () {
    $engine = new BrickEngine();
    $content = 'return;';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $result = $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ReturnStatement::class)
        ->and($result->return)
        ->toBeTrue()
        ->and($result->value)
        ->toBeNull();
});

test('can parse return with literal value', function () {
    $engine = new BrickEngine();
    $content = 'return 42;';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $result = $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ReturnStatement::class)
        ->and($result->return)
        ->toBeTrue()
        ->and($result->value->data)
        ->toBe(42);
});

test('can parse return with expression', function () {
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('x', 10)
        ->setVariable('y', 20);

    $content = 'return x + y;';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $result = $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ReturnStatement::class)
        ->and($result->return)
        ->toBeTrue()
        ->and($result->value->data)
        ->toBe(30);
});

test('can parse return with function call', function () {
    $engine = new BrickEngine();
    $engine->context->setFunction('test', fn () => 42);
    $content = 'return test();';

    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseStatement();
    $result = $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ReturnStatement::class)
        ->and($result->return)
        ->toBeTrue()
        ->and($result->value->data)
        ->toBe(42);
});

test('can parse return with array', function () {
    $engine = new BrickEngine();
    $content = 'return [1, 2, 3];';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $result = $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ReturnStatement::class)
        ->and($result->return)
        ->toBeTrue()
        ->and($result->value->data[0]->data)
        ->toBe(1)
        ->and($result->value->data[1]->data)
        ->toBe(2)
        ->and($result->value->data[2]->data)
        ->toBe(3);
});
