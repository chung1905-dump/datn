<?php

namespace MageBig\AjaxFilter\Block\Navigation\Renderer;

use Magento\Store\Model\ScopeInterface;
use MageBig\AjaxFilter\Model\Layer\Filter\Price;
use Magento\Catalog\Model\Layer\Filter\DataProvider\Price as PriceDataProvider;

class PriceSlider extends Slider
{
    /**
     * The Data role, used for Javascript mapping of slider Widget
     *
     * @var string
     */
    protected $dataRole = "range-price-slider";

    /**
     * {@inheritDoc}
     */
    protected function canRenderFilter()
    {
        return $this->getFilter() instanceof Price;
    }

    /**
     * @return array
     */
    protected function getFieldFormat()
    {
        return $this->localeFormat->getPriceFormat();
    }

    /**
     * {@inheritDoc}
     */
    protected function getConfig()
    {
        $config = parent::getConfig();

        if ($this->isManualCalculation() && ($this->getStepValue() > 0)) {
            $config['step'] = $this->getStepValue();
        }

        if ($this->getFilter()->getCurrencyRate()) {
            $config['rate'] = $this->getFilter()->getCurrencyRate();
        }

        return $config;
    }

    public function enablePriceSlider() {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(\MageBig\AjaxFilter\Helper\Data::class);
        return $helper->enablePriceSlider();
    }

    /**
     * Returns min value of the slider.
     *
     * @return int
     */
    public function getMinValue()
    {
        $minValue = $this->getFilter()->getMinValue();

        if ($this->isManualCalculation() && ($this->getStepValue() > 0)) {
            $stepValue = $this->getStepValue();
            $minValue  = floor($minValue / $stepValue) * $stepValue;
        }

        return $minValue;
    }

    /**
     * Returns max value of the slider.
     *
     * @return int
     */
    public function getMaxValue()
    {
        $maxValue = $this->getFilter()->getMaxValue() + 1;

        if ($this->isManualCalculation() && ($this->getStepValue() > 0)) {
            $stepValue = $this->getStepValue();
            $maxValue  = ceil($maxValue / $stepValue) * $stepValue;
        }

        return $maxValue;
    }

    /**
     * Check if price interval is manually set in the configuration
     *
     * @return bool
     */
    private function isManualCalculation()
    {
        $result      = false;
        $calculation = $this->_scopeConfig->getValue(PriceDataProvider::XML_PATH_RANGE_CALCULATION, ScopeInterface::SCOPE_STORE);

        if ($calculation === PriceDataProvider::RANGE_CALCULATION_MANUAL) {
            $result = true;
        }

        return $result;
    }

    /**
     * Retrieve the value for "Default Price Navigation Step".
     *
     * @return int
     */
    private function getStepValue()
    {
        $value = $this->_scopeConfig->getValue(PriceDataProvider::XML_PATH_RANGE_STEP, ScopeInterface::SCOPE_STORE);

        return (int) $value;
    }
}
