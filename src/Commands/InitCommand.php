<?php

namespace Iber\Lizard\Commands;

use Iber\Console\Question\MultipleChoiceQuestion;
use Iber\Console\Question\Question;
use Iber\Console\UI\Drawer;
use Iber\Console\UI\Window;
use Iber\Lizard\Composer;
use Iber\Lizard\Lizard;
use Iber\Lizard\UI\Confirmation;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InitCommand
 *
 * @package  Iber\Lizard\Commands
 */
class InitCommand extends Command
{

    /**
     * @var
     */
    protected $composer;

    /**
     * @var
     */
    protected $packageManager;

    /**
     * @var
     */
    protected $laravelManager;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Run lizard')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Project directory');
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $window = new Window();
        $lizard = new Lizard($input->getArgument('dir'));
        
        $composer = new Composer($input->getArgument('dir'));
        if (!$composer->hasDependency('laravel/framework')) {
            throw new \InvalidArgumentException('Package laravel/framework was not found in dependency list.');
        }

        $packages = array_keys($lizard->getPackages());
        $dependencies = $composer->getDependencies($packages);

        /*
         **********************************************
         *
         *  Select which packages to install/remove
         *
         **********************************************
         */
        $drawer = new Drawer($output, $window);
        $question = new MultipleChoiceQuestion($drawer);

        $selection = $question->setTitle('Which packages would you like to install?')
            ->setChoices($packages)
            ->setAnswers($dependencies)
            ->ask();

        extract($lizard->getDiff($dependencies, $selection));

        /*
         **********************************************
         *
         *  Confirm composer update
         *
         **********************************************
         */
        if (empty($added) && empty($removed)) {
            $output->writeln('<info>No updates were performed.</info>');

            return;
        }

        $confirm = new Question($drawer);
        $confirm->setTitle((new Confirmation())->getTitle($added, $removed))
            ->setChoices(['Yes', 'No'])
            ->setAnswers(['Yes'])
            ->ask();

        if ('No' === $confirm->getAnswers()) {
            $output->writeln('<error>Package updates cancelled.</error>');

            return;
        }

        $install = [];
        
        foreach ($added as $add) {
            $install[$add] = $lizard->getPackage($add)['version'];
        }

        $output->writeln('<info>Updating composer.json and running composer update</info>');

        $composer->update($install, $removed);

        $lizard->updateAppConfig($input->getArgument('dir'), $added, $removed);

        /*
         **********************************************
         *
         *  Select whether to use bower/gulp templates
         *
         **********************************************
         */
        $confirm = new Question($drawer);
        $confirm->setTitle("Would you like to setup bower and elixir templates?")
            ->setChoices(['Yes', 'No'])
            ->setAnswers(['Yes'])
            ->ask();

        if ('No' === $confirm->getAnswers()) {
            $output->writeln('<info>All done.</info>');

            return;
        }

        $output->writeln('<info>Adding .bowerrc and bower.json files</info>');
        $lizard->addBowerTemplate();
        
        $output->writeln('<info>Adding gulpfile.js</info>');
        $lizard->addElixirTemplate();
        
        $output->writeln('<info>All done.</info>');

    }
}
