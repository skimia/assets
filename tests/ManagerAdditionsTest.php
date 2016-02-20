<?php


class ManagerAdditionsTest extends TestCase
{
    public function testManager()
    {
        $manager = new \Skimia\Assets\Manager([]);
        $this->assertNotFalse($this->invokeMethod($manager, 'assetIsFromCollection', ['js-stack#blabla.js']));
        $this->assertFalse($this->invokeMethod($manager, 'assetIsFromCollection', ['jquery']));
    }

    public function testLinks()
    {
        $manager = new \Skimia\Assets\Manager([]);
        $this->assertEquals('collections/js-stack/js/blabla.js', $this->invokeMethod($manager, 'buildLocalLink', ['js-stack#blabla.js', 'js']));
        //fallback to classic implementations
        $this->assertEquals('js/blabla.js', $this->invokeMethod($manager, 'buildLocalLink', ['blabla.js', 'js']));
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
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
