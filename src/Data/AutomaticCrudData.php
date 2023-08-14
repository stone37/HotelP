<?php

namespace App\Data;

use App\Form\AutomaticForm;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AutomaticCrudData implements CrudDataInterface
{
    public function __construct(protected ?object $entity = null)
    {
        $reflexion = new ReflectionClass($this);
        $properties = $reflexion->getProperties(ReflectionProperty::IS_PUBLIC);
        $accessor = new PropertyAccessor();

        foreach ($properties as $property) {
            $name = $property->getName();
            /** @var ReflectionNamedType|null $type */
            $type = $property->getType();

            if ($type && UploadedFile::class === $type->getName()) {
                continue;
            }

            $accessor->setValue($this, $name, $accessor->getValue($entity, $name));
        }
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return AutomaticForm::class;
    }

    public function hydrate(): void
    {
        $reflexion = new ReflectionClass($this);
        $properties = $reflexion->getProperties(ReflectionProperty::IS_PUBLIC);

        $accessor = new PropertyAccessor();

        foreach ($properties as $property) {
            $name = $property->getName();
            $value = $accessor->getValue($this, $name);
            $accessor->setValue($this->entity, $name, $value);
        }
    }

    public function getId(): ?int
    {
        if (method_exists($this->entity, 'getId')) {
            return $this->entity->getId();
        }

        throw new RuntimeException('L\'entité doit avoir une méthode getId()');
    }
}