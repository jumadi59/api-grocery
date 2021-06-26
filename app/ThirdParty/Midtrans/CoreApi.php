<?php

namespace Midtrans;

/**
 * Provide charge and capture functions for Core API
 */
class CoreApi
{
    /**
     * Create transaction.
     *
     * @param mixed[] $params Transaction options
     */
    public static function charge($params)
    {
        $payloads = array(
            'payment_type' => 'credit_card'
        );

        if (isset($params['item_details'])) {
            $gross_amount = 0;
            foreach ($params['item_details'] as $item) {
                $gross_amount += $item['quantity'] * $item['price'];
            }
            $payloads['transaction_details']['gross_amount'] = $gross_amount;
        }

        $payloads = array_replace_recursive($payloads, $params);

        if (Config::$isSanitized) {
            Sanitizer::jsonRequest($payloads);
        }

        if (Config::$appendNotifUrl)
            Config::$curlOptions[CURLOPT_HTTPHEADER][] = 'X-Append-Notification: ' . Config::$appendNotifUrl;

        if (Config::$overrideNotifUrl)
            Config::$curlOptions[CURLOPT_HTTPHEADER][] = 'X-Override-Notification: ' . Config::$overrideNotifUrl;

        $result = ApiRequestor::post(
            Config::getBaseUrl() . '/charge',
            Config::$serverKey,
            $payloads
        );

        return $result;
    }

    /**
     * Capture pre-authorized transaction
     *
     * @param string $param Order ID or transaction ID, that you want to capture
     */
    public static function capture($param)
    {
        $payloads = array(
        'transaction_id' => $param,
        );

        $result = ApiRequestor::post(
            Config::getBaseUrl() . '/capture',
            Config::$serverKey,
            $payloads
        );

        return $result;
    }
    public static function vtweb_charge($payloads)
    {	

    	$result = ApiRequestor::post(
        Config::getBaseUrl() . '/charge',
        Config::$serverKey,
        $payloads);

        return $result->redirect_url;
    }

    public static function vtdirect_charge($payloads)
    {	

    	$result = ApiRequestor::post(
        Config::getBaseUrl() . '/charge',
        Config::$serverKey,
        $payloads);

        return $result;
    }
	
    /**
   	* Retrieve transaction status
   	* @param string $id Order ID or transaction ID
    * @return mixed[]
    */
	public static function status($id)
 	{
    	return ApiRequestor::get(
        	Config::getBaseUrl() . '/' . $id . '/status',
        	Config::$serverKey,
        	false);
  	}

  	/**
   	* Appove challenge transaction
   	* @param string $id Order ID or transaction ID
   	* @return string
   	*/
  	public static function approve($id)
  	{
    	return ApiRequestor::post(
        	Config::getBaseUrl() . '/' . $id . '/approve',
        	Config::$serverKey,
        	false)->status_code;
  	}

  	/**
   	* Cancel transaction before it's setteled
   	* @param string $id Order ID or transaction ID
   	* @return string
   	*/
  	public static function cancel($id)
  	{
    	return ApiRequestor::post(
        	Config::getBaseUrl() . '/' . $id . '/cancel',
        	Config::$serverKey,
        	false)->status_code;
  	}

   /**
    * Expire transaction before it's setteled
    * @param string $id Order ID or transaction ID
    * @return mixed[]
    */
  	public static function expire($id)
  	{
    	return ApiRequestor::post(
        	Config::getBaseUrl() . '/' . $id . '/expire',
        	Config::$serverKey,
        	false);
  	}
}
