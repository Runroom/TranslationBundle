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

namespace Runroom\TranslationBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Runroom\TranslationBundle\Entity\Translation;
use Runroom\TranslationBundle\Repository\TranslationRepository;
use Runroom\TranslationBundle\Service\TranslationCacheService;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\Cache\CacheItem;

class TranslationCacheServiceTest extends TestCase
{
    public const KEY = 'item.key';

    private $cache;
    private $repository;
    private $service;

    protected function setUp(): void
    {
        $this->cache = $this->prophesize(PhpArrayAdapter::class);
        $this->repository = $this->prophesize(TranslationRepository::class);

        $this->service = new TranslationCacheService(
            $this->repository->reveal(),
            $this->cache->reveal()
        );
    }

    /**
     * @test
     */
    public function itGetsNullItem()
    {
        $this->cache->getItem(self::KEY)->shouldBeCalled()->willReturn(new CacheItem());

        $result = $this->service->getItem(self::KEY);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function itGetsItem()
    {
        $translation = $this->prophesize(Translation::class);

        $cacheItem = new CacheItem();
        $cacheItem->set($translation->reveal());

        $this->cache->getItem(self::KEY)->shouldBeCalled()->willReturn($cacheItem);

        $result = $this->service->getItem(self::KEY);

        $this->assertSame($translation->reveal(), $result);
    }

    /**
     * @test
     */
    public function itGetsItemFromRepository()
    {
        $this->cache->getItem(self::KEY)->shouldBeCalled()->willReturn(new CacheItem());
        $this->cache->warmUp([])->willReturn(null);

        $translation = $this->prophesize(Translation::class);

        $this->repository->findByKey(self::KEY)->shouldBeCalled()->willReturn($translation->reveal());
        $this->repository->findAll()->willReturn([]);

        $result = $this->service->getItem(self::KEY);

        $this->assertSame($translation->reveal(), $result);
    }
}
