<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPZlc\PHPZlc\Doctrine\ORM\Repository\AbstractServiceEntityRepository;
use PHPZlc\PHPZlc\Doctrine\ORM\Rule\Rule;
use PHPZlc\PHPZlc\Doctrine\ORM\Rule\Rules;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission|null    findAssoc($rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Permission|null   findLastAssoc($rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Permission|null    findAssocById($id, $rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Permission[]    findAll($rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Permission[]    findLimitAll($rows, $page = 1, $rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 */

class PermissionRepository extends AbstractServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function registerRules()
    {
        // TODO: Implement registerRules() method.
    }

    public function ruleRewrite(Rule $currentRule, Rules $rules, ResultSetMappingBuilder $resultSetMappingBuilder)
    {
        // TODO: Implement ruleRewrite() method.
    }
}