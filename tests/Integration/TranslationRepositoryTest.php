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

namespace Runroom\TranslationBundle\Tests\Integration;

use Runroom\TranslationBundle\Entity\Translation;
use Runroom\TranslationBundle\Repository\TranslationRepository;
use Runroom\TranslationBundle\Tests\TestCase\DoctrineIntegrationTestBase;

class TranslationRepositoryTest extends DoctrineIntegrationTestBase
{
    public const LOCALE_EN = 'en';
    public const LOCALE_ES = 'es';
    public const MODIFIED_TRANSLATION_KEY = 'translation.key';
    public const MODIFIED_TRANSLATION_VALUE = 'modified value';
    public const TOTAL_TRANSLATIONS = 16;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TranslationRepository(static::$entityManager);
    }

    /**
     * @test
     */
    public function itFindsAll()
    {
        $translations = $this->repository->findAll();

        $this->assertCount(self::TOTAL_TRANSLATIONS, $translations);
    }

    /**
     * @test
     */
    public function itFindsByKey()
    {
        $translation = $this->repository->findByKey(self::MODIFIED_TRANSLATION_KEY);

        $this->assertInstanceOf(Translation::class, $translation);
        $this->assertSame(self::MODIFIED_TRANSLATION_KEY, $translation->getKey());
    }

    /**
     * @test
     */
    public function itDoesNotImportModifiedTranslations()
    {
        $catalogues = [
            self::LOCALE_EN => [
                self::MODIFIED_TRANSLATION_KEY => self::MODIFIED_TRANSLATION_VALUE . ' MODIFIED',
            ],
        ];

        $this->repository->importCatalogues($catalogues);

        $translation = $this->repository->findByKey(self::MODIFIED_TRANSLATION_KEY);
        $this->assertInstanceOf(Translation::class, $translation);
        $this->assertSame(self::MODIFIED_TRANSLATION_VALUE, $translation->getValue());
    }

    /**
     * @test
     */
    public function itImportsCatalogues()
    {
        $key = 'cookies.accept';
        $catalogues = [
            self::LOCALE_EN => [
                $key => 'Accept cookies',
            ],
            self::LOCALE_ES => [
                $key => 'Aceptar cookies',
            ],
        ];

        $this->repository->importCatalogues($catalogues);

        $translation = $this->repository->findByKey($key);
        $this->assertInstanceOf(Translation::class, $translation);
    }

    protected function getDataFixtures(): array
    {
        return ['translation.yaml'];
    }
}
