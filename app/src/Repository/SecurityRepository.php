<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\Fact;
use App\Entity\Security;
use App\Repository\Interfaces\SecurityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
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

    public function findFactsByAttributeName(Security $security, string $attributeName): ArrayCollection
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->select('f')
            ->innerJoin(Fact::class, 'f', Join::WITH, 's.id = f.security')
            ->innerJoin(Attribute::class, 'a', Join::WITH, 'a.id = f.attribute')
            ->where('f.security = :security')
            ->andWhere('a.name = :name')
            ->setParameter('security', $security)
            ->setParameter('name', $attributeName)
        ;

        return new ArrayCollection($qb->getQuery()->getResult());
    }
}
