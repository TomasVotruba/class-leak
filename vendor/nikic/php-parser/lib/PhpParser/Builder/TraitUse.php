<?php

declare (strict_types=1);
namespace ClassLeak202504\PhpParser\Builder;

use ClassLeak202504\PhpParser\Builder;
use ClassLeak202504\PhpParser\BuilderHelpers;
use ClassLeak202504\PhpParser\Node;
use ClassLeak202504\PhpParser\Node\Stmt;
class TraitUse implements Builder
{
    /** @var Node\Name[] */
    protected $traits = [];
    /** @var Stmt\TraitUseAdaptation[] */
    protected $adaptations = [];
    /**
     * Creates a trait use builder.
     *
     * @param Node\Name|string ...$traits Names of used traits
     */
    public function __construct(...$traits)
    {
        foreach ($traits as $trait) {
            $this->and($trait);
        }
    }
    /**
     * Adds used trait.
     *
     * @param Node\Name|string $trait Trait name
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function and($trait)
    {
        $this->traits[] = BuilderHelpers::normalizeName($trait);
        return $this;
    }
    /**
     * Adds trait adaptation.
     *
     * @param Stmt\TraitUseAdaptation|Builder\TraitUseAdaptation $adaptation Trait adaptation
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function with($adaptation)
    {
        $adaptation = BuilderHelpers::normalizeNode($adaptation);
        if (!$adaptation instanceof Stmt\TraitUseAdaptation) {
            throw new \LogicException('Adaptation must have type TraitUseAdaptation');
        }
        $this->adaptations[] = $adaptation;
        return $this;
    }
    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() : Node
    {
        return new Stmt\TraitUse($this->traits, $this->adaptations);
    }
}
