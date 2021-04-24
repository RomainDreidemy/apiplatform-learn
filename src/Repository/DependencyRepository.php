<?php


namespace App\Repository;


use App\Entity\Dependency;
use Ramsey\Uuid\Uuid;

class DependencyRepository
{
    public function __construct(private string $rootPath){}

    private function getComposerFile()
    {
        return json_decode(file_get_contents($this->rootPath . '/composer.json'), true);
    }

    private function saveComposerFile(array $content)
    {
        file_put_contents($this->rootPath . '/composer.json', json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function getDependencies()
    {
        return $this->getComposerFile()['require'];
    }

    public function findAll(): array
    {
        $items = [];
        foreach ($this->getDependencies() as $name => $version){
            $items[] = new Dependency($name, $version);
        }

        return $items;
    }

    public function find(string $uuid): ?Dependency
    {
        foreach ($this->findAll() as $dependency){
            if($dependency->getUuid() === $uuid){
                return $dependency;
            }
        }

        return null;
    }

    public function persist(Dependency $dependency)
    {
        $json = $this->getComposerFile();
        $json['require'][$dependency->getName()] = $dependency->getVersion();
        $this->saveComposerFile($json);
    }

    public function remove(Dependency $dependency)
    {
        $json = $this->getComposerFile();
        unset($json['require'][$dependency->getName()]);
        $this->saveComposerFile($json);
    }
}