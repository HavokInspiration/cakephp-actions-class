<?php
namespace HavokInspiration\ActionsClass\PHPStan;

use Bake\Shell\Task\SimpleBakeTask;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypehintHelper;

class ShellPropertiesClassReflectionExtension implements PropertiesClassReflectionExtension
{

    /**
     * @param ClassReflection $classReflection Class reflection
     * @param string $propertyName Method name
     * @return bool
     */
    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        return $classReflection->isSubclassOf(SimpleBakeTask::class) && ($propertyName === 'BakeTemplate' || $propertyName === 'Test');
    }

    /**
     * @param ClassReflection $classReflection Class reflection
     * @param string $propertyName Method name
     * @return PropertyReflection
     */
    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        $object = 'Bake\Shell\Task\BakeTemplateTask';

        if ($propertyName === 'Test') {
            $object = 'Bake\Shell\Task\TestTask';
        }

        return new BakeTemplatePropertyReflection(
            $classReflection,
            new ObjectType($object, false)
        );
    }
}
