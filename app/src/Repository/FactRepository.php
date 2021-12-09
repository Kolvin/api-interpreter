<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\Fact;
use App\Entity\Security;
use App\Repository\Interfaces\FactRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fact[]|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fact[]|null findById(array $id)
 * @method Fact|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Fact[]      findAll()
 * @method Fact[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactRepository extends ServiceEntityRepository implements FactRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Fact::class);
    }

    public function save(Fact $fact): Fact
    {
        $this->entityManager->persist($fact);
        $this->entityManager->flush();

        return $fact;
    }

    public function delete(Fact $fact): Fact
    {
        $this->entityManager->remove($fact);
        $this->entityManager->flush();

        return $fact;
    }
}
