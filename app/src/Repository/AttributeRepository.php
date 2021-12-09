<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\Fact;
use App\Entity\Security;
use App\Repository\Interfaces\AttributeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Attribute[]|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attribute[]|null findById(array $id)
 * @method Attribute|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Attribute[]      findAll()
 * @method Attribute[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeRepository extends ServiceEntityRepository implements AttributeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Attribute::class);
    }

    public function save(Attribute $attribute): Attribute
    {
        $this->entityManager->persist($attribute);
        $this->entityManager->flush();

        return $attribute;
    }

    public function delete(Attribute $attribute): Attribute
    {
        $this->entityManager->remove($attribute);
        $this->entityManager->flush();

        return $attribute;
    }

    //
    public function findByNameAndSecurity(Security $security, string $attributeName): ArrayCollection
    {
        $qb = $this->createQueryBuilder('s');

        $qb->select('a')
            ->from(Attribute::class, 'a')
            ->where('a.name = :name')
            ->setParameter('name', $attributeName)
            ->innerJoin('a.facts', 'f',Join::WITH, 'a.id = f.attribute')
//            ->setParameter('security', $security)
        ;


        return new ArrayCollection($qb->getQuery()->getResult());
    }
}
