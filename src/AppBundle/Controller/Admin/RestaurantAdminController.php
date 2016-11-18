<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Restaurant;
use AppBundle\Notification\Event\RestaurantValidatedEvent;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestaurantAdminController extends Controller
{
    public function validateAction()
    {
        /** @var Restaurant $object */
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('restaurant.not_found', array(), 'SonataAdminRestaurant')
            );
        }

        $object->setStatus(Restaurant::STATUS_VALIDATED);

        $this->admin->update($object);

        $this->get('event_dispatcher')->dispatch(
            RestaurantValidatedEvent::EVENT_NAME,
            new RestaurantValidatedEvent($object)
        );

        $this->addFlash(
            'sonata_flash_success',
            $this->get('translator')->trans('restaurant.validated', array(), 'SonataAdminRestaurant')
        );

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function rejectAction()
    {
        /** @var Restaurant $object */
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('restaurant.not_found', array(), 'SonataAdminRestaurant')
            );
        }

        // remove role owner
        $ownerAuth = $object->getOwner()->getAuthentication();
        $ownerAuth->removeRole('ROLE_OWNER');
        $this->admin->update($ownerAuth);

        $this->admin->delete($object);
        $this->addFlash(
            'sonata_flash_success',
            $this->get('translator')->trans('restaurant.rejected', array(), 'SonataAdminRestaurant')
        );

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}
