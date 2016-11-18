<?php

namespace Soluti\TranslationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Catalogue\MergeOperation;
use Symfony\Component\Translation\MessageCatalogue;

class ProfilerController extends Controller
{
    /**
     * Save the selected translation to resources.
     *
     * @Route("/{token}/translation/save", name="_profiler_save_translations")
     *
     * @return Response A Response instance
     */
    public function saveAction(Request $request, $token)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('_profiler', ['token' => $token]);
        }

        $profiler = $this->get('profiler');
        $profiler->disable();
        $selected = $request->request->get('selected');

        if (!$selected || count($selected) == 0) {
            return new Response('No key selected.');
        }

        $profile = $profiler->loadProfile($token);
        $all = $profile->getCollector('translation');
        $toSave = array_intersect_key($all->getMessages(), array_flip($selected));


        $this->save($toSave);
        // I'm using a custim Loco service, doing API calls for each message.
        if (true) {
            return new Response(sprintf("%s translation keys saved!", count($selected)));
        } else {
            return new Response("Can't save the translations.");
        }
    }

    private function save($data, $format = 'xlf', $locale = 'fr')
    {
        // check format
        $writer = $this->get('translation.writer');
        $supportedFormats = $writer->getFormats();
        if (!in_array($format, $supportedFormats)) {
            throw new \Exception('Unsupported format');
        }

        // Define Root Path to App folder
        $kernel = $this->get('kernel');
        $transPaths = array($kernel->getRootDir().'/Resources/');

        // load new messages into MessageCatalogue
        $extractedCatalogue = new MessageCatalogue($locale);
        foreach ($data as $message) {
            $extractedCatalogue->set($message['id'], $message['translation'], $message['domain']);
        }

        // extract existing messages
        $extractor = $this->get('translation.extractor');
        foreach ($transPaths as $path) {
            $path .= 'views';
            if (is_dir($path)) {
                $extractor->extract($path, $extractedCatalogue);
            }
        }

        // load any existing messages from the translation files
        $currentCatalogue = new MessageCatalogue($locale);
        $loader = $this->get('translation.loader');
        foreach ($transPaths as $path) {
            $path .= 'translations';
            if (is_dir($path)) {
                $loader->loadMessages($path, $currentCatalogue);
            }
        }

        // process catalogues
        $operation = new MergeOperation($currentCatalogue, $extractedCatalogue);

        // Exit if no messages found.
        if (!count($operation->getDomains())) {
            throw new \Exception('No translation messages were found.');
        }

        //$writer->disableBackup();


        // save the files
        $bundleTransPath = false;
        foreach ($transPaths as $path) {
            $path .= 'translations';
            if (is_dir($path)) {
                $bundleTransPath = $path;
            }
        }

        if (!$bundleTransPath) {
            $bundleTransPath = end($transPaths).'translations';
        }

        $writer->writeTranslations(
            $operation->getResult(),
            $format,
            array(
                'path' => $bundleTransPath,
                'default_locale' => $this->getParameter('kernel.default_locale')
            )
        );
    }
}
