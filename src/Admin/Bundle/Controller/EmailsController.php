<?php

namespace Admin\Bundle\Controller;

use Codeception\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class ValidationController
 * @package StudySauce\Bundle\Controller
 */
class EmailsController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $emails = [];
        $templatesDir = new \DirectoryIterator($this->container->getParameter('kernel.root_dir') . '/../src/StudySauce/Bundle/Resources/views/Emails/');
        foreach($templatesDir as $f) {
            /** @var \DirectoryIterator $f */
            if($f->getFilename() == 'layout.html.php')
                continue;
            if(!$f->isDot()) {
                // get count for current email category
                $base = basename($f->getFilename(), '.html.' . $f->getExtension());
                $count = $orm->getRepository('StudySauceBundle:Mail')->createQueryBuilder('m')
                    ->select('COUNT(DISTINCT m.id)')
                    ->andWhere('m.message LIKE \'%s:' . (17 + strlen($base)) . ':"{"category":["' . $base . '"]}"%\'')
                    ->getQuery()
                    ->getSingleScalarResult();
                $emails[] = [
                    'id' => $base,
                    'count' => $count
                ];
            }
        }

        $yesterday = new \DateTime('yesterday');
        /** @var QueryBuilder $qb */
        $qb = $orm->getRepository('StudySauceBundle:Mail')->createQueryBuilder('m');
        $recent = $qb->select('COUNT(DISTINCT m.id)')
            ->andWhere('m.created > :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->getQuery()
            ->getSingleScalarResult();


        return $this->render('AdminBundle:Emails:index.html.php', [
                'emails' => $emails,
                'total' => 0,
                'recent' => $recent
            ]);
    }
}