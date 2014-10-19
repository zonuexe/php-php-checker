<?php
namespace Teto\PHPChecker;

use PhpParser;

/**
 * PHP File checker
 *
 * @package   Teto\PHPChecker*
 * @copyright Copyright (c) 2014 USAMI Kenta
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @license   MIT License
 */
class PHPFile
{
    private static $parser;

    /** @var string */
    private $path;

    /** @var array */
    private $parsed;

    public function __construct($path)
    {
        $this->path = $path;
        $this->parsed = self::parse(file_get_contents($path));
    }

    public static function parse($source)
    {
        if (empty(self::$parse)) {
            self::$parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
        }

        return self::$parser->parse($source);
    }

    /**
     * @return bool
     */
    public function isTestFile()
    {
        return preg_match('@Test\.php\z@', $this->path);
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
