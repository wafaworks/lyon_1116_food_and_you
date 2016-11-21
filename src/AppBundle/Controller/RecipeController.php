<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Authentication;
use AppBundle\Entity\Media;
use AppBundle\Entity\Recipe;
use AppBundle\Form\FormErrorSerializer;
use AppBundle\Form\Type\RecipeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RecipeController extends Controller
{
    /**
     * @Route("/mes-recettes", name="recipe_list", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        /** @var Authentication $user */
        $user   = $this->getUser();
        if ($request->isXmlHttpRequest() && $user) {
            $repository =  $this->get('app.repository.recipe');
            $recipes    = $repository->findBy(array("member" => $user->getMember()));

            foreach ($recipes as &$recipe) {
                /** @var Recipe $recipe */
                $recipe = array(
                    'id'    => $recipe->getId(),
                    'image' => $recipe->getPhoto() ? $this->getPhotoUrl($recipe->getPhoto()) : '',
                    'name' => $recipe->getName(),
                    'description'   => $recipe->getPublicDescription()
                );
            }

            $response   = new JsonResponse(
                array(
                    "status"    => "ok",
                    "data"      => $recipes
                )
            );
        } else {
            $response   = new JsonResponse(
                array(
                    "status"    => "error",
                    "data"      => null,
                    "message"   => $user ? "is allowed only XmlHttpRequest" : "user is not logined"
                )
            );
        }
        return $response;
    }

    /**
     * [getPhotoUrl description]
     * @param  Media  $photo [description]
     * @return string        photo url
     */
    private function getPhotoUrl(Media $photo)
    {
        /** @var ImageProvider $provider */
        $provider = $this->get($photo->getProviderName());
        $format = $provider->getFormatName($photo, "small");

        return $provider->generatePublicUrl($photo, $format);
    }

    /**
     * @Route("/mes-recettes/ajouter", name="recipe_add", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $member = $this->getUser()->getMember();
        if (!$request->isXmlHttpRequest() || !$member) {
            return new JsonResponse(
                array(
                    "status" => "error",
                    "errors" => 'Not XHR or no Member'
                )
            );
        }

        /** @var Form $form */
        $recipe = new Recipe();
        $recipe->setMember($member);

        $form = $this->createForm(new RecipeType(), $recipe);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($recipe);
            $manager->persist($recipe->getPhoto());
            $manager->flush();

            return new JsonResponse(array(
                "status" => "success",
                "recipe" => array(
                    'id' => $recipe->getId(),
                    'image' => $recipe->getPhoto() ? $this->getPhotoUrl($recipe->getPhoto()) : '',
                    'name' => $recipe->getName(),
                    'description' => $recipe->getPublicDescription()
                ),
            ));
        }

        $errorSerializer = new FormErrorSerializer();

        return new JsonResponse(array(
            "status" => "error",
            "errors" => $errorSerializer->serializeFormErrors($form)
        ));
    }
}
