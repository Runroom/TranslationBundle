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

namespace Runroom\TranslationBundle\Service;

use Runroom\TranslationBundle\Entity\Translation;
use Runroom\TranslationBundle\Repository\TranslationRepository;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class TranslationCacheService implements CacheWarmerInterface, CacheClearerInterface
{
    private $repository;
    private $cache;

    public function __construct(
        TranslationRepository $repository,
        PhpArrayAdapter $cache
    ) {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function getItem(string $key): ?Translation
    {
        $translation = $this->cache->getItem($key)->get();

        if (null === $translation) {
            $translation = $this->repository->findByKey($key);

            if ($translation) {
                $this->warmUpTranslations();
            }
        }

        return $translation;
    }

    public function warmUpTranslations(): void
    {
        $translationsArray = [];
        $translations = $this->repository->findAll();

        foreach ($translations as $translation) {
            $translationsArray[$translation->getKey()] = $translation;
        }

        $this->cache->warmUp($translationsArray);
    }

    public function warmUp($cacheDirectory)
    {
        $this->warmUpTranslations();
    }

    public function clear($cacheDirectory)
    {
        $this->cache->clear();
    }

    public function isOptional()
    {
        return true;
    }
}
