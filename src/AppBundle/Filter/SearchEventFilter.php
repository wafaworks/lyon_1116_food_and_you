<?php

namespace AppBundle\Filter;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class SearchEventFilter extends BaseFilter implements FilterInterface
{
    public function __construct(array $params = array())
    {
        $parsedParams = array_merge(
            array_key_exists('app_search', $params) ? (array) $params['app_search'] : array(),
            array(
                'page' => array_key_exists('page', $params) ? $params['page'] : 1
            )
        );
        parent::__construct($parsedParams);
    }

    protected function getAvailableFilters()
    {
        return array_merge(
            parent::getAvailableFilters(),
            array(
                'city',
                'eventDate',
                'restaurant',
                'participatorType',
                'page'
            )
        );
    }
}
