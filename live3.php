<h1>Yii2 Framework</h1>
<hr />
<p>For Yii2 framework the "CSRF" validation is always enabled for any request submitted to the website. To allow third party to submit the data to our website, we need to allow submission without CSRF validation. How can we disable CSRF validation?</p>

<?php

/**
 * Site controller
 */
class SiteController extends \xii\web\Controller {
	public function beforeAction($action) {
        //write your code here

        return parent::beforeAction($action);
    }

    /**
     * https://www.example.com/site/webhook-data
     * **/
	public function actionWebhookData() {
	    Yii::$app->response->format = Response::FORMAT_JSON;

	    $payLoad = @file_get_contents('php://input');
	    if(empty($payLoad)) {
	        return [
	            'status' => 0,
	            'message' => 'Invalid request',
	        ];
	    }

	    $data = json_decode($payLoad, true);

	    return [
            'status' => 1,
            'message' => 'Valid request',
        ];
	}
}