<?php

namespace IBW\WebsiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * User Entity Repository 
 */
class UserRepository extends EntityRepository
{
    /**
     * Returns a top of user emails and amounts for specific period and/or team, limited to a specified amount
     *
     * @param Datetime $start_date Optional "Y-m-d H:i:s" formatted date string
     * @param Datetime $end_date Optional "Y-m-d H:i:s" formatted date string
     * @param \IBW\WebsiteBundle\Entity\Team $team The team for which the top will be returner
     * @param integer $limit Optional limit of returned top items
     * @return array User emails and amounts ordered by amount descending
     */
    public function getTop($start_date = null, $end_date = null, \IBW\WebsiteBundle\Entity\Team $team = null, $limit = null)
    { 
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->add('select', 'u.email, SUM(s.amount) AS total')
              ->add('from', 'IBWWebsiteBundle:User u, IBWWebsiteBundle:StairsActivity s')
              ->add('where', 'u.id = s.user AND s.is_deleted = false');
        if($team) {
            $query->add('from','IBWWebsiteBundle:User u, IBWWebsiteBundle:UserTeam ut, IBWWebsiteBundle:StairsActivity s');
            $query->andWhere($query->expr()->eq('u.id', 'ut.user'));
            $query->andWhere($query->expr()->eq('ut.team', ':team'));
            $query->setParameter('team', $team);
        }
        if($start_date && $end_date) {
            $query->andWhere($query->expr()->between('s.created_at', ':start_date', ':end_date'))
                  ->setParameter('start_date', $start_date)
                  ->setParameter('end_date', $end_date);
        }
        if(!$start_date && $end_date) {
            $query->andWhere($query->expr()->lte('s.created_at', ':end_date'))
                  ->setParameter('end_date', $end_date);
        }
        if(!$end_date && $start_date) { 
            $query->andWhere($query->expr()->gte('s.created_at', ':start_date'))
                  ->setParameter('start_date', $start_date);
        }
        $query->add('orderBy', 'total DESC')
              ->add('groupBy', 'u.id');
        if($limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }
    
    /**
     * Returns the amount of climbed stairs by an user in a specific period
     *
     * @param \IBW\WebsiteBundle\Entity\User $user The user object
     * @param Datetime $start_date Optional "Y-m-d H:i:s" formatted date string
     * @param Datetime $end_date Optional "Y-m-d H:i:s" formatted date string
     * @return integer Amount of climbed stairs in specified period
     */
    public function getAmountForUser(\IBW\WebsiteBundle\Entity\User $user, $start_date = null, $end_date = null)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->add('select','SUM(s.amount) AS total')
              ->add('from','IBWWebsiteBundle:StairsActivity s')
              ->add('where','s.user = :user AND s.is_deleted = false')
              ->setParameter('user', $user);
        if($start_date && $end_date) {    
            $query->andWhere($query->expr()->between('s.created_at', ':start_date',':end_date'))
                  ->setParameter('start_date', $start_date)
                  ->setParameter('end_date', $end_date);
        }
        if(!$start_date && $end_date) {
            $query->andWhere($query->expr()->lte('s.created_at',':end_date'))
                  ->setParameter('end_date', $end_date);
        }
        if(!$end_date && $start_date) { 
            $query->andWhere($query->expr()->gte('s.created_at',':start_date'))
                  ->setParameter('start_date', $start_date);
        }
        
        $result = $query->getQuery()->getSingleScalarResult();
        if($result === null) {
          $result = 0;
        }
        
        return $result;
    }
    
    /**
     * Returns the amount of climbed stairs by an user grouped by day
     *
     * @param \IBW\WebsiteBundle\Entity\User $user The user object
     * @return array An array with amount of climbed stairs by user, grouped by day
     */
    public function statsForUser(\IBW\WebsiteBundle\Entity\User $user)
    {
        $sql = "SELECT DATE(s.created_at) as date, SUM(s.amount) as total  FROM stairs_activity s WHERE s.user_id = :user_id AND s.is_deleted = false GROUP BY DATE(s.created_at) ORDER BY DATE(s.created_at)";
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('IBWWebsiteBundle:StairsActivity', 's');
        $rsm->addScalarResult('total', 'total');
        $rsm->addScalarResult('date', 'date');       
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameter('user_id', $user->getId());
        
        return $query->getResult();
    }
    
     /**
     * Returns users that are part of a team
     *
     * @param \IBW\WebsiteBundle\Entity\Team $team The team object
     * @return array Users that are part of the team
     */
    public function findByTeam(\IBW\WebsiteBundle\Entity\Team $team)
    {
        return $this->getEntityManager()
                    ->createQuery('SELECT u
                                   FROM IBWWebsiteBundle:User u, IBWWebsiteBundle:UserTeam ut
                                   WHERE ut.team = :team AND u.id = ut.user
                                   ORDER BY t.name')
                    ->setParameter('team', $team)
                    ->getResult();
    }
}
