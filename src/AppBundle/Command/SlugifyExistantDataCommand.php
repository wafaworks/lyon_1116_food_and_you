<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 22/11/16
 * Time: 09:48
 */

namespace AppBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SlugifyExistantDataCommand extends Command
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
        // ...
    }
}