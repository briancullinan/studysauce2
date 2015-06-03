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
                $coupon->setOptions(['torch' => ['price' => 50, 'description' => '<s style=\'color:#CC0000;margin-top:-22px;position:absolute;\'>Normally $99 for 12 months</s>$50 for 18 months<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Torch &amp; Laurel Scholars)']]);
                $coupon->setName('TORCHANDLAUREL');
                $coupon->setDescription('');
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
                $coupon->setOptions(['torch' => ['price' => 50, 'description' => '<s style=\'color:#CC0000;margin-top:-22px;position:absolute;\'>Normally $99 for 12 months</s>$50 for 18 months<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Torch &amp; Laurel Scholars)']]);
                $coupon->setName('TORCH&LAUREL');
                $coupon->setDescription('');
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
                $coupon->setOptions(['torch' => ['price' => 50, 'description' => '<s style=\'color:#CC0000;margin-top:-22px;position:absolute;\'>Normally $99 for 12 months</s>$50 for 18 months<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Torch &amp; Laurel Scholars)']]);
                $coupon->setName('TORCH AND LAUREL');
                $coupon->setDescription('');
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
                $coupon->setOptions(['torch' => ['price' => 50, 'description' => '<s style=\'color:#CC0000;margin-top:-22px;position:absolute;\'>Normally $99 for 12 months</s>$50 for 18 months<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Torch &amp; Laurel Scholars)']]);
                $coupon->setName('TORCH & LAUREL');
                $coupon->setDescription('');
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
