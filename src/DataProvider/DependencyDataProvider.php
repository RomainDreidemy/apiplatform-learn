<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Dependency;
use Ramsey\Uuid\Uuid;

class DependencyDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface, ItemDataProviderInterface
{

    public function __construct(private string $rootPath){}

    private function getDepedencies()
    {
        return json_decode(file_get_contents($this->rootPath . '/composer.json'), true)['require'];
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $items = [];
        foreach ($this->getDepedencies() as $name => $version){
            $items[] = new Dependency(Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString(), $name, $version);
        }

        return $items;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        foreach ($this->getDepedencies() as $name => $version){
            if(Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString() === $id){
                return new Dependency($id, $name, $version);
            }
        }

        return null;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Dependency::class;
    }
}