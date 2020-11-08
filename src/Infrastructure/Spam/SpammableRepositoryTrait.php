<?php

namespace App\Infrastructure\Spam;

trait SpammableRepositoryTrait
{
    /**
     * Marque les contenu contenant un mot parmis une liste comme spam.
     *
     * @param string[] $words
     */
    public function flagAsSpam(array $words): int
    {
        $query = $this->createQueryBuilder('t');
        foreach ($words as $k => $word) {
            $query = $query->orWhere("t.content LIKE :word{$k}")->setParameter("word{$k}", "%{$word}%");
        }
        $query = $query->andWhere('t.spam != true');

        return $query->update()->set('t.spam', 'true')->getQuery()->execute();
    }
}
