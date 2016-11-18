<?php

namespace AppBundle\Filter;

/**
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 */
class BaseFilter implements FilterInterface
{
    protected $filters;

    /**
     * @inheritdoc
     */
    public function __construct(array $params = array())
    {
        $this->filters = array();
        foreach ($this->getAvailableFilters() as $filter) {
            if (isset($params[$filter])) {
                $this->filters[$filter] = $params[$filter];
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function addFilter($key, $value)
    {
        $this->filters[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    protected function getAvailableFilters()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function isFilterSet($filterName)
    {
        return (array_key_exists($filterName, $this->filters) && (!empty($this->filters[$filterName])));
    }

    /**
     * @inheritdoc
     */
    public function getFilter($filterName)
    {
        if ($this->isFilterSet($filterName)) {
            return $this->filters[$filterName];
        }

        return null;
    }
}
