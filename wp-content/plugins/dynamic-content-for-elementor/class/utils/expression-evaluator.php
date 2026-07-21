<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Utils;

use DynamicOOOS\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
if (!\defined('ABSPATH')) {
    exit;
}
class ExpressionEvaluator
{
    /**
     * @var ExpressionLanguage|null
     */
    private static $instance = null;
    /**
     * @return ExpressionLanguage
     */
    public static function get_language()
    {
        $instance = self::$instance;
        if (!$instance instanceof ExpressionLanguage) {
            $instance = new ExpressionLanguage();
            self::register_functions($instance);
            self::$instance = $instance;
        }
        return $instance;
    }
    /**
     * Register custom functions for the expression language.
     *
     * @param ExpressionLanguage $lang
     * @return void
     */
    private static function register_functions(ExpressionLanguage $lang)
    {
        $lang->register('in_array', function ($str) {
            return 'false';
        }, function ($arguments, $el, $arr) {
            if (!\is_array($arr)) {
                return $el === $arr;
            }
            return \in_array($el, $arr, \true);
        });
        $lang->register('to_number', function ($str) {
            return 'false';
        }, function ($arguments, $str) {
            $dec = \filter_var($str, \FILTER_VALIDATE_INT);
            if ($dec !== \false) {
                return $dec;
            }
            $fl = \filter_var($str, \FILTER_VALIDATE_FLOAT);
            if ($fl !== \false) {
                return $fl;
            }
            return 0;
        });
    }
    /**
     * Join multiple lines of expressions with AND operator.
     *
     * @param string $expr
     * @return string
     */
    public static function and_join_lines($expr)
    {
        $lines = \preg_split('/\\r\\n|\\r|\\n/', $expr);
        if ($lines === \false) {
            $lines = [];
        }
        $lines = \array_filter($lines, function ($l) {
            return !\preg_match('/^\\s*$/', $l);
        });
        if (empty($lines)) {
            return 'true';
        }
        return '(' . \implode(')&&(', $lines) . ')';
    }
    /**
     * Evaluate an expression against field values.
     *
     * @param string              $expression The expression to evaluate.
     * @param array<string,mixed> $values     Field values keyed by field ID.
     * @return bool
     * @throws \Symfony\Component\ExpressionLanguage\SyntaxError If the expression is invalid.
     */
    public static function evaluate($expression, $values)
    {
        if (empty(\trim($expression))) {
            return \true;
        }
        $expr = self::and_join_lines($expression);
        return (bool) self::get_language()->evaluate($expr, $values);
    }
}
