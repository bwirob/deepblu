<?php

namespace Netbaseteam\Blog\Block\Post;


class Productrelated extends \Magento\Catalog\Block\Product\AbstractProduct
{

  protected $_postDataHelper;
  protected $urlHelper;
  protected $_catalogLayer;
  protected $_productCollection;

  public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->urlHelper = $urlHelper;
        $this->_productCollection = $productCollection;
        parent::__construct(
            $context,
            $data
        );
    }

    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

  


  public function getRelatedProducts(){
    $post = $this->getPost();
    $relatedProduct = $post->getRelatedProducts();
    $relatedlist = array();
    if($relatedProduct){
      $relatedlist = explode('&', $relatedProduct);
    }

    return $relatedlist;
  }

  public function getListProduct(){
    $related = $this->getRelatedProducts();
    $collection = $this->_productCollection->create()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('entity_id', array('in'=>$related));
    return $collection;
  }

  public function hasRelatedProduct(){
    $productCollection = $this->getListProduct();
    if($productCollection->count()>0){
      return true;
    }
    return false;
  }

  public function getPost(){
    return $this->_coreRegistry->registry('post');
    
  }





    
}
