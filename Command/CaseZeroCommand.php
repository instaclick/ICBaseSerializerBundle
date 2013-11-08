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

        $user = $repo->get(1);

        //\Doctrine\Common\Util\Debug::dump($user);
        //die;

        // $json = $ser->serialize($user, 'json');
        // $x    = json_decode($json);
        // var_dump($x);
        // echo "Finished the full json\n";

        echo "Printing the caped one in 6sec\n";
        //sleep(6);

        $json = $ser->serialize($user, 'json', $context);
        $x    = json_decode($json);
        var_dump($x);

        // echo "Printing the deserialized Profile 1 getContent in 6sec\n";
        // sleep(6);

        // $obj = $ser->deserialize($json, 'IC\Bundle\Core\UserBundle\Entity\User', 'json');
        // $p1  = $obj->getProfileList();
        // var_dump($p1[0]->getContent());
    }
}
