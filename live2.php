<h1>PHP</h1>
<hr />
<p>Please mention what's wrong and correct the code.</p>

<?php

//1. What's wrong with the below lines of code?
$username = $_GET['username'];
$sql = "SELECT * FROM user WHERE username = '$username'";

//2. What's wrong with the below lines of code?
$arr = [
	'a' => 'ABC',
	'b' => 'DEF',
	'c' => 'GHI',
	'd' => 'JKL',
	'e' => 'MNO',
];

$k = 'ab';
if($arr[$k]) {
	var_dump($arr[$k])
}

//3. What's wrong with the below lines of code?
$arr = [
	'a' => 'ABC',
	'b' => 'DEF',
	'c' => 'GHI',
	'd' => 'JKL',
	'e' => 'MNO',
];

$valid = false;
foreach($arr as $key => $val) {
	if($key == 'b' && $val = 'DEF') {
		$valid = true;
	}
}

if($valid) {
	echo 'VALID';
}else {
	echo 'INVALID';
}

//4. What's wrong with the below lines of code?

$i = 10;
$count = 0;
while ($i < 10) {
	$count++;
}

echo $count;

//5. What's wrong with the below lines of code (Yii2 framework)?
class ServiceController extends \xii\web\Controller {
	//url service/test-result
	public function actiontestResult() {
		return $this->render('test-result');
	}

	//url service/convert-page
	public function ConvertPage() {
		return $this->render('convert-page');
	}
}