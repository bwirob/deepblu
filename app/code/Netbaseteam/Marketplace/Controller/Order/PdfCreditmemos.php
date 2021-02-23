<?php

namespace Netbaseteam\Marketplace\Controller\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PdfCreditmemos extends \Magento\Framework\App\Action\Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Invoice
     */
    protected $pdfCreditmemo;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Creditmemo $pdfCreditmemo
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        \Magento\Sales\Model\Order\Pdf\Creditmemo $pdfCreditmemo
    )
    {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfCreditmemo = $pdfCreditmemo;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        $creditmemo = $this->_objectManager->create('Magento\Sales\Api\CreditmemoRepositoryInterface')->get($creditmemoId);

        return $this->fileFactory->create(
            sprintf('creditmemo%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
            $this->pdfCreditmemo->getPdf(
                [$creditmemo]
            )->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
