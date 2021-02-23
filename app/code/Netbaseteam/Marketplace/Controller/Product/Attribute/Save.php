<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Product\Attribute;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $attributeFactory;


    protected $_entityTypeId;


    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Netbaseteam\Marketplace\Model\AttributeFactory $mpAttributeFactory,
        \Magento\Catalog\Helper\Product $productHelper
    )
    {
        parent::__construct($context);
        $this->productHelper = $productHelper;
        $this->attributeFactory = $attributeFactory;
        $this->mpAttributeFactory = $mpAttributeFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $sellerId = $helper->getSellerId();
        if ($sellerId) {
            $data = $this->getRequest()->getPostValue();
            if ($data) {
                $setId = $this->getRequest()->getParam('set');

                $attributeSet = null;
                $attributeCode = $this->getRequest()->getParam('attribute_code');

                if (strlen($attributeCode) > 0) {
                    $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
                    if (!$validatorAttrCode->isValid($attributeCode)) {
                        $this->messageManager->addError(
                            __(
                                'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                                'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                                $attributeCode
                            )
                        );
                    }
                }
                $data['attribute_code'] = $attributeCode;

                /* @var $model \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
                $model = $this->attributeFactory->create();

                /**
                 * @todo add to helper and specify all relations for properties
                 */
                $data['source_model'] = $this->productHelper->getAttributeSourceModelByInputType(
                    $data['frontend_input']
                );
                $data['backend_model'] = $this->productHelper->getAttributeBackendModelByInputType(
                    $data['frontend_input']
                );


                $data += ['is_filterable' => 0, 'is_filterable_in_search' => 0, 'apply_to' => []];

                if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                    $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
                }

                $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
                if ($defaultValueField) {
                    $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
                }

                if (!$model->getIsUserDefined() && $model->getId()) {
                    // Unset attribute field for system attributes
                    unset($data['apply_to']);
                }

                $model->addData($data);

                $this->_entityTypeId = $this->_objectManager->create(
                    'Magento\Eav\Model\Entity'
                )->setType(
                    \Magento\Catalog\Model\Product::ENTITY
                )->getTypeId();

                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);


                try {
                    $model->save();
                    $this->saveMpAttribute($model->getId(), $sellerId);
                    $this->messageManager->addSuccess(__('Product attribute was successfully saved.'));

                    return $this->returnResult('*/*/new', ['error' => false]);
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    return $this->returnResult('*/*/new', ['error' => true]);
                }
            }
            return $this->returnResult('*/*/new', ['error' => false]);
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/registry',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }

    public function saveMpAttribute($id, $sellerId) {
        $model = $this->mpAttributeFactory->create();
        $model->setAttributeId($id);
        $model->setAttributeCode($this->getRequest()->getParam('attribute_code'));
        $model->setSellerId($sellerId);
        $model->save();
    }

    /**
     * @param string $path
     * @param array $params
     * @param array $response
     * @return \Magento\Framework\Controller\Result\Json|\Magento\Backend\Model\View\Result\Redirect
     */
    private function returnResult($path = '', array $params = [], array $response = [])
    {
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath($path, $params);

    }
}
