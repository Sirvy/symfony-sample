<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class ContactRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array<Contact>
     */
    public function getAll(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Contact::class, 'contact')
            ->select('contact')
            ->orderBy('contact.id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param string $contactIdentifier
     * @return Contact|null
     */
    public function getByIdentifier(string $contactIdentifier): ?Contact
    {
        try {
            return $this->entityManager
                ->createQueryBuilder()
                ->from(Contact::class, 'contact')
                ->select('contact')
                ->where('contact.identifier = :identifier')
                ->setParameter('identifier', $contactIdentifier)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Returns true, if a different record with the same identifier exists
     * Returns false otherwise
     *
     * @param Contact $contact
     * @return bool
     */
    public function contactWithSameIdentifierExists(Contact $contact): bool
    {
        $result = $this->entityManager
            ->createQueryBuilder()
            ->from(Contact::class, 'contact')
            ->select('contact')
            ->where('contact.identifier = :identifier')
            ->setParameter('identifier', $contact->getIdentifier())
            ->andWhere('contact.id <> :id')
            ->setParameter('id', $contact->getId())
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();

        return 0 !== count($result);
    }
}