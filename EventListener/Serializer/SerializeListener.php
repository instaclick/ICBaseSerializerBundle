<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\EventListener\Serializer;

use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManager;
use Metadata\AdvancedMetadataFactoryInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

/**
 * Serialize Listener.
 *
 * @author Yuan Xie <shayx@nationalfibre.net>
 */
class SerializeListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Metadata\MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Define the translator.
     *
     * @param \Symfony\Component\Translation\TranslatorInterface $translator translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Define the metadata factory.
     *
     * @param \Metadata\AdvancedMetadataFactoryInterface $metadataFactory metadataFactory
     */
    public function setMetadataFactory(AdvancedMetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Define the entity manager.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * If an object has a translatable attribute, translate and set the value.
     *
     * @param \JMS\Serializer\EventDispatcher\PreSerializeEvent $event
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $context = $event->getContext();

        if ( ! $context->attributes->containsKey('translatable') ||
            $context->attributes->containsKey('translatable') && ! $context->attributes->get('translatable')->get()) {
            return;
        }

        $object          = $event->getObject();
        $objectClassName = get_class($object);
        $classMetadata   = $this->metadataFactory->getMetadataForClass($objectClassName);

        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            if ( ! $propertyMetadata->isTranslatable()) {
                continue;
            }

            $targetAttribute     = $propertyMetadata->getValue($object);
            $translatedAttribute = $this->translator->trans($targetAttribute);

            $propertyMetadata->setValue($object, $translatedAttribute);
        }
    }

    /**
     * If an object has a translatable attribute, refresh the entity to roll back the untranslated value.
     *
     * @param \JMS\Serializer\EventDispatcher\ObjectEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        $context = $event->getContext();

        if ( ! $context->attributes->containsKey('translatable') ||
            $context->attributes->containsKey('translatable') && ! $context->attributes->get('translatable')->get()) {
            return;
        }

        $object          = $event->getObject();
        $objectClassName = get_class($object);
        $classMetadata   = $this->metadataFactory->getMetadataForClass($objectClassName);

        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            if ( ! $propertyMetadata->isTranslatable()) {
                continue;
            }

            $this->entityManager->refresh($object);
            break;
        }
    }

    /**
     * Returns the event name that this subscriber is listening to and the
     * method name to call when the event happens.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
        );
    }
}
