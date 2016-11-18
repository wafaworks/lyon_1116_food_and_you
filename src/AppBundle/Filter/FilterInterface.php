<?php

namespace AppBundle\Filter;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
interface FilterInterface
{
    /**
     * Initialize filter with array from Request
     *
     * @param array $initialFilters
     */
    public function __construct(array $initialFilters = array());

    /**
     * Add a custom filter on demand
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function addFilter($key, $value);

    /**
     * Return array of valid filters with values
     *
     * @return array
     */
    public function getFilters();

    /**
     * @param string $filterName
     *
     * @return boolean
     */
    public function isFilterSet($filterName);

    /**
     * @param string $filterName
     *
     * @return mixed
     */
    public function getFilter($filterName);
}
