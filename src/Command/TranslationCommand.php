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

namespace Runroom\TranslationBundle\Command;

use Runroom\TranslationBundle\Repository\TranslationRepository;
use Runroom\TranslationBundle\Service\TranslationCacheService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationCommand extends Command
{
    public const DOMAIN = 'messages';

    private $translator;
    private $repository;
    private $cache;
    private $locales;

    public function __construct(
        TranslatorInterface $translator,
        TranslationRepository $repository,
        TranslationCacheService $cache,
        array $locales
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->repository = $repository;
        $this->cache = $cache;
        $this->locales = $locales;
    }

    protected function configure()
    {
        $this
            ->setName('runroom:translation:import')
            ->setDescription('Import messages from translation YAML files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $catalogues = [];
        foreach ($this->locales as $locale) {
            $catalogues[$locale] = $this->translator->getCatalogue($locale)->all(self::DOMAIN);
        }

        $this->repository->importCatalogues($catalogues);
        $this->cache->warmUpTranslations();
    }
}
