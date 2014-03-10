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
 * @category	CosmoCommerce
 * @package 	CosmoCommerce_Sinapay
 * @copyright	Copyright (c) 2009-2013 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @contact :
 * T: +86-021-66346672
 * L: Shanghai,China
 * M:sales@cosmocommerce.com
 */
class CosmoCommerce_Sinapay_Model_Source_Banks
{
    public function toOptionArray()
    {
        $banks=array(
        "ABC"=>"中国农业银行",
        "BCCB"=>"北京银行",
        "BOC"=>"中国银行",
        "BOS"=>"上海银行",
        "CCB"=>"中国建设银行",
        "CEB"=>"中国光大银行",
        "CIB"=>"兴业银行",
        "CITIC"=>"中信银行",
        "CMBC"=>"中国民生银行",
        "COMM"=>"交通银行",
        "GDB"=>"广发银行",
        "HXB"=>"华夏银行",
        "ICBC"=>"中国工商银行",
        "PSBC"=>"中国邮政储蓄银行",
        "CMB"=>"招商银行",
        "SPDB"=>"浦东发展银行",
        "SZPAB"=>"平安银行",
        "UPOP"=>"银联在线支付",
        );
        $banks_options=array();
        foreach($banks as $code=>$bank){
            $banks_options[]=array('value' => $code, 'label' => $bank);
        
        }
        return $banks_options;
    }
}





