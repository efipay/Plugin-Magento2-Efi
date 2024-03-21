<?php
declare(strict_types=1);

namespace Gerencianet\Magento2\Controller\Redirect;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order;
use Gerencianet\Magento2\Helper\Data;

class RedirectOpenFinance extends Action implements CsrfAwareActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /** @var SearchCriteriaBuilder */
    protected $_searchCriteriaBuilder;

    /** @var OrderRepositoryInterface */
    protected $_orderRepository;

    /** @var Data */
    protected $_helperData;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        Data $helperData,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_helperData = $helperData;
    }

    /**
     * Execute method
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // Get identificadorPagamento from query params
        $identificadorPagamento = $this->getRequest()->getParam('identificadorPagamento');

        
        // Load page
        $resultPage = $this->resultPageFactory->create(true);
        $resultPage->getConfig()->getTitle()->prepend(__('Redirect to Open Finance Page'));
         // Criar um bloco para o cabeçalho
         $headerBlock = $resultPage->getLayout()->getBlock('header');
        
         // Criar um bloco para o rodapé
         $footerBlock = $resultPage->getLayout()->getBlock('footer');
        $block = $resultPage->getLayout()->createBlock('Magento\Framework\View\Element\Template');
        $block->setTemplate('Gerencianet_Magento2::redirect/redirectopenfinance.phtml');

        $block->setData('identificadorPagamento', $identificadorPagamento);
     
        
        $htmlContent = $block->toHtml();
    
    // Return the HTML content
        return $this->getResponse()->setBody($htmlContent);
       
    }

   


    /** * @inheritDoc */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /** * @inheritDoc */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
