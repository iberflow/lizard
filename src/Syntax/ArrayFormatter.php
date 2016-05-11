<?php

namespace Iber\Lizard\Syntax;

use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\Pattern;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use Funivan\PhpTokenizer\Strategy\Strict;
use Funivan\PhpTokenizer\Token;

/**
 * Class ArrayFormatter
 *
 * @package  Iber\Lizard\Syntax
 */
class ArrayFormatter
{

    /**
     * @var \Funivan\PhpTokenizer\Collection
     */
    protected $collection;

    /**
     * @param $code
     */
    public function __construct($code)
    {
        $this->collection = Collection::createFromString($code);
    }

    /**
     * @param $key
     * @param $items
     */
    public function append($key, $items)
    {

        (new Pattern($this->collection))->apply(function (QuerySequence $checker) use ($key, $items) {

            $key = $checker->strict(Strict::create()->valueLike('!^(\'|")' . $key . '\\1$!'));

            $checker->possible(T_WHITESPACE);
            $checker->strict('=>');
            $checker->possible(T_WHITESPACE);

            $openBracket = $checker->strict('[');
            $checker->moveToToken($openBracket);
            $section = $checker->section('[', ']');

            if ($checker->isValid()) {
                # add comma if needed
                $index = $section->count() - 2; // move to latest token
                $lastToken = $section[$index];

                if ($lastToken->getType() == T_WHITESPACE) {
                    $lastToken = $section[($index - 1)];
                }

                // add a comma if needed
                if ($lastToken->getValue() != ',') {
                    $lastToken->appendToValue(',');
                }

                $tokens = $this->renderItemTokens($items);

                $lastToken->appendToValue($tokens);
            }

        });

    }

    /**
     * @return string
     */
    public function toString()
    {
        return (string)$this->collection;
    }

    /**
     * @param $items
     * @return array
     */
    protected function renderItemTokens($items)
    {
        $tokens = [];

        $isNumeric = $this->isNumeric($items);

        foreach ($items as $key => $item) {
            $tokens[] = "\n" . str_repeat(' ', 8);

            if (!$isNumeric) {
                $tokens[] = "'" . $key . "'";
                $tokens[] = ' ';
                $tokens[] = '=>';
                $tokens[] = ' ';
            }

            $tokens[] = $item;
            $tokens[] = ',';
        }

        return join('', $tokens);
    }

    /**
     * @param $items
     * @return bool
     */
    protected function isNumeric($items)
    {
        return isset($items[count($items) - 1]);
    }
}