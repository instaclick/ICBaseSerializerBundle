<?php
namespace IC\Bundle\Base\SerializerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JMS\Serializer\SerializationContext;
use IC\Bundle\Base\SerializerBundle\Serializer\Handler\ProxyHandler;

/**
 * Just a test command
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class CaseZeroCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sandbox:case0')
            ->setDescription('Case Zero')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = new SerializationContext();
        $context->setAttribute(ProxyHandler::ENABLE_HANDLER, true);

        $em   = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $this->getContainer()->get('ic_core_user.repository.user');
        $ser  = $this->getContainer()->get('serializer');

        $entity = $repo->get(1);

        try {
            $json = $ser->serialize($entity, 'json', $context);
            $x = json_decode($json);
        } catch (\Exception $e) {
            var_dump($e->getTraceAsString());
            var_dump($e->getMessage());
            die;
        }

        echo PHP_EOL;
        var_dump($x);
    }
}
