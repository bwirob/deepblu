<?php

namespace Netbaseteam\Marketplace\Controller\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PdfInvoices extends \Magento\Framework\App\Action\Action
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
    protected $pdfInvoice;

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
     * @param Invoice $pdfInvoice
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        \Magento\Sales\Model\Order\Pdf\Invoice $pdfInvoice
    )
    {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfInvoice = $pdfInvoice;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $invoices = $this->_objectManager->create('Magento\Sales\Api\InvoiceRepositoryInterface')->get($invoiceId);

        return $this->fileFactory->create(
            sprintf('invoice%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
            $this->pdfInvoice->getPdf(
                [$invoices]
            )->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
