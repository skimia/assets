<?php


class DumpCollectionsCommandTest extends TestCase
{

    use \Skimia\Foundation\Testing\Traits\CommandTrait;


    protected function getGeneratedFilePath(){
        return app()['path.storage'].'/framework/assets.generation.scanned--test.php';
    }

    protected function getDirectories(){
        return [
            __DIR__.'/scan'
        ];
    }

    public function testCommand(){


        $scannerMock = Mockery::mock(\Skimia\Assets\Scanner\Scanner::class.'[getScannedPath]',[app()])->shouldAllowMockingProtectedMethods();

        $scannerMock->shouldReceive('getScannedPath')->atLeast()->times(1)->andReturn($this->getGeneratedFilePath());

        $commandMock = Mockery::mock(\Skimia\Assets\Console\Commands\DumpCollectionsCommand::class.'[getScanner,getDirectories]')->shouldAllowMockingProtectedMethods();

        $commandMock->shouldReceive('getScanner')->atLeast()->times(1)->andReturn($scannerMock);
        $commandMock->shouldReceive('getDirectories')->atLeast()->times(1)->andReturn($this->getDirectories());

        //var_dump(Cache::get('skimia.assets.collections.builded', []));
        $this->invokeCommandWithPrompt($commandMock);

        $this->assertTrue($this->getCommandOutput()->contains('angularjs'));
        //verifie si la question a été posée
        $this->assertTrue($this->getCommandOutput()->contains('<ask>Update Assets'));

        $this->assertTrue(File::exists($this->getGeneratedFilePath()));

        require $this->getGeneratedFilePath();

        $this->assertArrayHasKey('js-stack',Assets::group('default')->getCollections());


        File::delete($this->getGeneratedFilePath());
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
