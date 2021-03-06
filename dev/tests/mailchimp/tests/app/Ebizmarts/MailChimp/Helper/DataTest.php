<?php

class Ebizmarts_MailChimp_Helper_DataTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Mage::app('default');
    }

    public function testGetLastDateOfPurchase()
    {
        /**
         * @var \Ebizmarts_MailChimp_Helper_Data $helperMock
         */
        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getOrderCollectionByCustomerEmail'))
            ->getMock();

        $helperMock->expects($this->once())->method('getOrderCollectionByCustomerEmail')->with("john@example.com")
            ->willReturn(array());

        $this->assertNull($helperMock->getLastDateOfPurchase("john@example.com"));
    }

    public function testCustomMergeFieldAlreadyExists()
    {
        /**
         * @var \Ebizmarts_MailChimp_Helper_Data $helperMock
         */
        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getCustomMergeFields'))
            ->getMock();

        $helperMock->expects($this->once())->method('getCustomMergeFields')->with(0, "default")
            ->willReturn(
                array(
                    array(
                        "value" => "FNAME"
                    )
                )
            );

        $this->assertTrue($helperMock->customMergeFieldAlreadyExists("FNAME", 0, "default"));
    }

    public function testIsCheckoutSubscribeEnabled()
    {
        /**
         * @var \Ebizmarts_MailChimp_Helper_Data $helperMock
         */
        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('isMailChimpEnabled', 'getCheckoutSubscribeValue'))
            ->getMock();
        $helperMock->expects($this->once())->method('isMailChimpEnabled')->with(1, 'stores')
            ->willReturn(true);

        $helperMock->expects($this->once())->method('getCheckoutSubscribeValue')->with(1, 'stores')
            ->willReturn(Ebizmarts_MailChimp_Model_System_Config_Source_Checkoutsubscribe::NOT_CHECKED_BY_DEFAULT);

        $this->assertTrue($helperMock->isCheckoutSubscribeEnabled(1, "stores"));
    }

    public function testDeleteStore()
    {
        $scopeId = 1;
        $scope = 'stores';
        $mailchimpStoreId = 'a18a1a8a1aa7aja1a';
        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMCStoreId', 'getApiStores', 'getGeneralList', 'deleteCurrentWebhook', 'deleteLocalMCStoreData'))
            ->getMock();
        $apiStoresMock = $this->getMockBuilder(Ebizmarts_MailChimp_Model_Api_Stores::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helperMock->expects($this->once())->method('getMCStoreId')->with($scopeId, $scope)->willReturn($mailchimpStoreId);
        $helperMock->expects($this->once())->method('getApiStores')->willReturn($apiStoresMock);
        $helperMock->expects($this->once())->method('getGeneralList')->with($scopeId, $scope)->willReturn('listId');
        $helperMock->expects($this->once())->method('deleteCurrentWebhook')->with($scopeId, $scope, 'listId');
        $helperMock->expects($this->once())->method('deleteLocalMCStoreData')->with($mailchimpStoreId, $scopeId, $scope);

        $helperMock->deleteStore($scopeId, $scope);
    }

    public function testAddResendFilter()
    {
        $storeId = 1;
        /**
         * @var \Ebizmarts_MailChimp_Helper_Data $helperMock
         */
        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getResendEnabled', 'getResendTurn', 'getOrderResendLastId'))
            ->getMock();

        $orderCollectionMock = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helperMock->expects($this->once())->method('getResendEnabled')->with($storeId)->willReturn(1);
        $helperMock->expects($this->once())->method('getResendTurn')->with($storeId)->willReturn(1);
        $helperMock->expects($this->once())->method('getOrderResendLastId')->with($storeId);

        $helperMock->addResendFilter($orderCollectionMock, $storeId);
    }

    public function testHandleResendFinish()
    {
        $scopeId = 1;
        $scope = 'stores';
        /**
         * @var \Ebizmarts_MailChimp_Helper_Data $helperMock
         */
        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('allResendItemsSent', 'deleteResendConfigValues'))
            ->getMock();

        $helperMock->expects($this->once())->method('allResendItemsSent')->with($scopeId, $scope)->willReturn(1);
        $helperMock->expects($this->once())->method('deleteResendConfigValues')->with($scopeId, $scope);

        $helperMock->handleResendFinish($scopeId, $scope);
    }

    public function testDeleteLocalMCStoreData()
    {
        $scope = 'default';
        $scopeId = 0;
        $mailchimpStoreId = 'a1s2d3f4g5h6j7k8l9n0';

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getConfig'))
            ->getMock();

        $configMock = $this->getMockBuilder(Mage_Core_Model_Config::class)
            ->disableOriginalConstructor()
            ->setMethods(array('deleteConfig', 'cleanCache'))
            ->getMock();

        $helperMock->expects($this->once())->method('getConfig')->willReturn($configMock);

        $param1 = array(Ebizmarts_MailChimp_Model_Config::GENERAL_MCSTOREID, $scope, $scopeId);

        $param2 = array(Ebizmarts_MailChimp_Model_Config::GENERAL_MCISSYNCING, $scope, $scopeId);

        $param3 = array(Ebizmarts_MailChimp_Model_Config::GENERAL_MCSTORE_RESETED, $scope, $scopeId);

        $param4 = array(Ebizmarts_MailChimp_Model_Config::GENERAL_ECOMMMINSYNCDATEFLAG, $scope, $scopeId);

        $param5 = array(Ebizmarts_MailChimp_Model_Config::ECOMMERCE_MC_JS_URL, $scope, $scopeId);

        $param6 = array(Ebizmarts_MailChimp_Model_Config::ECOMMERCE_SYNC_DATE . "_$mailchimpStoreId", 'default', 0);

        $configMock->expects($this->exactly(6))->method('deleteConfig')->withConsecutive($param1, $param2, $param3, $param4, $param5, $param6);
        $configMock->expects($this->once())->method('cleanCache');

        $helperMock->deleteLocalMCStoreData($mailchimpStoreId, $scopeId, $scope);
    }

    public function testGetDateSyncFinishByStoreId()
    {
        $scope = 'default';
        $scopeId = 0;
        $mailchimpStoreId = 'a1s2d3f4g5h6j7k8l9n0';

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMCStoreId', 'getConfigValueForScope'))
            ->getMock();

        $helperMock->expects($this->once())->method('getMCStoreId')->with($scopeId, $scope)->willReturn($mailchimpStoreId);
        $helperMock->expects($this->once())->method('getConfigValueForScope')->with(Ebizmarts_MailChimp_Model_Config::ECOMMERCE_SYNC_DATE."_$mailchimpStoreId", $scopeId, $scope);

        $helperMock->getDateSyncFinishByStoreId($scopeId, $scope);
    }

    public function testHandleResendDataBefore()
    {
        $scopeId = 0;
        $scope = 'default';
        $configMock = $this->getMockBuilder(Mage_Core_Model_Config_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getScope', 'getScopeId'))
            ->getMock();
        $configEntries = array();

        /**
         * @var \Ebizmarts_MailChimp_Helper_Data $helperMock
         */
        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getResendTurnConfigCollection', 'getResendTurn', 'getResendEnabled', 'setIsSyncingIfFinishedPerScope'))
            ->getMock();

        $collectionMock = $this->getMockBuilder(Mage_Core_Model_Resource_Config_Data_Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configMock->expects($this->once())->method('getScope')->willReturn($scope);
        $configMock->expects($this->once())->method('getScopeId')->willReturn($scopeId);
        $configEntries [] = $configMock;
        $collectionMock->expects($this->once())->method("getIterator")->willReturn(new ArrayIterator($configEntries));

        $helperMock->expects($this->once())->method('getResendTurnConfigCollection')->willReturn($collectionMock);
        $helperMock->expects($this->once())->method('getResendTurn')->with($scopeId, $scope)->willReturn(1);
        $helperMock->expects($this->once())->method('getResendEnabled')->with($scopeId, $scope)->willReturn(1);
        $helperMock->expects($this->once())->method('setIsSyncingIfFinishedPerScope')->with(true, $scopeId, $scope);

        $helperMock->handleResendDataBefore();
    }

    public function testHandleResendDataAfter()
    {
        $scopeId = 0;
        $scope = 'default';
        $configMock = $this->getMockBuilder(Mage_Core_Model_Config_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getScope', 'getScopeId'))
            ->getMock();
        $configEntries = array();

        /**
         * @var \Ebizmarts_MailChimp_Helper_Data $helperMock
         */
        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getResendTurnConfigCollection', 'getResendTurn', 'setIsSyncingIfFinishedPerScope', 'setResendTurn', 'handleResendFinish'))
            ->getMock();

        $collectionMock = $this->getMockBuilder(Mage_Core_Model_Resource_Config_Data_Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configMock->expects($this->once())->method('getScope')->willReturn($scope);
        $configMock->expects($this->once())->method('getScopeId')->willReturn($scopeId);
        $configEntries [] = $configMock;
        $collectionMock->expects($this->once())->method("getIterator")->willReturn(new ArrayIterator($configEntries));
        $helperMock->expects($this->once())->method('getResendTurnConfigCollection')->willReturn($collectionMock);
        $helperMock->expects($this->once())->method('getResendTurn')->with($scopeId, $scope)->willReturn(1);
        $helperMock->expects($this->once())->method('setIsSyncingIfFinishedPerScope')->with(false, $scopeId, $scope);

        $helperMock->expects($this->once())->method('setResendTurn')->with(0, $scopeId, $scope);
        $helperMock->expects($this->once())->method('handleResendFinish')->with($scopeId, $scope);

        $helperMock->handleResendDataAfter();
    }

    public function testResetMCEcommerceData()
    {
        $scopeId = 0;
        $scope = 'default';
        $deleteDataInMailchimp = true;

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getGeneralList', 'getMCStoreId', 'removeEcommerceSyncData', 'resetCampaign', 'clearErrorGrid', 'deleteStore', 'isEcomSyncDataEnabled'))
            ->getMock();

        $helperMock->expects($this->once())->method('getGeneralList')->with($scopeId, $scope)->willReturn('a1s2d3f4g5');
        $helperMock->expects($this->once())->method('getMCStoreId')->with($scopeId, $scope)->willReturn('q1w2e3r4t5y6u7i8o9p0');
        $helperMock->expects($this->once())->method('removeEcommerceSyncData')->with($scopeId, $scope);
        $helperMock->expects($this->once())->method('resetCampaign')->with($scopeId, $scope);
        $helperMock->expects($this->once())->method('clearErrorGrid')->with($scopeId, $scope, true);
        $helperMock->expects($this->once())->method('deleteStore')->with($scopeId, $scope);

        $helperMock->resetMCEcommerceData($scopeId, $scope, $deleteDataInMailchimp);
    }

    public function testSaveMailChimpConfig()
    {
        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getConfig'))
            ->getMock();

        $configMock = $this->getMockBuilder(Mage_Core_Model_Config_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('saveConfig', 'cleanCache'))
            ->getMock();


        $helperMock->expects($this->exactly(2))->method('getConfig')->willReturn($configMock);
        $configMock->expects($this->once())->method('saveConfig')->with(Ebizmarts_MailChimp_Model_Config::GENERAL_MIGRATE_FROM_116, 1, 'default', 0);
        $configMock->expects($this->once())->method('cleanCache');

        $helperMock->saveMailChimpConfig(array(array(Ebizmarts_MailChimp_Model_Config::GENERAL_MIGRATE_FROM_116, 1)), 0, 'default');
    }

    public function testHandleWebhookChange()
    {
        $scopeId = 0;
        $scope = 'default';
        $realScopeArray = array('scope_id' => 0, 'scope' => 'default');
        $listId = 'a1s2d3f4g5';

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getRealScopeForConfig', 'getGeneralList', 'deleteCurrentWebhook', 'isMailChimpEnabled', 'createNewWebhook'))
            ->getMock();

        $helperMock->expects($this->once())->method('getRealScopeForConfig')->with(Ebizmarts_MailChimp_Model_Config::GENERAL_LIST, $scopeId, $scope)->willReturn($realScopeArray);
        $helperMock->expects($this->once())->method('getGeneralList')->with($scopeId, $scope)->willReturn($listId);
        $helperMock->expects($this->once())->method('deleteCurrentWebhook')->with($realScopeArray['scope_id'], $realScopeArray['scope'], $listId);
        $helperMock->expects($this->once())->method('isMailChimpEnabled')->with($scopeId, $scope)->willReturn(1);
        $helperMock->expects($this->once())->method('createNewWebhook')->with($scopeId, $scope, $listId);

        $helperMock->handleWebhookChange($scopeId, $scope);
    }

    public function testCreateWebhookIfRequired()
    {
        $scopeId = 0;
        $scope = 'default';
        $webhookId = null;

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getWebhookId', 'handleWebhookChange'))
            ->getMock();

        $helperMock->expects($this->once())->method('getWebhookId')->with($scopeId, $scope)->willReturn($webhookId);
        $helperMock->expects($this->once())->method('handleWebhookChange')->with($scopeId, $scope);

        $helperMock->createWebhookIfRequired($scopeId, $scope);
    }

    public function testGetImageUrlById()
    {
        $productId = 1;
        $magentoStoreId = 1;
        $defaultStoreId = 0;
        $imageSize = 'image';
        $upperCaseImage = 'getImageUrl';

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getProductResourceModel', 'getProductModel', 'getImageSize', 'getCurrentStoreId', 'setCurrentStore', 'getImageFunctionName'))
            ->getMock();

        $productModelMock = $this->getMockBuilder(Mage_Catalog_Model_Product::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setData', 'getImageUrl'))
            ->getMock();

        $productResourceModelMock = $this->getMockBuilder(Mage_Catalog_Model_Resource_Product::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getAttributeRawValue'))
            ->getMock();

        $imageModelMock = $this->getMockBuilder(Mage_Media_Model_Image::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helperMock->expects($this->once())->method('getProductResourceModel')->willReturn($productResourceModelMock);
        $helperMock->expects($this->once())->method('getProductModel')->willReturn($productModelMock);
        $helperMock->expects($this->once())->method('getImageSize')->with($magentoStoreId)->willReturn($imageSize);

        $productResourceModelMock->expects($this->once())->method('getAttributeRawValue')->with($productId, $imageSize, $magentoStoreId)->willReturn($imageModelMock);

        $productModelMock->expects($this->once())->method('setData')->with($imageSize, $imageModelMock);
        $productModelMock->expects($this->once())->method('getImageUrl')->willReturn('ImageUrl');

        $helperMock->expects($this->once())->method('getCurrentStoreId')->willReturn($defaultStoreId);

        $helperMock->expects($this->exactly(2))->method('setCurrentStore')->withConsecutive(array($magentoStoreId), array($defaultStoreId));

        $helperMock->expects($this->once())->method('getImageFunctionName')->with($imageSize)->willReturn($upperCaseImage);

        $return = $helperMock->getImageUrlById($productId, $magentoStoreId);

        $this->assertEquals($return, 'ImageUrl');
    }

    public function testGetImageFunctionName(){
        $imageSize = 'image_size';
        $imageArray = array('image', 'size');
        $upperCaseImage = 'ImageSize';
        $functionName = 'getImageSizeUrl';

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setImageSizeVarToArray', 'setWordToCamelCase', 'setFunctionName'))
            #->setMethods()
            ->getMock();

        $helperMock->expects($this->once())->method('setImageSizeVarToArray')->with($imageSize)->willReturn($imageArray);
        $helperMock->expects($this->once())->method('setWordToCamelCase')->with($imageArray)->willReturn($upperCaseImage);
        $helperMock->expects($this->once())->method('setFunctionName')->with($upperCaseImage)->willReturn($functionName);

        $result = $helperMock->getImageFunctionName($imageSize);

        $this->assertEquals($result, 'getImageSizeUrl');

    }

    public function testSetImageSizeVarToArray(){
        $imageSize = 'image_size';

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $result = $helperMock->setImageSizeVarToArray($imageSize);

        $this->assertEquals($result, array('image', 'size'));
    }

    public function testSetWordToCamelCase(){
        $imageArray = array('image', 'size');

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $result = $helperMock->setWordToCamelCase($imageArray);

        $this->assertEquals($result, 'ImageSize');
    }

    public function testSetFunctionName(){
        $upperCaseImage = 'ImageSize';

        $helperMock = $this->getMockBuilder(Ebizmarts_MailChimp_Helper_Data::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $result = $helperMock->setFunctionName($upperCaseImage);

        $this->assertEquals($result, 'getImageSizeUrl');
    }
}
