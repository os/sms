<?php
	class ClickatellSMSSender {
		function __construct($username, $password, $apid) {
			$this->username = $username;
			$this->password = $password;
			$this->apid = $apid;
			$this->messageId = null;
			$this->errorId = null;
		}
		
		public function send($to, $message, $msgid = null) {
			$params = array(
				'user'     => $this->username,
				'password' => $this->password,
				'api_id'   => $this->apid,
				'to'       => $to,
				'text'     => $this->slugified($message),
				'callback' => 3
			);
			
			if ($msgid != null)
				$params['climsgid'] = $msgid;
			
			$request = new HttpRequest("http://api.clickatell.com/http/sendmsg", HttpRequest::METH_GET);
			$request->addQueryData($params);
			
			try {
			    $request->send();
			    if ($request->getResponseCode() == 200)
			        return parseResponse($request->getResponseBody());
			} catch (HttpException $ex) {
			    echo $ex;
			}
			
			return false;
		}
		
		private function parseResponse($response) {
			list($responseCode, $responseDetails) = split(': ', $response);
		
			if ($responseCode == "ID") {
				$this->messageId = $responseDetails;
				return true;
			} else if ($responseCode == "ERR") {
				list($errorCode, $errorMessage) = split(', ', $responseDetails, 2);
				$this->errorId = $errorMessage;
				return false;
			} else {
				// TODO: catch this!
			}
		}
		
		private function slugified($message) {
			return str_replace(" ", "+", $message);
		}
	}
?>