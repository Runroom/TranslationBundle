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

namespace Runroom\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 */
class Translation
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="translation_key", type="string", unique=true)
     */
    private $key;

    public function __toString(): string
    {
        return (string) $this->getKey();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getValue(): ?string
    {
        return $this->translate()->getValue();
    }
}
