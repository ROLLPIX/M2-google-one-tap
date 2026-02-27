<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ButtonShape implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'rectangular', 'label' => __('Rectangular')],
            ['value' => 'pill', 'label' => __('Pill')],
            ['value' => 'circle', 'label' => __('Circle')],
            ['value' => 'square', 'label' => __('Square')],
        ];
    }
}
