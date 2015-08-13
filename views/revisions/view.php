<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use kartik\icons\Icon;

/**
 * @var yii\web\View $this
 * @var nitm\module\models\Revisions $model
 */

$this->title = "Revision for ".$model->parent_type." by ".$model->author()->username;
$this->params['breadcrumbs'][] = ['label' => 'Revisions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="revisions-view" id="revisions-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">
        <?= Html::a(Icon::show('reply'), ['restore', 'id' => $model->id], [
			'class' => 'btn btn-primary',
			'role' => 'metaAction'
		]) ?>
        <?= Html::a(Icon::show('trash-o'), [
			'disable', 
			'id' => $model->getId(), 
		], [
            'class' => 'btn btn-danger',
			'role' => 'metaAction disableAction',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
			'allModels' => [$model],
			'pagination' => false,
		]),
        'columns' => [
            'created_at:datetime',
            'parent_type',
            'parent_id',
        ],
		'afterRow' => function ($model, $key, $index, $grid) {
			switch($model->getAttribute('data') != null)
			{
				case true:
				$data = json_decode($model->getAttribute('data'), true);
				$data['attribute'] = isset($data['attribute']) ? $data['attribute'] : key($data);
				$attributes = explode(',', $data['attribute']);
				switch(is_array($data))
				{
					case true:
					$ret_val = '';
					foreach($attributes as $attribute)
					{
						$ret_val .= Html::tag('div',
							Html::tag('h3', ucfirst($attribute))
							.Html::tag('div', urldecode($data[$attribute])),
							[
								'class' => 'well'
							]
						);
					}
					break;
				}
				break;
				
				default:
				$ret_val = $model->getAttribute('data');
				break;
			}
			return Html::tag('tr', 
				Html::tag(
					'td', 
					$ret_val, 
					[
						'colspan' => 6, 
						'rowspan' => 1
					]
				)
			);
		}
    ]) ?>

</div>
<?php if(\Yii::$app->request->isAjax ): ?>
<script type="text/javascript">
$nitm.onModuleLoad('revisions', function (module) {
	module.initDefaults("#revisions-view", 'revisions');
});
</script>
<?php endif; ?>
