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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationService
{
    private $translator;
    private $cache;
    private $requestStack;

    public function __construct(
        TranslatorInterface $translator,
        TranslationCacheService $cache,
        RequestStack $requestStack
    ) {
        $this->translator = $translator;
        $this->cache = $cache;
        $this->requestStack = $requestStack;
    }

    public function translate(string $key, array $parameters = [], string $locale = null): string
    {
        $locale = $locale ?? $this->requestStack->getCurrentRequest()->getLocale();
        $translation = $this->cache->getItem($key);

        if (null !== $translation) {
            return strtr($translation->translate($locale)->getValue(), $parameters);
        }

        return $this->translator->trans($key, $parameters, null, $locale);
    }
}
