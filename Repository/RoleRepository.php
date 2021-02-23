<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use PHPZlc\PHPZlc\Doctrine\ORM\Repository\AbstractServiceEntityRepository;
use PHPZlc\PHPZlc\Doctrine\ORM\Rule\Rule;
use PHPZlc\PHPZlc\Doctrine\ORM\Rule\Rules;
use PHPZlc\Validate\Validate;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role|null    findAssoc($rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Role|null   findLastAssoc($rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Role|null    findAssocById($id, $rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Role[]    findAll($rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Role[]    findLimitAll($rows, $page = 1, $rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 */
class RoleRepository extends AbstractServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
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