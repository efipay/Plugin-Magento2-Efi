<?php

declare(strict_types=1);

namespace Gerencianet\Magento2\Controller\Ajax;

use Gerencianet\Magento2\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Gerencianet\Magento2\Block\Checkout\OpenFinanceParticipants;

class ListParticipantsOpenFinance extends Action implements HttpGetActionInterface {

  /** @var Data */
  private $_helperData;

  protected $openFinanceParticipants;

  /** @var JsonFactory */
  private $_jsonResultFactory;

  /**
   * @param RequestInterface $request
   * @param Data $helperData
   * @param JsonFactory $jsonResultFactory
   */
  public function __construct( 
      Context $context, 
      Data $helperData, 
      JsonFactory $jsonResultFactory,
      OpenFinanceParticipants $openFinanceParticipants 
  ) {
    $this->_helperData = $helperData;
    $this->_jsonResultFactory = $jsonResultFactory;
    $this->openFinanceParticipants = $openFinanceParticipants;
    
    parent::__construct($context);
  }

  /** @inheritdoc */
  public function execute() {

    $participants = $this->openFinanceParticipants->getParticipants();
    $this->getResponse()->setBody(json_encode($participants));
    
  }
}