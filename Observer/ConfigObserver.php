<?php

namespace Gerencianet\Magento2\Observer;

use Exception;
use Gerencianet\Gerencianet;
use Gerencianet\Magento2\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Filesystem\DirectoryList;

class ConfigObserver implements ObserverInterface
{

    /** @var Data */
    private $_helperData;

    /** @var StoreManagerInterface */
    private $_storeMagerInterface;

    /** @var Config */
    private $_resourceConfig;

    public function __construct(
        Data $helperData,
        StoreManagerInterface $storeManager,
        Config $resourceConfig,
        DirectoryList $dl
    ) {
        $this->_helperData = $helperData;
        $this->_storeMagerInterface = $storeManager;
        $this->_resourceConfig = $resourceConfig;
        $this->_dir = $dl;
    }

    public function execute(Observer $observer)
    {
        if ($this->_helperData->isPixActive()) {
            $this->cadastraWebhookPix($observer);
        }
        if ($this->_helperData->isOpenFinanceActive()) {
            $this->cadastraWebhookOpenFinance($observer);
        }
    }

    public function cadastraWebhookPix(Observer $observer)
    {
        $this->defaultName($observer);

        $skipMtls = (bool)$this->_helperData->getSkipMtls() ? 'false' : 'true';

        $options = $this->_helperData->getOptions();
        $options['pix_cert'] = $this->getCertificadoPath();
        $options['headers'] = array('x-skip-mtls-checking' => $skipMtls);

        $params = ['chave' => $this->_helperData->getChavePix()];
        $body = ['webhookUrl' => $this->getNotificationUrlPix()];

        try {
            $api = Gerencianet::getInstance($options);
            $pix = $api->pixConfigWebhook($params, $body);

            //echo print_r($pix);

            //print_r("'<pre>' . json_encode($pix, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</pre>'");
            //exit();

        } catch (Exception $e) {
            $this->_helperData->logger($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function cadastraWebhookOpenFinance(Observer $observer)
    {
        $this->defaultName($observer);



        $options = $this->_helperData->getOptions();
        $options['pix_cert'] = $this->getCertificadoPath();


        $callbackUrl = $this->getNotificationUrlOpenFinance();

        $redirectUrl = $this->getRedirectnUrlOpenFinance();




        $hash = hash('sha256', $options['client_id']);

        $webhookSecurity = ["type" => "hmac", "hash" => $hash];



        $body = [

            'webhookURL' => $callbackUrl,

            'redirectURL' => $redirectUrl,

            'webhookSecurity' => $webhookSecurity,
            'processPayment' =>'async'

        ];

        try {
            $api = new Gerencianet($options);
            $openFinance = $api->ofConfigUpdate($params = [], $body);

            //echo print_r($pix);

            //print_r("'<pre>' . json_encode($pix, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</pre>'");
            //exit();

        } catch (Exception $e) {
            $this->_helperData->logger($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function defaultName(Observer $observer)
    {
        $path = "payment/gerencianet_pix/certificado";
        $value = "certificate.pem";
        $scope = "default";
        $scopeId = 0;

        if (!empty($this->getCertificadoPath()) && in_array($path, $observer->getEvent()->getData()['changed_paths'])) {
            $this->_resourceConfig->saveConfig($path, $value, $scope, $scopeId);
        }
    }

    public function getCertificadoPath(): string
    {
        $certificadopath = $this->_dir->getPath('media') . "/test/" . $this->_helperData->getPixCert();
        return file_exists($certificadopath) ? $certificadopath : false;
    }

    public function getNotificationUrlPix(): string
    {
        return $this->_storeMagerInterface->getStore()->getBaseUrl() . 'gerencianet/notification/updatepixstatus';
    }
    public function getNotificationUrlOpenFinance(): string
    {
        return $this->_storeMagerInterface->getStore()->getBaseUrl() . 'gerencianet/notification/updateopenfinancestatus';
    }
    public function getRedirectnUrlOpenFinance(): string
    {
        return $this->_storeMagerInterface->getStore()->getBaseUrl() . 'gerencianet/redirect/redirectopenfinance';
    }
}
