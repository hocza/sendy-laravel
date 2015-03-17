<?php namespace Hocza\Sendy;

use Illuminate\Support\Facades\Config;

class Sendy {

	protected $installation_url;
	protected $api_key;
	protected $list_id;

	public function __construct()
	{
		//error checking
		$list_id = Config::get('interspire::list_id');
		$installation_url = Config::get('interspire::installation_url');
		$api_key = Config::get('interspire::api_key');
		
		if (!isset($list_id)) {
			throw new \Exception("Required config parameter [list_id] is not set", 1);
		}
		
		if (!isset($installation_url)) {
			throw new \Exception("Required config parameter [installation_url] is not set", 1);
		}
		
		if (!isset($api_key)) {
			throw new \Exception("Required config parameter [api_key] is not set", 1);
		}
		$this->list_id = $list_id;
		$this->installation_url = $installation_url;
		$this->api_key = $api_key;
	}

	public function subscribe(array $values)
    {
		$type = 'subscribe';
		//Send the subscribe
		$result = strval($this->buildAndSend($type, $values));
		//Handle results
		switch ($result) {
			case '1':
				return array(
					'status' => true,
					'message' => 'Subscribed'
					);
				break;
			case 'Already subscribed.':
				return array(
					'status' => true,
					'message' => 'Already subscribed.'
					);
				break;
			default:
				return array(
					'status' => false,
					'message' => $result
					);
				break;
		}
    }
    public function unsubscribe($email)
    {
		$type = 'unsubscribe';
		//Send the unsubscribe
		$result = strval($this->buildAndSend($type, array('email' => $email)));
		//Handle results
		switch ($result) {
			case '1':
				return array(
					'status' => true,
					'message' => 'Unsubscribed'
					);
				break;
			
			default:
				return array(
					'status' => false,
					'message' => $result
					);
				break;
		}
    }

	private function buildAndSend($type, array $values)
	{
		//error checking
		if (!isset($type)) {
			throw new \Exception("Required config parameter [type] is not set", 1);
		}
		if (!isset($values)) {
			throw new \Exception("Required config parameter [values] is not set", 1);
		}
		//
		$return_options = array(
			'list' => $this->list_id,
			'boolean' => 'true'
		);
		//Merge the passed in values with the options for return
		$content = array_merge($values, $return_options);
		//build a query using the $content
		$post_data = http_build_query($content);
		$ch = curl_init($this->installation_url .'/'. $type);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

}