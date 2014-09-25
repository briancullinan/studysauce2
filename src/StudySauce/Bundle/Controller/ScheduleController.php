<?php

namespace StudySauce\Bundle\Controller;

//use StudySauce\Bundle\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ScheduleController extends Controller
{
    public function indexAction(Request $request)
    {
        /* working
        $product = new Session();
        $product->setId(time());
        $product->setTime(time());
        $product->setValue('Lorem ipsum dolor');

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
*/

        // regex and links to generate institutions.json
        // {"institution": "\2", "link": "\1", "state": "\3"},
        // http://www.utexas.edu/world/comcol/alpha/
        // http://www.utexas.edu/world/univ/alpha/

        return $this->render('StudySauceBundle:Schedule:index.html.php');
    }

    public function institutionsAction(Request $request)
    {
        $results = array();

        $kernel = $this->container->get('kernel');
        $path = $kernel->locateResource('@StudySauceBundle/Resources/public/js/institutions.json');
        $institutions = json_decode(file_get_contents($path));
        $search = $request->query->get('q');
        foreach($institutions as $i => $u) {
            //if (count($results) > 10) {
            //    break;
            //}
            if (strpos($u->institution, $search) > -1 || strpos($u->state, $search) > -1 ||
                strpos($u->link, $search) > -1) {
                $results[] = array('institution' => html_entity_decode($u->institution), 'state' => $u->state, 'link' => $u->link);
            }
        }

        return new JsonResponse($results);
    }
}