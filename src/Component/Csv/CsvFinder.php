<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 11/10/2018
 * Time: 21:35
 */

namespace App\Component\Csv;

use App\Component\AppConfigInterface;
use App\Component\Exception\AlertMessageException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class CsvFinder implements CsvFinderInterface
{
    /**
     * @var string
     */
    private $basedir;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var array
     */
    private $files;

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config = null)
    {
        $this->config = $config;
    }

    public function setCsvDir(string $dir): CsvFinderInterface
    {
        if (!is_dir($dir)) {
            throw new AlertMessageException('The provided directory doesn\'t exists');
        }
        $this->basedir = $dir;
        $adapter = new Local($dir);
        $this->filesystem = new Filesystem($adapter);

        return $this;
    }

    public function listFiles(): array
    {
        if (!$this->filesystem) {
            $this->setCsvDir($this->config[AppConfigInterface::DEFAULT_BASE_DIR]);
        }
        $contents = $this->filesystem->listContents('/');

        $this->files = [];
        foreach ($contents as $object) {
            if ($object['type'] === 'file') {
                $this->addFile($object['basename']);
            }
        }

        return $this->files;
    }

    public function getFileFullPath(string $file): string
    {
        return $this->basedir . DIRECTORY_SEPARATOR . $file;
    }

    private function addFile($file): bool
    {
        $fileArr = explode('.', $file);
        $ext = array_pop($fileArr);
        if (strtolower($ext) === 'csv') {
            $this->files[] = $file;
            return true;
        }
        return false;
    }
}
