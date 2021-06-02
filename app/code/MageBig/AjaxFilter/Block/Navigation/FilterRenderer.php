<?php
/**
 * Catalog layer filter renderer
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageBig\AjaxFilter\Block\Navigation;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\LayeredNavigation\Block\Navigation\FilterRendererInterface;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;

/**
 * Catalog layer filter renderer
 *
 * @api
 * @since 100.0.2
 */
class FilterRenderer extends AbstractBlock implements FilterRendererInterface
{
    /**
     * {@inheritDoc}
     */
    public function render(FilterInterface $filter)
    {
        $this->setFilter($filter);

        return $this->_toHtml();
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * {@inheritDoc}
     */
    public function _toHtml()
    {
        $html = '';

        foreach ($this->getChildNames() as $childName) {
            if ($html === '') {
                $renderer = $this->getChildBlock($childName);
                $html = $renderer->render($this->getFilter());
            }
        }

        return $html;
    }
}
