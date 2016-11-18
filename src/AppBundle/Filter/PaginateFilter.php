<?php

namespace AppBundle\Filter;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class PaginateFilter extends BaseFilter implements FilterInterface
{
    public function __construct(array $params = array())
    {
        $parsedParams = array(
            'page' => array_key_exists('page', $params) ? $params['page'] : 1
        );

        parent::__construct($parsedParams);
    }

    protected function getAvailableFilters()
    {
        return array_merge(
            parent::getAvailableFilters(),
            array(
                'page'
            )
        );
    }
}
