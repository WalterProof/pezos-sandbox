<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Form;

use function Safe\json_decode;
use function Safe\json_encode;
use Symfony\Component\Form\DataTransformerInterface;

class StringToJsonTransformer implements DataTransformerInterface
{
    /**
     * Transform a json object to a string.
     *
     * @param Json|null $json
     *
     * @return string
     */
    public function transform($json)
    {
        if (null === $json) {
            return '';
        }

        return json_encode($json);
    }

    /**
     * Transform a string to a json object.
     *
     * @param string $string
     *
     * @return object
     */
    public function reverseTransform($string)
    {
        if (!$string) {
            return null;
        }

        return json_decode($string, true);
    }
}
