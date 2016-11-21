<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Restaurant;
use Cocur\Slugify\Slugify;
use AppBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SlugifyExistantDataController extends Controller
{
    /**
     *
     *
     * @Route("/slugify/existant/data", name="slugify_existant_data")
     */
    public function slugifyExistantDataAction()
    {
        $em = $this->getDoctrine()->getManager();
        $restaurants = $this->get('app.repository.restaurant')->findAll();
        $events = $this->get('app.repository.event')->findAll();
        /** @var Restaurant $restaurant */
        foreach ($restaurants as $restaurant){
            $slugify = new Slugify();
            $restaurant->setSlug($slugify->slugify($restaurant->getName()));
            $em->persist($restaurant);
        }
        /** @var Event $event */
        foreach ($events as $event){
            $slugify = new Slugify();
            $toBeSlugified = $event->getRestaurant()->getName() . ' ' . $event->getStartDate()->format('d-m-Y');
            $event->setSlug($slugify->slugify($toBeSlugified));
            $em->persist($event);
        }
        $em->flush();
    }
}