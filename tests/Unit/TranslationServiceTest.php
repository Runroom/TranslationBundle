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
use Runroom\TranslationBundle\Service\TranslationCacheService;
use Runroom\TranslationBundle\Service\TranslationService;
use Runroom\TranslationBundle\Tests\Fixtures\TranslationFixtures;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationServiceTest extends TestCase
{
    private $translator;
    private $cache;
    private $requestStack;
    private $service;

    protected function setUp(): void
    {
        $this->translator = $this->prophesize(TranslatorInterface::class);
        $this->cache = $this->prophesize(TranslationCacheService::class);
        $this->requestStack = $this->prophesize(RequestStack::class);

        $this->service = new TranslationService(
            $this->translator->reveal(),
            $this->cache->reveal(),
            $this->requestStack->reveal()
        );
    }

    /**
     * @test
     */
    public function itReturnsAStringTranslatedByTheRepository()
    {
        $translation = TranslationFixtures::create();

        $this->cache->getItem(TranslationFixtures::KEY)->willReturn($translation);
        $this->translator->trans(TranslationFixtures::KEY, [], null, 'en')->shouldNotBeCalled();

        $result = $this->service->translate(TranslationFixtures::KEY, [], 'en');

        $this->assertSame(TranslationFixtures::VALUE, $result);
    }

    /**
     * @test
     */
    public function itReturnsAStringTranslatedByTheTranslatorComponent()
    {
        $this->cache->getItem(TranslationFixtures::KEY)->willReturn(null);
        $this->translator->trans(TranslationFixtures::KEY, [], null, 'en')
            ->willReturn('another_translation');

        $result = $this->service->translate(TranslationFixtures::KEY, [], 'en');

        $this->assertSame('another_translation', $result);
    }
}
