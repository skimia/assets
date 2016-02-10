<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetsCollectionScannerTest extends TestCase
{
    protected function getScanner(){
        return new \Skimia\Assets\Scanner\Scanner($this->app);
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testScannerDetectFile()
    {
        $scanner = $this->getScanner();

        $file = $scanner->getScannedPath();
        if(file_exists($file))
            unlink($file);
        file_put_contents($file,'<?php define(\'TEST_SCANNER\',true);');

        $this->assertTrue($scanner->isScanned());

        $scanner->loadScanned();

        $this->assertTrue(defined('TEST_SCANNER'));

        unlink($file);

        $this->assertFalse($scanner->isScanned());

    }

    public function testScanFiles()
    {
        $scanner = $this->getScanner();

        $scanner->setDirectoriesToScan([__DIR__.'/scan']);

        $definition = $this->invokeMethod($scanner,'getOrderedFileDefinitions');

        $this->assertCount(3,$definition);

        $first = $definition[0];
        $second = $definition[1];
        $last = $definition[2];

        $this->assertEquals('js-stack',$first['name']);
        $this->assertEquals('js-stack-dark-glow',$last['name']);

        $merged = $this->invokeMethod($scanner,'mergeFiles',[$definition]);

        $this->assertArrayHasKey('jquery',$merged);
        $this->assertArrayHasKey('angularjs',$merged);
        $this->assertArrayHasKey('js-stack',$merged);

        $jsStackCollection = $merged['js-stack'];

        $this->assertContains('js-stack-dark-glow#css/jsstack.dark-glow.css',$jsStackCollection);

    }
    
     /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
