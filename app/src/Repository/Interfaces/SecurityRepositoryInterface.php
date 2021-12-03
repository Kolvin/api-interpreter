<?php

namespace App\Repository\Interfaces;

use App\Entity\Security;

/**
 * @method Security|null find($id, $lockMode = null, $lockVersion = null)
 * @method Security[]    findById(array $id)
 * @method Security[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface SecurityRepositoryInterface
{
    public function save(Security $security): Security;

    public function delete(Security $security): Security;
}
