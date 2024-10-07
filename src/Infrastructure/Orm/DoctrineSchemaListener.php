<?php

namespace App\Infrastructure\Orm;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

#[AsDoctrineListener(event: ToolEvents::postGenerateSchema)]
class DoctrineSchemaListener
{

    public function postGenerateSchema (GenerateSchemaEventArgs $event) {
        $table = $event->getSchema()->getTable('forum_topic');
        $table->addColumn('search_vector', 'tsvector', [
            'notnull' => false,
        ]);
        $table->addIndex(['search_vector'], 'search_idx');
    }

}
