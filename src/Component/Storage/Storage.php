<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 12/10/2018
 * Time: 01:43
 */

namespace App\Component\Storage;

use App\Component\Exception\AlertMessageException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Storage implements StorageInterface
{
    /**
     * @var array
     */
    private $collection = [];

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $baseDir = __DIR__ . '/../../../data/';

    /**
     * @var string
     */
    private $databaseFile = 'database.json';

    public function __construct(string $baseDir = null, string $databaseFile = null)
    {
        if ($baseDir) {
            $this->baseDir = $baseDir;
        }
        if ($databaseFile) {
            $this->databaseFile = $databaseFile;
        }
        $this->loadJsonFile();
    }

    public function addOrUpdate(array $entry): StorageInterface
    {
        $id = array_search($entry['id'], array_column($this->collection, 'id'));
        if ($id !== false) {
            $this->collection[$id] = $entry;
        } else {
            $this->collection[] = $entry;
        }

        return $this;
    }

    public function get(int $index): array
    {
        if (isset($this->collection[$index])) {
            return $this->collection[$index];
        }
        throw new AlertMessageException('Record not found');
    }

    public function getAll(): array
    {
        return $this->collection;
    }

    public function delete(int $index): StorageInterface
    {
        if (isset($this->collection[$index])) {
            unset($this->collection[$index]);
        }
        return $this;
    }

    public function count(): int
    {
        return count($this->collection);
    }

    public function save(): StorageInterface
    {
        $this->filesystem->put($this->databaseFile, json_encode($this->collection));
        return $this;
    }

    private function loadJsonFile(): void
    {
        $adapter = new Local($this->baseDir);
        $this->filesystem = new Filesystem($adapter);

        if (!$this->filesystem->has($this->databaseFile)) {
            $this->filesystem->put($this->databaseFile, '[]');
        }

        $contents = $this->filesystem->read($this->databaseFile);
        $this->collection = $this->collection + json_decode($contents, true);
    }

    public function clear(): StorageInterface
    {
        $this->collection = [];
        if ($this->filesystem->has($this->databaseFile)) {
            $this->filesystem->delete($this->databaseFile);
        }
        return $this;
    }
}
