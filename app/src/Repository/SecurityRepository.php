<?php

namespace App\Repository;

use App\Entity\Security;
use App\Repository\Interfaces\SecurityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Security[]|null find($id, $lockMode = null, $lockVersion = null)
 * @method Security[]|null findById(array $id)
 * @method Security|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Security[]      findAll()
 * @method Security[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecurityRepository extends ServiceEntityRepository implements SecurityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Security::class);
    }

    public function save(Security $security): Security
    {
        $this->entityManager->persist($security);
        $this->entityManager->flush();

        return $security;
    }

    public function delete(Security $security): Security
    {
        $this->entityManager->remove($security);
        $this->entityManager->flush();

        return $security;
    }
}
