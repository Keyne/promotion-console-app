<?php

namespace Tests\Command;

use App\Component\AppConfigInterface;
use App\Component\Exception\AlertMessageException;
use App\Component\Storage\Storage;
use App\Component\Storage\StorageInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StorageTest extends KernelTestCase
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->config = include dirname(__FILE__) . '/../../config/config.php';
    }

    public function buildStorage(): StorageInterface
    {
        if ($this->storage instanceof StorageInterface) {
            return $this->storage;
        }
        $this->storage = $this->storage = new Storage($this->config[AppConfigInterface::DEFAULT_BASE_DIR], 'database.json');
        $this->storage->clear();
        return $this->storage;
    }

    public function testStorage(): void
    {
        $storage = $this->buildStorage();
        $storage
            ->addOrUpdate(['id' => '123-555', 'first_name' => 'Keyne Viana', 'email' => 'keyneviana@gmail.com'])
            ->save()
            ->addOrUpdate(['id' => '123-555', 'first_name' => 'Keyne Viana Silva', 'email' => 'keyneviana@gmail.com'])
            ->save();

        $user = $storage->get(0);

        $this->assertArrayHasKey('first_name', $user);
        $this->assertContains('Keyne Viana Silva', $user['first_name']);

        $this->assertEquals(1, $storage->count());

        $storage->delete(0);

        try {
            $storage->get(0);
        } catch (AlertMessageException $e) {
            $this->assertInstanceOf(AlertMessageException::class, $e);
        }
    }
    
    public function testFinish(): void
    {
        $storage = $this->buildStorage()->clear();
        $this->assertInstanceOf(StorageInterface::class, $storage);
    }
}
