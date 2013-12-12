<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    CosmoCommerce
 * @package     CosmoCommerce_Sinapay
 * @copyright   Copyright (c) 2009-2013 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @contact :
 * T: +86-021-66346672
 * L: Shanghai,China
 * M:sales@cosmocommerce.com
 */
class CosmoCommerce_Sinapay_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'sinapay_payment';
    protected $_formBlockType = 'sinapay/form';
    
    //测试URL: http://testpay.sina.com.cn/openserver/1/order/receiver
    //线上URL: http://pay.sina.com.cn/openserver/1/order/receiver
    //测试URL: http://testpay.sina.com.cn/openserver/1/order/query
    //线上URL: http://pay.sina.com.cn/openserver/1/order/query
    
	protected $_gateway="https://testgate.pay.sina.com.cn/acquire-order-channel/gateway/receiveOrderLoading.htm?";
	//https://testmas.weibopay.com/acquire-order-channel/gateway/receiveOrderLoading.htm
     
    // Sinapay return codes of payment
    const RETURN_CODE_ACCEPTED      = 'Success';
    const RETURN_CODE_TEST_ACCEPTED = 'Success';
    const RETURN_CODE_ERROR         = 'Fail';

    // Payment configuration
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    // Order instance
    protected $_order = null;

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
     */
    public function getSinapayUrl()
    {
        $url = $this->_gateway;
        return $url;
    }

    /**
     *  Return back URL
     *
     *  @return	  string URL
     */
	protected function getReturnURL()
	{
		return Mage::getUrl('sinapay/payment/notify/', array('_secure' => true));
	}

	/**
	 *  Return URL for Sinapay success response
	 *
	 *  @return	  string URL
	 */
	protected function getSuccessURL()
	{
		return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
	}

    /**
     *  Return URL for Sinapay failure response
     *
     *  @return	  string URL
     */
    protected function getErrorURL()
    {
        return Mage::getUrl('sinapay/payment/error', array('_secure' => true));
    }

	/**
	 *  Return URL for Sinapay notify response
	 *
	 *  @return	  string URL
	 */
	protected function getNotifyURL()
	{
		return Mage::getUrl('sinapay/payment/notify/', array('_secure' => true));
	}

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('sinapay/form_payment', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('sinapay/payment/pay');
    }

    /**
     *  Return Standard Checkout Form Fields for request to Sinapay
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields()
    {
        $session = Mage::getSingleton('checkout/session');
        
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }
		
		
     
	 
		//必填五个字段
		$inputCharset=1;
		$bgUrl= $this->getNotifyURL();
		$version="v2.3";
		$language="1";
		$signType="1";
		
		//买卖双方
		$merchantAcctId=$this->getConfigData('seller_email');  //本参数用来指定接收款项的人民币账号

		//业务参数
		$orderId=$order->getRealOrderId();
		$orderAmount=(sprintf('%.2f', $order->getGrandTotal()))*100;
		$orderTime= date('Ymdhjs',strtotime($order->getCreatedAt()));
		$pid=$this->getConfigData('partner_id');  //商户的memberId
		
		$key=$this->getConfigData('security_code');
		
		$Msg="inputCharset={$inputCharset}&bgUrl={$bgUrl}&version={$version}&language={$language}&signType={$signType}&merchantAcctId={$merchantAcctId}&orderId={$orderId}&orderAmount={$orderAmount}&orderTime={$orderTime}&pid={$pid}&key={$key}";
		
		$signMsg=strtolower(md5($Msg));
		
		


        $parameter = array();
		$parameter['inputCharset']=$inputCharset;
		$parameter['bgUrl']=$bgUrl;
		$parameter['version']=$version;
		$parameter['language']=$language;
		$parameter['signType']=$signType;
		$parameter['merchantAcctId']=$merchantAcctId;
		$parameter['orderId']=$orderId;
		$parameter['orderAmount']=$orderAmount;
		$parameter['orderTime']=$orderTime;
		$parameter['pid']=$pid;
		$parameter['signMsg']=$signMsg;
		
		
		
        return $parameter;
    }
    
	public function sign($prestr) {
		$mysign = md5($prestr);
		return $mysign;
	}
    
	public function para_filter($parameter) {
		$para = array();
		while (list ($key, $val) = each ($parameter)) {
			if($key == "sign" || $key == "sign_type" || $val == "")continue;
			else	$para[$key] = $parameter[$key];

		}
		return $para;
	}
	
	public function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}

	public function charset_encode($input,$_output_charset ,$_input_charset ="GBK" ) {
		$output = "";
		if($_input_charset == $_output_charset || $input ==null) {
			$output = $input;
		} elseif (function_exists("mb_convert_encoding")){
			$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
		} elseif(function_exists("iconv")) {
			$output = iconv($_input_charset,$_output_charset,$input);
		} else die("sorry, you have no libs support for charset change.");
		return $output;
	}
   
	/**
	 * Return authorized languages by Sinapay
	 *
	 * @param	none
	 * @return	array
	 */
	protected function _getAuthorizedLanguages()
	{
		$languages = array();
		
        foreach (Mage::getConfig()->getNode('global/payment/sinapay_payment/languages')->asArray() as $data) 
		{
			$languages[$data['code']] = $data['name'];
		}
		
		return $languages;
	}
	
	/**
	 * Return language code to send to Sinapay
	 *
	 * @param	none
	 * @return	String
	 */
	protected function _getLanguageCode()
	{
		// Store language
		$language = strtoupper(substr(Mage::getStoreConfig('general/locale/code'), 0, 2));

		// Authorized Languages
		$authorized_languages = $this->_getAuthorizedLanguages();

		if (count($authorized_languages) === 1) 
		{
			$codes = array_keys($authorized_languages);
			return $codes[0];
		}
		
		if (array_key_exists($language, $authorized_languages)) 
		{
			return $language;
		}
		
		// By default we use language selected in store admin
		return $this->getConfigData('language');
	}



    /**
     *  Output failure response and stop the script
     *
     *  @param    none
     *  @return	  void
     */
    public function generateErrorResponse()
    {
        die($this->getErrorResponse());
    }

    /**
     *  Return response for Sinapay success payment
     *
     *  @param    none
     *  @return	  string Success response string
     */
    public function getSuccessResponse()
    {
        $response = array(
            'Pragma: no-cache',
            'Content-type : text/plain',
            'Version: 1',
            'OK'
        );
        return implode("\n", $response) . "\n";
    }

    /**
     *  Return response for Sinapay failure payment
     *
     *  @param    none
     *  @return	  string Failure response string
     */
    public function getErrorResponse()
    {
        $response = array(
            'Pragma: no-cache',
            'Content-type : text/plain',
            'Version: 1',
            'Document falsifie'
        );
        return implode("\n", $response) . "\n";
    }

}