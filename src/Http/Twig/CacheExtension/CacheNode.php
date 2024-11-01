<?php

namespace App\Http\Twig\CacheExtension;

use App\Http\Twig\TwigCacheExtension;
use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\CaptureNode;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

/**
 * Cache twig node.
 **/
#[YieldReady]
class CacheNode extends Node
{
    private static int $cacheCount = 1;

    /**
     * @param AbstractExpression<AbstractExpression> $key
     * @param Node<AbstractExpression>               $body
     */
    public function __construct(AbstractExpression $key, Node $body, int $lineno)
    {
        $body = new CaptureNode($body, $lineno);
        parent::__construct(['key' => $key, 'body' => $body], [], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $i = self::$cacheCount++;
        $extension = TwigCacheExtension::class;
        $templateParam = "\"{$this->getTemplateName()}\", ";

        $compiler
            ->addDebugInfo($this)
            ->write("\$twigCacheExtension = \$this->env->getExtension('{$extension}');\n")
            ->write("\$twigCacheBody{$i} = \$twigCacheExtension->getCacheValue($templateParam")
            ->subcompile($this->getNode('key'), true)
            ->raw(");\n")
            ->write("if (\$twigCacheBody{$i} !== null) { yield \$twigCacheBody{$i}; } else {\n")
            ->indent()
            ->write("\$twigCacheBody{$i} = ")
            ->subcompile($this->getNode('body'))
            ->write(";\n")
            ->write("\$twigCacheExtension->setCacheValue($templateParam")
            ->subcompile($this->getNode('key'))
            ->raw(',')
            ->raw("\$twigCacheBody{$i});\n")
            ->write("yield \$twigCacheBody{$i};\n")
            ->outdent()
            ->write("}\n");
    }
}
