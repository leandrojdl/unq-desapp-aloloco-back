<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class DoctrineProductCategoryRepository
 * @package App\Repository
 */
class DoctrineProductCategoryRepository
    extends EntityRepository
    implements ProductCategoryRepository {
}