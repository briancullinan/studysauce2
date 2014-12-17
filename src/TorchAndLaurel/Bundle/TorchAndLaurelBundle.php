<?php

namespace TorchAndLaurel\Bundle;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\Group;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NewBrandBundle
 * @package NewBrand\Bundle
 */
class TorchAndLaurelBundle extends Bundle
{
    public function boot()
    {
        try {
            /** @var $orm EntityManager */
            $orm = $this->container->get('doctrine')->getManager();

            // create torch and laurel group if it does not exist
            /** @var Group $group */
            $group = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['name' => 'Torch And Laurel']);
            if (empty($group)) {
                $group = new Group();
                $group->setName('Torch And Laurel');
                $group->setDescription('');
                $orm->persist($group);
                $orm->flush();
            }

            // create a torch and laurel discount coupon
            /** @var Coupon $coupon */
            $coupon = $orm->getRepository('StudySauceBundle:Coupon')->findOneBy(['name' => 'TORCHANDLAUREL']);
            if (empty($coupon)) {
                $coupon = new Coupon();
                $coupon->setTerm(18);
                $coupon->setName('TORCHANDLAUREL');
                $coupon->setDescription('75% off from Torch & Laurel');
                $coupon->setType('=50');
                $coupon->setMaxUses(1);
                $coupon->setSeed(md5(uniqid()));
                $coupon->setGroup($group);
                $orm->persist($coupon);
                $orm->flush();
            }

            // create a torch and laurel discount coupon
            /** @var Coupon $coupon */
            $coupon = $orm->getRepository('StudySauceBundle:Coupon')->findOneBy(['name' => 'TORCH&LAUREL']);
            if (empty($coupon)) {
                $coupon = new Coupon();
                $coupon->setTerm(18);
                $coupon->setName('TORCH&LAUREL');
                $coupon->setDescription('75% off from Torch & Laurel');
                $coupon->setType('=50');
                $coupon->setMaxUses(1);
                $coupon->setSeed(md5(uniqid()));
                $coupon->setGroup($group);
                $orm->persist($coupon);
                $orm->flush();
            }

            // create a torch and laurel discount coupon
            /** @var Coupon $coupon */
            $coupon = $orm->getRepository('StudySauceBundle:Coupon')->findOneBy(['name' => 'TORCH AND LAUREL']);
            if (empty($coupon)) {
                $coupon = new Coupon();
                $coupon->setTerm(18);
                $coupon->setName('TORCH AND LAUREL');
                $coupon->setDescription('75% off from Torch & Laurel');
                $coupon->setType('=50');
                $coupon->setMaxUses(1);
                $coupon->setSeed(md5(uniqid()));
                $coupon->setGroup($group);
                $orm->persist($coupon);
                $orm->flush();
            }

            // create a torch and laurel discount coupon
            /** @var Coupon $coupon */
            $coupon = $orm->getRepository('StudySauceBundle:Coupon')->findOneBy(['name' => 'TORCH & LAUREL']);
            if (empty($coupon)) {
                $coupon = new Coupon();
                $coupon->setTerm(18);
                $coupon->setName('TORCH & LAUREL');
                $coupon->setDescription('75% off from Torch & Laurel');
                $coupon->setType('=50');
                $coupon->setMaxUses(1);
                $coupon->setSeed(md5(uniqid()));
                $coupon->setGroup($group);
                $orm->persist($coupon);
                $orm->flush();
            }
        }
        catch (\Exception $ex) {

        }
    }
}
