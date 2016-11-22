<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 22/11/16
 * Time: 09:48
 */

namespace AppBundle\Command;


use AppBundle\Entity\Event;
use AppBundle\Entity\Restaurant;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SlugifyExistantDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:slugify:existantData')
            ->setDescription('Slugify existant data to fit new database')
            ->setHelp('This command allow you to slugify existant data to not have errors');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();
        $restaurants = $this->getContainer()->get('app.repository.restaurant')->findAll();
        $events = $this->getContainer()->get('app.repository.event')->findAll();
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