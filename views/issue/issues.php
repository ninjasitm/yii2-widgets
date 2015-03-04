<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use kartik\icons\Icon;
use yii\bootstrap\Modal;
use nitm\models\Issues;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\Issues $searchModel
 */

$uniqid = uniqid();
Pjax::begin([
	'enablePushState' => false,
	'linkSelector' => "a[data-pjax], [data-pjax] a",
	'formSelector' => "[data-pjax]",
	'options' => [
		'id' => 'issues-list'
	]
]);
echo $this->render('_search', [
	'model' => $searchModel, 
	'enableComments' => $enableComments,
	'parentType' => $parentType,
	'parentId' => $parentId
])."<br><br>";
echo Html::tag('div', '', ['id' => 'issues-alerts-message']);
echo ListView::widget([
	'options' => [
		'id' => 'issues-'.$filterType.'-list'.$uniqid,
		'style' => 'color:black;'
	],
	'dataProvider' => $dataProvider,
	'itemOptions' => ['class' => 'item'],
	'itemView' => function ($model, $key, $index, $widget) use($options){
		$viewOptions = array_merge(['model' => $model], $options);
		return $widget->render('@nitm/widgets/views/issue/view', $viewOptions);
	},
	/*'pager' => [
		'class' => \kop\y2sp\ScrollPager::className(),
		'container' => '#issues-ias-container',
		'item' => "tr"
	]*/
	'pager' => [
		'linkOptions' => [
			'data-pjax' => 1
		],
	]

]);
?>

<br>
<?php Pjax::end(); ?>
