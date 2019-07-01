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

use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Runroom\TranslationBundle\Service\TranslationService;
use Runroom\TranslationBundle\Tests\Fixtures\TranslationFixtures;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationServiceTest extends TestCase
{
    private $repository;
    private $translator;
    private $service;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(EntityRepository::class);
        $this->translator = $this->prophesize(TranslatorInterface::class);

        $this->service = new TranslationService(
            $this->repository->reveal(),
            $this->translator->reveal()
        );
    }

    /**
     * @test
     */
    public function itReturnsAStringTranslatedByTheRepository()
    {
        $translation = TranslationFixtures::create();

        $this->repository->find(TranslationFixtures::ID)->willReturn($translation);
        $this->translator->trans(TranslationFixtures::ID, [], null, 'en')->shouldNotBeCalled();

        $result = $this->service->translate(TranslationFixtures::ID, [], 'en');

        $this->assertSame(TranslationFixtures::VALUE, $result);
        $this->assertSame(TranslationFixtures::ID, $translation->getId());
        $this->assertSame(TranslationFixtures::VALUE, $translation->getValue());
    }

    /**
     * @test
     */
    public function itReturnsAStringTranslatedByTheTranslatorComponent()
    {
        $this->repository->find(TranslationFixtures::ID)->willReturn(null);
        $this->translator->trans(TranslationFixtures::ID, [], null, 'en')
            ->willReturn(TranslationFixtures::VALUE);

        $result = $this->service->translate(TranslationFixtures::ID, [], 'en');

        $this->assertSame(TranslationFixtures::VALUE, $result);
    }
}
