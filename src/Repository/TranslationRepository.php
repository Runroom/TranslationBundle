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

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Runroom\TranslationBundle\Entity\Translation;

class TranslationRepository extends ServiceEntityRepository
{
    protected $requestStack;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translation::class);
    }
}
