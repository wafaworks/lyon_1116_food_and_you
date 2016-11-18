<?php

namespace Soluti\SogenactifBundle\Service;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class RequestGenerator
{
    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * RequestGenerator constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;

        $resolver = new OptionsResolver();

        $resolver->setRequired(array(
            'merchant_id',
            'merchant_country',
            'amount',
            'currency_code',
            'pathfile',
        ));

        $resolver->setDefined(array(
            'merchant_id',
            'merchant_country',
            'amount',
            'currency_code',
            'pathfile',

            'transaction_id',

            // Affectation dynamique des autres parametres
            // Les champs et leur utilisation sont expliques dans le Dictionnaire des donnees
            'ncy_code',
            'normal_return_url',
            'cancel_return_url',
            'automatic_response_url',
            'language',
            'payment_means',
            'header_flag',
            'capture_day',
            'capture_mode',
            'bgcolor',
            'block_align',
            'block_order',
            'textcolor',
            'receipt_complement',
            'caddie',
            'customer_id',
            'customer_email',
            'customer_ip_address',
            'data',
            'return_context',
            'target',
            'order_id',

            // Les valeurs suivantes ne sont utilisables qu'en pre-production
            // Elles necessitent l'installation de vos fichiers sur le serveur de paiement
            'normal_return_logo',
            'cancel_return_logo',
            'submit_logo',
            'logo_id',
            'logo_id2',
            'advert',
            'background_id',
            'templatefile',
        ));

        $resolver->setDefaults(array(
            'normal_return_url' => $this->router->generate('soluti_sogenactif_normal', [], RouterInterface::ABSOLUTE_URL),
            'cancel_return_url' => $this->router->generate('soluti_sogenactif_cancel', [], RouterInterface::ABSOLUTE_URL),
            'automatic_response_url' => $this->router->generate('soluti_sogenactif_auto', [], RouterInterface::ABSOLUTE_URL),
        ));

        $this->resolver = $resolver;
    }

    public function getRequestParameters(array $options)
    {
        $parsedOptions = $this->resolver->resolve($options);

        $result = '';
        foreach ($parsedOptions as $key => $value) {
            $result .= " $key=$value";
        }

        return $result;
    }
}
