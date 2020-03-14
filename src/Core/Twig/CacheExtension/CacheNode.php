<?php

namespace App\Core\Twig\CacheExtension;

use App\Core\Twig\TwigCacheExtension;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

/**
 * Cache twig node.
 **/
class CacheNode extends Node
{

    private static int $cacheCount = 1;

    /**
     * @param AbstractExpression<AbstractExpression> $key
     * @param Node<AbstractExpression> $body
     */
    public function __construct(AbstractExpression $key, Node $body, int $lineno, string $tag = null)
    {
        parent::__construct(['key' => $key, 'body' => $body], [], $lineno, $tag);
    }

    /**
     * {@inheritDoc}
     */
    public function compile(Compiler $compiler)
    {
        $i = self::$cacheCount++;
        $extension = TwigCacheExtension::class;
        $compiler
            ->addDebugInfo($this)
            ->write("\$twigCacheExtension = \$this->env->getExtension('{$extension}');\n")
            ->write("\$twigCacheBody{$i} = \$twigCacheExtension->getCacheValue(")
            ->subcompile($this->getNode('key'))
            ->raw(");\n")
            ->write("if (\$twigCacheBody{$i} !== null) { echo \$twigCacheBody{$i}; } else {\n")
            ->indent()
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("\$twigCacheBody{$i} = ob_get_clean();\n")
            ->write("\$twigCacheExtension->setCacheValue(")
            ->subcompile($this->getNode('key'))
            ->raw(',')
            ->raw("\$twigCacheBody{$i});\n")
            ->write("echo \$twigCacheBody{$i};\n")
            ->outdent()
            ->write("}\n");
    }
}
