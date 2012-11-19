<?php

namespace IBW\WebsiteBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Stairs Activities Entity Repository 
 */
class StairsActivityRepository extends EntityRepository
{
    /**
     * Returns all activities for a user, newest first, limited to $limit
     *
     * @param \IBW\WebsiteBundle\Entity\User $user The user object
     * @param integer $limit Optional limit of returned items
     * 
     * @return array StairsActivities for the specified user, ordered by cretaed_at desc, id desc, limited to $limit
     */
    public function findByUser(\IBW\WebsiteBundle\Entity\User $user, $limit = null)
    {
        $query = $this->createQueryBuilder('s')
                      ->where('s.user = :user AND s.is_deleted = false')
                      ->setParameter('user', $user)
                      ->addOrderBy('s.created_at', 'DESC')
                      ->addOrderBy('s.id', 'DESC')
                      ->getQuery();

        if($limit)
        {
            $query->setMaxResults($limit);
        }
        
        return $query->getResult();
    }
    
    /**
     * Returns activities from the last 5 minutes for which notifications were not send
     * 
     * @return array StairsActivities from the last 5 minutes for which notifications were not send
     */
    public function getForNotifications()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('PT5M')); // last 5 minutes

        $query = $this->createQueryBuilder('s')
                      ->where('s.is_notification_sent = :notification_sent
                               AND s.is_deleted = :deleted
                               AND s.created_at >= :datetime')
                      ->setParameter('notification_sent', false)
                      ->setParameter('deleted', false)
                      ->setParameter('datetime', $date)
                      ->addOrderBy('s.created_at', 'ASC')
                      ->getQuery();
        
        return $query->getResult();
    }
}
