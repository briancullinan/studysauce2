<?php

namespace Admin\Bundle\Controller;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class PartnerController
 * @package StudySauce\Bundle\Controller
 */
class ActivityController extends Controller
{

    /**
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        $start = new \DateTime('today');
        if(!empty($request->get('start')))
            $start->setTimestamp(intval($request->get('start')));
        $end = new \DateTime('now');
        if(!empty($request->get('end')))
            $end->setTimestamp(intval($request->get('end')));
        /** @var QueryBuilder $entities */
        $entities = $orm->getRepository('StudySauceBundle:Visit')->createQueryBuilder('v')
            ->distinct()
            ->select(['v', 'u'])
            ->leftJoin('v.user', 'u')
            ->leftJoin('u.groups', 'g')
            ->where('v.created > :start AND v.created < :end' . (!empty($request->get('not')) ? (' AND v.id NOT IN (' . $request->get('not') . ')') : ''))
            ->andWhere('u.roles NOT LIKE \'%s:10:"ROLE_ADMIN"%\' AND v.path != \'/cron\'');
        if(!empty($request->get('search')))
            $entities = $entities->andWhere('v.session LIKE \'%' . $request->get('search') . '%\' OR u.email LIKE \'%' . $request->get('search') . '%\' OR u.first LIKE \'%' . $request->get('search') . '%\' OR u.last LIKE \'%' . $request->get('search') . '%\' OR u.id LIKE \'%' . $request->get('search') . '%\' OR g.name LIKE \'%' . $request->get('search') . '%\' OR g.description LIKE \'%' . $request->get('search') . '%\'');
        $entities = $entities
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('v.created', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
        /** @var array $entities */

        $visits = array_map(function (Visit $v) {
            return [
                'id' => $v->getId(),
                'start' => $v->getCreated()->format('r'),
                'content' => '<a href="#visit-id-' . $v->getId() . '">' .
                    '<strong>Path:</strong><span> ' . $v->getMethod() . ' ' . $v->getPath() . '</span><br />' .
                    '<strong>User:</strong><span> ' . (!empty($v->getUser()) ? $v->getUser()->getEmail() : 'Guest') . '</span><br />' .
                    '<strong>Session Id:</strong><span> ' . (!empty($v->getSession()) ? $v->getSession() : 'New session') . '</span><br />' .
                    '<strong>IP:</strong><span> ' . long2ip($v->getIp()) . '</span><br />' .
                    '</a>',
                'className' => 'session-id-' . (!empty($v->getSession()) ? $v->getSession() : '') . ' user-id-' . (!empty($v->getUser()) && !$v->getUser()->hasRole('ROLE_GUEST') && !$v->getUser()->hasRole('ROLE_DEMO') ? $v->getUser()->getId() : '')
            ];
        }, $entities);

        if($request->isXmlHttpRequest() && !empty($request->get('start')) && !empty($request->get('end'))) {
            return new JsonResponse($visits);
        }

        return $this->render('AdminBundle:Activity:tab.html.php', [
            'visits' => $visits
        ]);
    }
}