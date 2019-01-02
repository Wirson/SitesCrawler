<?php


namespace Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Sites extends Command
{

    protected static $defaultName = 'app:crawl-sites';

    protected function configure()
    {
        $this->setDescription('Crawl sites from given file');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }

}
