<?php

declare(strict_types=1);

namespace Agenda\Contacts\Infrastructure\Persistence\DoctrineOrm;

use Agenda\Contacts\Domain\Entity\Contacts;
use Agenda\Contacts\Domain\Repository\ContactsRepository;
use Doctrine\ORM\EntityManager;

class ContactsRepositoryFromDoctrineOrm implements ContactsRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function store(Contacts $contacts): void
    {
        $this->entityManager->persist($contacts);
        $this->entityManager->flush();
    }

    public function remove(Contacts $contacts): void
    {
        $this->entityManager->remove($contacts);
        $this->entityManager->flush();
    }
}
