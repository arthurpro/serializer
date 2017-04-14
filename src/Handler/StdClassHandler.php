<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Handler;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializationVisitorInterface;
use JMS\Serializer\TypeDefinition;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class StdClassHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');

        foreach ($formats as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'stdClass',
                'format' => $format,
                'method' => 'serializeStdClass',
            );
        }

        return $methods;
    }

    public function serializeStdClass(SerializationVisitorInterface $visitor, \stdClass $stdClass, array $type, SerializationContext $context)
    {
        $type = new TypeDefinition('stdClass');

        $classMetadata = $context->getMetadataFactory()->getMetadataForClass($type->getName());
        $visitor->startSerializingObject($classMetadata, $stdClass, $type, $context);

        foreach ((array)$stdClass as $name => $value) {
            $metadata = new StaticPropertyMetadata($type->getName(), $name, $value);
            $visitor->serializeProperty($metadata, $value, $context);
        }

        return $visitor->endSerializingObject($classMetadata, $stdClass, $type, $context);
    }
}
