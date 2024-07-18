<?php

namespace Scheel\InjectQueryParams\Concerns;

use Illuminate\Routing\ResolvesRouteDependencies;
use Illuminate\Validation\ValidationException;
use ReflectionParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\QueryParameterValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait InjectsQueryParamsFromAttribute
{
    use ResolvesRouteDependencies {
        transformDependency as originalTransformDependency;
    }

    /**
     * Attempt to transform the given parameter into a class instance.
     *
     * @param  array<int|string, mixed>  $parameters
     * @param  object  $skippableValue
     * @return mixed
     */
    protected function transformDependency(ReflectionParameter $parameter, $parameters, $skippableValue)
    {
        if (! empty($parameter->getAttributes(MapQueryParameter::class))) {
            /** @var \Illuminate\Http\Request $request */
            $request = app('request');
            if ($parameter->isVariadic()) {
                throw new \RuntimeException('Variadic parameters are not supported for MapQueryParameter.');
            }
            try {
                $defaultValue = $parameter->getDefaultValue();
            } catch (\ReflectionException) {
                $defaultValue = null;
            }
            $attributeInstances = array_map(
                fn (\ReflectionAttribute $attribute) => $attribute->newInstance(),
                $parameter->getAttributes()
            );
            if (($type = $parameter->getType()) && $type instanceof \ReflectionNamedType) {
                $typeName = $type->getName();
            } else {
                $typeName = null;
            }
            $metadata = new ArgumentMetadata(
                name: $parameter->name,
                type: $typeName,
                isVariadic: false,
                hasDefaultValue: $parameter->isDefaultValueAvailable(),
                defaultValue: $defaultValue,
                isNullable: $parameter->allowsNull(),
                attributes: $attributeInstances
            );
            $resolver = new QueryParameterValueResolver();
            try {
                $value = $resolver->resolve($request, $metadata);
            } catch (NotFoundHttpException) {
                // Symfony throws a 404 if an argument is missing. We'll throw a ValidationException in stead.
                throw ValidationException::withMessages([$parameter->name => ["$parameter->name is required."]]);
            }
            if (empty($value)) {
                return $defaultValue;
            }
            if (\count($value) > 1) {
                throw new \RuntimeException("Unsupported query parameter $parameter->name");
            }

            return $value[0];
        }

        return $this->originalTransformDependency($parameter, $parameters, $skippableValue);
    }
}
