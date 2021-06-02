<?php
declare(strict_types=1);

namespace MageBig\AjaxFilter\Model\Layer;

use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{
    /**
     * Boolean filter name
     */
    const BOOLEAN_FILTER = 'boolean';
    const RATING_FILTER = 'rating';

    protected $helper;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    protected $filters = [];

    /**
     * @var string[]
     */
    protected $filterTypes = [
        self::ATTRIBUTE_FILTER => \Magento\Catalog\Model\Layer\Filter\Attribute::class,
        self::PRICE_FILTER     => \Magento\Catalog\Model\Layer\Filter\Price::class,
        self::DECIMAL_FILTER   => \Magento\Catalog\Model\Layer\Filter\Decimal::class,
        self::RATING_FILTER   => \MageBig\AjaxFilter\Model\Layer\Filter\Rating::class
    ];

    public function __construct(
        \MageBig\AjaxFilter\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        FilterableAttributeListInterface $filterableAttributes,
        array $filters = []
    ) {
        parent::__construct($objectManager, $filterableAttributes, $filters);
        $this->helper = $helper;
        /** Override default filter type models */
        $this->filterTypes = array_merge($this->filterTypes, $filters);
    }

    /**
     * Retrieve list of filters
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array|Filter\AbstractFilter[]
     */
    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {
        if (!count($this->filters)) {
            foreach ($this->filterableAttributes->getList() as $attribute) {
                $this->filters[] = $this->createAttributeFilter($attribute, $layer);
            }
            if ($this->helper->enableRatingFilter()) {
                $productCollection = $layer->getProductCollection();
                $this->helper->ratingCollection($productCollection);
                $this->filters[] = $this->objectManager->create(
                    $this->filterTypes[self::RATING_FILTER],
                    ['layer' => $layer]
                );
            }
        }
        return $this->filters;
    }

    /**
     * {@inheritDoc}
     */
    protected function getAttributeFilterClass(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        $filterClassName = parent::getAttributeFilterClass($attribute);

        if ($attribute->getBackendType() == 'varchar' && $attribute->getFrontendClass() == 'validate-number') {
            $filterClassName = $this->filterTypes[self::DECIMAL_FILTER];
        }

        if (($attribute->getFrontendInput() == 'boolean')
            && ($attribute->getSourceModel() == 'Magento\Eav\Model\Entity\Attribute\Source\Boolean')
            && isset($this->filterTypes[self::BOOLEAN_FILTER])) {
            $filterClassName = $this->filterTypes[self::BOOLEAN_FILTER];
        }

        return $filterClassName;
    }
}
