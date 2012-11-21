<?php

namespace IBW\WebsiteBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TeamRepository
 */
class TeamRepository extends EntityRepository
{    
    /**
    * Search teams by name
    *
    * @param string $name The name to search for
    * @return array Found teams 
    */
    public function searchByName($name)
    {
        return $this->getEntityManager()
                    ->createQuery("SELECT t
                                   FROM IBWWebsiteBundle:Team t 
                                   WHERE t.name LIKE :name
                                   ORDER BY t.name")
                    ->setParameter('name', "%".$name."%")
                    ->getResult();
    }
    
    /**
     * Returns all teams in which an user is part
     *
     * @param \IBW\WebsiteBundle\Entity\User $user The user object
     * @return array Teams in which the user is part
     */
    public function findByUser(\IBW\WebsiteBundle\Entity\User $user)
    {
        return $this->getEntityManager()
                    ->createQuery('SELECT t
                                   FROM IBWWebsiteBundle:Team t, IBWWebsiteBundle:UserTeam ut
                                   WHERE ut.user = :user AND t.id = ut.team
                                   ORDER BY t.name')
                    ->setParameter('user', $user)
                    ->getResult();
    }
    
    /**
     * Returns the amount of climbed stairs by all the users of a team in a specific period
     *
     * @param \IBW\WebsiteBundle\Entity\Team $team The team object
     * @param Datetime $start_date Optional "Y-m-d H:i:s" formatted date string
     * @param Datetime $end_date Optional "Y-m-d H:i:s" formatted date string
     * @return int Amount of climbed stairs in specified period
     */
    public function getAmountForTeam(\IBW\WebsiteBundle\Entity\Team $team, $start_date = null, $end_date = null)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->add('select', 'SUM(s.amount)')
              ->add('from', 'IBWWebsiteBundle:StairsActivity s, IBWWebsiteBundle:UserTeam ut, IBWWebsiteBundle:User u')
              ->add('where', 'ut.team = :team AND u.id = ut.user AND s.user = u.id AND s.is_deleted = false')
              ->setParameter('team', $team);
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

        return $query->getQuery()->getSingleScalarResult();
    }
}
