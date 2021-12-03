<?php

namespace App\Repository\Interfaces;

use App\Entity\Fact;

/**
 * @method Fact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fact[]    findById(array $id)
 * @method Fact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface FactRepositoryInterface
{
    public function save(Fact $fact): Fact;

    public function delete(Fact $fact): Fact;
}
