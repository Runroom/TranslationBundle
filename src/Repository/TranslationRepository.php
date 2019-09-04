<?php

declare(strict_types=1);

/*
 * This file is part of the TranslationBundle.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\TranslationBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Runroom\TranslationBundle\Entity\Translation;

class TranslationRepository
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $query = $builder
            ->select('translation')
            ->from('TranslationBundle:Translation', 'translation')
            ->getQuery();

        return $query->getResult();
    }

    public function findByKey(string $key): ?Translation
    {
        $builder = $this->entityManager->createQueryBuilder();
        $query = $builder
            ->select('translation')
            ->from('TranslationBundle:Translation', 'translation')
            ->where('translation.key = :key')
            ->setParameter('key', $key)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function importCatalogues(array $catalogues): void
    {
        $locales = array_keys($catalogues);
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            foreach ($catalogues[$locales[0]] as $key => $text) {
                $translation = $this->findByKey($key);

                if (null === $translation) {
                    $translation = new Translation();
                    $translation->setKey($key);
                }

                if (false === $translation->getModified() || null === $translation->getModified()) {
                    foreach ($locales as $locale) {
                        if (\array_key_exists($key, $catalogues[$locale])) {
                            $translation->translate($locale)->setValue($catalogues[$locale][$key]);
                        }
                    }

                    $translation->mergeNewTranslations();
                    $this->entityManager->persist($translation);
                }
            }

            $this->entityManager->flush();
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }
}
