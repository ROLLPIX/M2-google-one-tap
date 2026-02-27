<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ButtonLogoAlignment implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'left', 'label' => __('Left')],
            ['value' => 'center', 'label' => __('Center')],
        ];
    }
}
