<?php
namespace Teto\PHPChecker\php_files;

use Teto\PHPChecker\FileChecker;

final class NamespaceOKClassTest extends \Teto\PHPChecker\TestCase
{
    public function getChecker()
    {
        return new FileChecker(__DIR__ . '/NamespaceOKClass.php', 'Teto\PHPChecker\php_files', __DIR__);
    }
    
    public function test_requireNamespace()
    {
        $this->getChecker()->requireNamespace();
    }

    /**
     * @expectedException Teto\PHPChecker\Exception\ClassError
     */
    public function test_hasNClasses_0()
    {
        $this->getChecker()->hasNClasses(0);
    }

    public function test_hasNClasses_1()
    {
        $this->getChecker()->hasNClasses(1);
    }

    public function test_hasNoInline()
    {
        $this->getChecker()->hasNoInline();
    }
}
