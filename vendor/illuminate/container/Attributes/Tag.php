<?php

declare (strict_types=1);
namespace ClassLeak202411\Illuminate\Container\Attributes;

use Attribute;
use ClassLeak202411\Illuminate\Contracts\Container\Container;
use ClassLeak202411\Illuminate\Contracts\Container\ContextualAttribute;
#[Attribute(Attribute::TARGET_PARAMETER)]
final class Tag implements ContextualAttribute
{
    /**
     * @var string
     */
    public $tag;
    public function __construct(string $tag)
    {
        $this->tag = $tag;
    }
    /**
     * Resolve the tag.
     *
     * @param  self  $attribute
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return mixed
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->tagged($attribute->tag);
    }
}