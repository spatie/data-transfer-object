<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass;
use RuntimeException;

class ReflectionUtils
{
    private const SKIPPED = [
        'string',
        'int',
        'integer',
        'float',
        'bool',
        'double',
        'array',
        'object',
        'callable',
        'callback',
        'iterable',
        'void',
        'null',
        'mixed',
    ];

    /**
     * @param string $type
     * @return bool
     */
    public static function isSkipped(string $type): bool
    {
        return in_array(strtolower($type), self::SKIPPED, true);
    }

    /**
     * @param ReflectionClass $class
     * @return array
     */
    public static function getUsesAndAliases(ReflectionClass $class): array
    {
        try {
            if ($class->isAnonymous()) {
                throw new RuntimeException('Anonymous classes are not supported.');
            }
            static $cache = [];
            if (! isset($cache[$name = $class->getName()])) {
                if ($class->isInternal()) {
                    $cache[$name] = [];
                } else {
                    $code = file_get_contents($class->getFileName());
                    $cache = self::parseUsesAndAliases($code, $name) + $cache;
                }
            }

            return $cache[$name];
        } catch (\Exception $exception) {
        }

        return [];
    }

    /**
     * @param string $code
     * @param string|null $forClass
     * @return array
     */
    private static function parseUsesAndAliases(string $code, string $forClass = null): array
    {
        try {
            $tokens = token_get_all($code, TOKEN_PARSE);
        } catch (\Exception $e) {
            $tokens = [];
        }
        $namespace = $class = $classLevel = $level = null;
        $res = $uses = [];

        while ($token = current($tokens)) {
            next($tokens);
            switch (is_array($token) ? $token[0] : $token) {
                case T_NAMESPACE:
                    $namespace = ltrim(self::fetch($tokens, [T_STRING, T_NS_SEPARATOR]).'\\', '\\');
                    $uses = [];
                    break;

                case T_CLASS:
                case T_INTERFACE:
                case T_TRAIT:
                    if ($name = self::fetch($tokens, T_STRING)) {
                        $class = $namespace.$name;
                        $classLevel = $level + 1;
                        $res[$class] = $uses;
                        if ($class === $forClass) {
                            return $res;
                        }
                    }
                    break;

                case T_USE:
                    while (! $class && ($name = self::fetch($tokens, [T_STRING, T_NS_SEPARATOR]))) {
                        $name = ltrim($name, '\\');
                        if (self::fetch($tokens, '{')) {
                            while ($suffix = self::fetch($tokens, [T_STRING, T_NS_SEPARATOR])) {
                                /* @noinspection NotOptimalIfConditionsInspection */
                                if (self::fetch($tokens, T_AS)) {
                                    $uses[self::fetch($tokens, T_STRING)] = $name.$suffix;
                                } else {
                                    $tmp = explode('\\', $suffix);
                                    $uses[end($tmp)] = $name.$suffix;
                                }
                                if (! self::fetch($tokens, ',')) {
                                    break;
                                }
                            }
                        } elseif (self::fetch($tokens, T_AS)) {
                            $uses[self::fetch($tokens, T_STRING)] = $name;
                        } else {
                            $tmp = explode('\\', $name);
                            $uses[end($tmp)] = $name;
                        }
                        if (! self::fetch($tokens, ',')) {
                            break;
                        }
                    }
                    break;

                case T_CURLY_OPEN:
                case T_DOLLAR_OPEN_CURLY_BRACES:
                case '{':
                    $level++;
                    break;

                case '}':
                    if ($level === $classLevel) {
                        $class = $classLevel = null;
                    }
                    $level--;
            }
        }

        return $res;
    }

    private static function fetch(&$tokens, $take): ?string
    {
        $res = null;
        while ($token = current($tokens)) {
            [$token, $s] = is_array($token) ? $token : [$token, $token];
            if (in_array($token, (array) $take, true)) {
                $res .= $s;
            } elseif (! in_array($token, [T_DOC_COMMENT, T_WHITESPACE, T_COMMENT], true)) {
                break;
            }
            next($tokens);
        }

        return $res;
    }
}
