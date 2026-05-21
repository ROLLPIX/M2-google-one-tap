<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class AddGoogleOneTapEmailAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private CustomerSetupFactory $customerSetupFactory;

    private AttributeSetFactory $attributeSetFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * @inheritDoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Add custom attribute to store Google email used for authentication
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'google_onetap_email',
            [
                'type' => 'varchar',
                'label' => 'Google One Tap Email',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'system' => false,
                'position' => 1000,
                'sort_order' => 1000,
            ]
        );

        // Add attribute to default attribute set
        $attributeSetId = $customerSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $customerSetup->getDefaultAttributeGroupId(Customer::ENTITY, $attributeSetId);

        $customerSetup->addAttributeToSet(
            Customer::ENTITY,
            $attributeSetId,
            $attributeGroupId,
            'google_onetap_email'
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [AddGoogleOneTapLinkedAttribute::class];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
