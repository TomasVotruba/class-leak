<?php

declare (strict_types=1);
namespace ClassLeak202412\PhpParser\Node\Stmt;

use ClassLeak202412\PhpParser\Node\StaticVar;
use ClassLeak202412\PhpParser\Node\Stmt;
class Static_ extends Stmt
{
    /** @var StaticVar[] Variable definitions */
    public $vars;
    /**
     * Constructs a static variables list node.
     *
     * @param StaticVar[] $vars Variable definitions
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->vars = $vars;
    }
    public function getSubNodeNames() : array
    {
        return ['vars'];
    }
    public function getType() : string
    {
        return 'Stmt_Static';
    }
}
