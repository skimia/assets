<?php


class DumpCollectionsCommandTest extends TestCase
{
    use \Skimia\Foundation\Testing\Traits\CommandTrait;

    protected function getGeneratedFilePath()
    {
        return app()['path.storage'].'/framework/assets.generation.scanned--test.php';
    }

    protected function getDirectories()
    {
        return [
            __DIR__.'/scan' => [
                'max_depth' => 3,
            ],
        ];
    }

    protected function getMockedScanner($path, $minuse = 1){

        $scannerMock = Mockery::mock(\Skimia\Assets\Scanner\Scanner::class.'[getScannedPath]', [app()])->shouldAllowMockingProtectedMethods();

        $scannerMock->shouldReceive('getScannedPath')->atLeast()->times($minuse)->andReturn($path);

        return $scannerMock;
    }

    protected function getMockedCommand($scannerMock = null,$directories = [], $minuse = 1){

        $commandMock = Mockery::mock(\Skimia\Assets\Console\Commands\DumpCollectionsCommand::class.'[getScanner,getDirectories]')->shouldAllowMockingProtectedMethods();

        if(isset($scannerMock))
            $commandMock->shouldReceive('getScanner')->atLeast()->times($minuse)->andReturn($scannerMock);

        $commandMock->shouldReceive('getDirectories')->atLeast()->times($minuse)->andReturn($directories);
        return $commandMock;

    }
    public function testCommand()
    {
        app()->register(\Skimia\Assets\AssetsServiceProvider::class);

        $scannerMock = $this->getMockedScanner($this->getGeneratedFilePath());

        $this->assertFalse($scannerMock->loadScanned());

        $commandMock = $this->getMockedCommand($scannerMock,$this->getDirectories());

        //var_dump(Cache::get('skimia.assets.collections.builded', []));
        $this->invokeCommandWithPrompt($commandMock);

        $this->assertTrue($this->getCommandOutput()->contains('angularjs'));
        //verifie si la question a été posée
        $this->assertTrue($this->getCommandOutput()->contains('<ask>Update Assets'));

        $this->assertTrue(File::exists($this->getGeneratedFilePath()));

        require $this->getGeneratedFilePath();

        $this->assertArrayHasKey('js-stack', Assets::group('default')->getCollections());

        File::delete($this->getGeneratedFilePath());
    }

    public function testEmptyCommand()
    {
        $commandMock = $this->getMockedCommand(null,[]);
        $this->commandOutput = null;

        $this->invokeCommandWithPrompt($commandMock);

        $this->assertTrue($this->getCommandOutput()->contains('no directories'));
    }

    public function testRemoveCommand()
    {
        $scannerMock = $this->getMockedScanner($this->getGeneratedFilePath());


        $commandMock = $this->getMockedCommand($scannerMock,[__DIR__.'/emptyscans']);

        Cache::forever('skimia.assets.collections.builded', ['angularjs', 'jquery']);
        //var_dump(Cache::get('skimia.assets.collections.builded', []));
        $this->invokeCommandWithPrompt($commandMock);

        $this->assertTrue($this->getCommandOutput()->contains('removed collections'));
        //verifie si la question a été posée
        $this->assertTrue($this->getCommandOutput()->contains('<ask>Update Assets'));

        $this->assertTrue(File::exists($this->getGeneratedFilePath()));

        File::delete($this->getGeneratedFilePath());
    }


    public function testUnknown()
    {
        $scannerMock = $this->getMockedScanner($this->getGeneratedFilePath());


        $commandMock = $this->getMockedCommand($scannerMock,[__DIR__.'/undefined']);


        $this->setExpectedException('Exception');
        $this->invokeCommandWithPrompt($commandMock);

        File::delete($this->getGeneratedFilePath());
    }

    public function testRedundant()
    {
        $scannerMock = $this->getMockedScanner($this->getGeneratedFilePath());


        $commandMock = $this->getMockedCommand($scannerMock,[__DIR__.'/circular']);


        $this->setExpectedException('Exception');
        $this->invokeCommandWithPrompt($commandMock);

        File::delete($this->getGeneratedFilePath());
    }

    public function testCopy()
    {
        $scannerMock = $this->getMockedScanner($this->getGeneratedFilePath());


        $commandMock = $this->getMockedCommand($scannerMock,[__DIR__.'/copy']);

        app()['config']->set('assets.copy_mode', 'copy');

        $this->invokeCommandWithPrompt($commandMock);

        $path = public_path(app()['config']->get('assets.collections_dir', 'collections').'/js-stack-dark-glow--testing');

        $this->assertTrue(File::exists($path.'/css'));

        File::deleteDirectory($path);
        File::delete($this->getGeneratedFilePath());


        app()['config']->set('assets.copy_mode', 'symlink');

        $this->invokeCommandWithPrompt($commandMock);

        $path = public_path(app()['config']->get('assets.collections_dir', 'collections').'/js-stack-dark-glow--testing');

        $this->assertTrue(is_link($path.'/css'));

        File::deleteDirectory($path);
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
