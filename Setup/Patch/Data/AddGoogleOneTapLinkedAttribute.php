<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class AddGoogleOneTapLinkedAttribute implements DataPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CustomerSetupFactory $customerSetupFactory,
        private readonly AttributeSetFactory $attributeSetFactory
    ) {}

    /**
     * @inheritDoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Add custom attribute to track when Google One Tap was first linked
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'google_onetap_linked_at',
            [
                'type' => 'datetime',
                'label' => 'Google One Tap Linked At',
                'input' => 'date',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'system' => false,
                'position' => 999,
                'sort_order' => 999,
            ]
        );

        // Add attribute to default attribute set
        $attributeSetId = $customerSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $customerSetup->getDefaultAttributeGroupId(Customer::ENTITY, $attributeSetId);

        $customerSetup->addAttributeToSet(
            Customer::ENTITY,
            $attributeSetId,
            $attributeGroupId,
            'google_onetap_linked_at'
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
