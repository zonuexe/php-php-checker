<?php
namespace Teto\PHPChecker\php_files;

use Teto\PHPChecker\FileChecker;

final class hello_world_Test extends \Teto\PHPChecker\TestCase
{
    public function getChecker()
    {
        return new FileChecker(__DIR__ . '/hello_world.php', 'Teto\PHPChecker\php_files', __DIR__);
    }
    
    /**
     * @expectedException Teto\PHPChecker\Exception\NamespaceError
     */
    public function test_requireNamespace()
    {
        $this->getChecker()->requireNamespace();
    }

    public function test_hasNClasses_0()
    {
        $this->getChecker()->hasNClasses(0);
    }

    /**
     * @expectedException Teto\PHPChecker\Exception\ClassError
     */
    public function test_hasNClasses_1()
    {
        $this->getChecker()->hasNClasses(1);
    }

    /**
     * @expectedException Teto\PHPChecker\Exception\InlineExistsError
     */
    public function test_hasNoInline()
    {
        $this->getChecker()->hasNoInline();
    }
}
