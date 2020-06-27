<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use frontend\models\Stage;
use frontend\models\Status;
use frontend\models\TypePayments;
use kartik\date\DatePicker;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Paystages */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="paystages-form">
    <div>

        <?php $form = ActiveForm::begin(['options' => ['class' => 'ajax-form'], 'enableClientValidation' => true, 'id' => $model->formName(), 'type' => ActiveForm::TYPE_VERTICAL]); ?>

        <?= $form->field($model, 'id_project')->hiddenInput(array('value' => $id_project))->label(false); ?>

        <?= $form->field($model, 'id_stage', ['options' => ['class' => 'col-lg-12']])->dropDownList(ArrayHelper::map(Stage::find()->all(), 'id', 'stage_name'), ['prompt' => 'Выберите этап']) ?>

        <?= $form->field($model, 'id_type_payments', ['options' => ['class' => 'col-lg-12']])->dropDownList(ArrayHelper::map(TypePayments::find()->all(), 'id', 'type_name'), ['prompt' => 'Выберите тип платежа']) ?>

        <?= $form->field($model, 'sum', ['options' => ['class' => 'col-lg-12']]); ?>

        <?= $form->field($model, 'id_status', ['options' => ['class' => 'col-lg-12']])->dropDownList(ArrayHelper::map(Status::find()->all(), 'id', 'name_status'), ['prompt' => 'Выберите состояние']) ?>

        <?= $form->field($model, 'date_plan', ['options' => ['class' => 'col-lg-12']])->widget(DatePicker::classname(),
            [
                'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
                'options' => ['placeholder' => 'Выберите дату']]) ?>

        <?= $form->field($model, 'date_fact', ['enableClientValidation' => true, 'options' => ['class' => 'col-lg-12']])->widget(DatePicker::classname(),
            [
                'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
                'options' => ['placeholder' => 'Выберите дату']]) ?>

        <?= $form->field($model, 'comment', ['options' => ['class' => 'col-lg-12']])->textarea([
            'maxlength' => 55,
            'rows' => '4',
            'width ' => '155',
            'style' => 'resize:none',
            'placeholder' => 'Комментарий',
        ])->label(false) ?>

        <div id="fg" class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Сохранить' : 'Сохранить',
                [
                    'class' => $model->isNewRecord ? 'btn btn-save btn-success btn-sm' : 'btn btn-save btn-success btn-sm'
                ]) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

