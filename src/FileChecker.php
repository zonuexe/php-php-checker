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
class FileChecker
{
    /** @var string */
    private $namespace;

    /** @var string */
    private $base_dir;

    /** @var string */
    private $path;

    /** @var array */
    private $options;

    /** @var PHPFile */
    private $file;

    /**
     * @param string $path
     * @param string $namespace
     * @param string $base_dir
     */
    public function __construct($path, $namespace, $base_dir)
    {
        $this->path = $path;
        $this->file = new PHPFile($path);
        $this->namespace = $namespace;
        $this->base_dir  = $base_dir;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * @throws \DomainException
     * @throws \UnexpectedValueException The script did not use namespace
     * @throws Exception\NamespaceError
     * @return PHPChecker $this
     */
    public function requireNamespace()
    {
        if (empty($this->namespace)) {
            throw new \DomainException('Is not set $this->namespace.');
        }

        if (empty($this->base_dir)) {
            throw new \DomainException('Is not set $this->base_dir.');
        }

        if (empty($this->file->parsed[0]) || !($this->file->parsed[0] instanceof PhpParser\Node\Stmt\Namespace_)) {
            $msg = "This file doesn't have Namespace (in {$this->path})";
            throw new Exception\NamespaceError($msg);
        }

        $namespace = $this->file->parsed[0];
        $split = explode($this->base_dir, $this->file->path);

        if (count($split) !== 2 || strlen($split[0]) !== 0) {
            throw new \UnexpectedValueException;
        }

        $path = array_values(array_filter(explode('/', $split[1]), 'strlen'));
        $file = array_pop($path);

        $expected = implode('\\', array_merge([$this->namespace], $path));
        $actual   = implode('\\', $namespace->name->parts);

        if ($expected !== $actual) {
            $msg = "Expected namespace is $expected, but $actual. (in {$this->path})";
            throw new Exception\NamespaceError($msg);
        }

        return $this;
    }

    public function hasNClasses($num)
    {
        $classes = [];

        if (empty($this->file->parsed[0]) || empty($this->file->parsed[0]->stmts)) {
            if ($num === 0) { return; }

            $msg = "This file doesn't have any class. (in {$this->path})";
            throw new Exception\ClassError($msg);
        }

        foreach ($this->file->parsed[0]->stmts as $stmt) {
            if (false
                || $stmt instanceof PhpParser\Node\Stmt\Class_
                || $stmt instanceof PhpParser\Node\Stmt\Trait_
                || $stmt instanceof PhpParser\Node\Stmt\Interface_
            ) {
                $classes[] = $stmt;
            }
        }

        if ($num != count($classes)) {
            $actual = count($classes);
            $msg = "Expects $num classes, but this file has $actual classes (in {$this->path})";
            throw new Exception\ClassError();
        }

        return $this;
    }

    public function hasNoInline()
    {
        foreach ($this->file->parsed as $stmt) {
            if ($stmt instanceof PhpParser\Node\Stmt\InlineHTML) {
                throw new Exception\InlineExistsError;
            }
        }
    }

    public static function checkRecursive(\SplFileInfo $file_info, $namespace, $base_dir)
    {
        if ($file_info->isFile()) {
            $checker = new FileChecker($file_info->getPathname(), $namespace, $base_dir);
            $checker->requireNamespace()->hasNClasses(1);

            return;
        } elseif (!$file_info->isDir()) {
            throw new \LogicException;
        }

        $dir = new \FilesystemIterator($file_info->getPathname(),
            \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_PATHNAME
            | \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS
        );

        foreach ($dir as $f) {
            FileChecker::checkRecursive($f, $namespace, $base_dir);
        }
    }

    /**
     * @return bool
     */
    public static function isTestFile()
    {
        $this->file->isTestFile();
    }
}
