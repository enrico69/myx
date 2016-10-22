<?php
/**
 * Tests regarding the app.utils.utils service
 * @author Eric COURTIAL
 */
namespace tests\AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UtilsTest extends KernelTestCase {

    private $utilsService;
    static $container;
    
    protected function setUp()
    {
        self::bootKernel();
        self::$container = static::$kernel->getContainer();
        
        $this->utilsService = self::$container->get('app.utils.utils');
    }
    
    public function testCalculateNumberOfPagesForPagination() {
        $this->assertEquals(4, $this->utilsService->calculateNumberOfPagesForPagination(20, 5));
        $this->assertEquals(5, $this->utilsService->calculateNumberOfPagesForPagination(21, 5));
        $this->assertEquals(3, $this->utilsService->calculateNumberOfPagesForPagination(20, 7));
    }
    
    public function testIsDevEnvironment() {
        $this->assertFalse($this->utilsService->isDevEnvironment());
        $this->assertTrue(self::$container->getParameter('kernel.environment') == 'test');
    }

}