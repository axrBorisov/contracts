<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

$this->title = 'Этапы оплаты';
?>

<div class="paystages-index">

    <div class="main-title-stage">
        <table class="title-tb" cellpadding="5">
            <tbody>
            <tr>
                <td><b>Договор:</b></td>
                <td><?= Html::encode($project->n_contract) ?></td>
                <td><b>Контрагент:</b></td>
                <td><?= Html::encode($project->partner->name_partner) ?></td>
            </tr>
            <tr>
                <td><b>Описание проекта:</b></td>
                <td colspan="3"><?= Html::encode($project->project_name) ?></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div>

    <br>
    
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#calendarplan" aria-controls="calendarplan" role="tab" data-toggle="tab">Календарный план</a></li>
      <li role="presentation"><a href="#paymentstage" aria-controls="paymentstage" role="tab" data-toggle="tab">Этапы оплаты</a></li>
      <li role="presentation"><a href="#documentflow" aria-controls="documentflow" role="tab" data-toggle="tab">Документооборот</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="calendarplan">
            <div>
                <p class="btn-cr-calendar">
                    <?php if (Yii::$app->user->can('create')): ?>
                        <?= Html::button('Добавить этап', [
                            'value' => Url::to(['calendar-plan/create', 'id_project' => $project->id]),
                            'class' => 'btn btn-primary btn-sm submodal-button',
                            'id' => 'createButtonCalendar',
                            'data' => [
                                'modal' => 'modalCrCalendar',
                            ]
                        ]) ?>
                    <?php endif; ?>
                </p>

            </div>

            <?= GridView::widget([
                'dataProvider' => $provider_plan,
                'hover' => true,
                'summary' => false,
                'resizableColumns' => false,
                'showPageSummary' => true,
                'rowOptions' => function ($model, $action) {
                    return [$action, 'id' => $model['id']];
                },
                'columns' => [
                    [
                        'attribute' => 'stage_name',
                        'label' => 'Этап',
                        'format' => 'raw',
                        'contentOptions' => [
                            'class' => 'name text-center'
                        ]
                        /*'value' => function ($data, $action, $model) {
                            return Html::a($data['stage_name'], ['update', 'id' => $data['id']], ['class' => 'updateButton']);
                        },*/
                    ],
                    [
                        'attribute' => 'date_begin',
                        'label' => 'Дата начала',
                        'format' => ['date', 'php:d.m.Y'],
                    ],
                    [
                        'attribute' => 'date_end',
                        'label' => 'Дата окончания',
                        'format' => ['date', 'php:d.m.Y'],
                    ],
                    [
                        'attribute' => 'sum',
                        'label' => 'Сумма',
                        'format' => ['decimal', 2],
                        'contentOptions' => [
                            'class' => 'name text-right'
                        ],
                        'pageSummary' => true,
                        'pageSummaryFunc' => GridView::F_SUM,
                    ],
                    [
                        'attribute' => 'plan_status_name',
                        'label' => 'Состояние',
                    ],
                    [
                        'attribute' => 'fact_date',
                        'label' => 'Факт. дата выполнения',
                        'format' => ['date', 'php:d.m.Y'],
                    ],
                    [
                        'attribute' => 'comment',
                        'label' => 'Комментарий',
                        'format' => 'text',
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'visible' => \Yii::$app->user->can('moderator'),
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return ['calendar-plan/' . $action, 'id' => $model['id']];
                        },
                        'header' => false,
                        'headerOptions' => ['width' => '50'],
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($data) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $data, ['class' => 'updateButtonCalendar submodal-button', 'data' => [
                                'modal' => 'modalUpCalendar',
                            ]]);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'onclick' => "ajax_delete('".Url::toRoute($url)."','".Yii::t('yii', 'Are you sure you want to delete this item?')."', this); return false;",
                                ]);
                            },
                        ],
                        'visibleButtons' => [
                            'delete' => \Yii::$app->user->can('delete'),
                            'update' => \Yii::$app->user->can('update'),
                        ]
                    ],
                ],
            ]); ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="paymentstage">
            
            <div>
                <p class="btn-cr">
                    <?php if (Yii::$app->user->can('create')): ?>
                    <?= Html::button('Добавить этап', [
                        'value' => Url::to(['create', 'id_project' => $project->id]),
                        'class' => 'btn btn-primary btn-sm submodal-button',
                        'id' => 'createButton',
                        'data' => [
                            'modal' => 'modalCr',
                        ]
                    ]) ?>
                    <?php endif; ?>
                </p>
            </div>

            <?= GridView::widget([
                'dataProvider' => $provider,
                'hover' => true,
                'summary' => false,
                'resizableColumns' => false,
                'showPageSummary' => true,
                'rowOptions' => function ($model, $action) {
                    return [$action, 'id' => $model['id']];
                },
                'columns' => [
                    [
                        'attribute' => 'stage_name',
                        'label' => 'Этап',
                        'format' => 'raw',
                        'contentOptions' => [
                            'class' => 'name text-center'
                        ]
                        /*'value' => function ($data, $action, $model) {
                            return Html::a($data['stage_name'], ['update', 'id' => $data['id']], ['class' => 'updateButton']);
                        },*/
                    ],
                    [
                        'attribute' => 'type_name',
                        'label' => 'Тип платежа',
                    ],
                    [
                        'attribute' => 'sum',
                        'label' => 'Сумма',
                        'format' => ['decimal', 2],
                        'contentOptions' => [
                            'class' => 'name text-right'
                        ],
                        'pageSummary' => true,
                        'pageSummaryFunc' => GridView::F_SUM,
                    ],
                    [
                        'attribute' => 'date_plan',
                        'label' => 'План. дата',
                        'format' => ['date', 'php:d.m.Y'],
                    ],
                    [
                        'attribute' => 'name_status',
                        'label' => 'Состояние',
                    ],
                    [
                        'attribute' => 'date_fact',
                        'label' => 'Факт. дата',
                        'format' => ['date', 'php:d.m.Y'],
                    ],
                    [
                        'attribute' => 'comment',
                        'label' => 'Комментарий',
                        'format' => 'text',
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'visible' => \Yii::$app->user->can('moderator'),
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model['id']];
                        },
                        'header' => false,
                        'headerOptions' => ['width' => '50'],
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($data) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $data, ['class' => 'updateButton submodal-button', 'data' => [
                                'modal' => 'modalUp',
                            ]]);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'onclick' => "ajax_delete('".Url::toRoute($url)."','".Yii::t('yii', 'Are you sure you want to delete this item?')."', this); return false;",
                                ]);
                            },
                        ],
                        'visibleButtons' => [
                            'delete' => \Yii::$app->user->can('delete'),
                            'update' => \Yii::$app->user->can('update'),
                        ]
                    ],
                ],
            ]); ?>

            
        </div>
        <div role="tabpanel" class="tab-pane" id="documentflow">
            
            <div>
                <p class="btn-cr-document">
                    <?php if (Yii::$app->user->can('create')): ?>
                        <?= Html::button('Добавить документ', [
                            'value' => Url::to(['document/create', 'id_project' => $project->id]),
                            'class' => 'btn btn-primary btn-sm submodal-button',
                            'id' => 'createButtonDocument',
                            'data' => [
                                'modal' => 'modalCrDocument',
                            ]
                        ]) ?>
                    <?php endif; ?>
                </p>
            </div>
            
            <?= GridView::widget([
                'dataProvider' => $documentsDataProvider,
                'summary' => false,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'rowOptions' => function ($model, $action) {
                    return [$action, 'id' => $model['id']];
                },
                'columns' => [
                    [
                        'attribute' => 'document_type_id',
                        'format' => 'raw',
                        'contentOptions' => [
                            'class' => 'name text-left'
                        ],
                        'value' => function ($data, $action, $model) {
                           if($data->documentType != null){
                               if(strlen($data->document_url) > 0){
                                   return \yii\bootstrap\Html::a($data->documentType->doctype, $data->document_url, ['target' => '_blank']);
                               }
                               return $data->documentType->doctype;
                           }
                           return '(нет данных)';
                        },
                    ],
                    [
                        'attribute' => 'document_date',
                        'format' => ['date', 'php:d.m.Y'],
                    ],
                    [
                        'attribute' => 'document_status_id',
                        'format' => 'raw',
                        'contentOptions' => [
                            'class' => 'name text-left'
                        ],
                        'value' => function ($data, $action, $model) {
                            if($data->documentStatus != null){
                               if(sizeof($data->documentEvents) > 0){
                                   return $data->getStatusWithHistory();
                               }
                               return $data->documentStatus->status;
                           }
                           return '(нет данных)';
                        },
                    ],
                    [
                        'attribute' => 'comment',
                        'label' => 'Комментарий',
                        'format' => 'text',
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'visible' => \Yii::$app->user->can('moderator'),
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return ['document/' . $action, 'id' => $model['id']];
                        },
                        'header' => false,
                        'headerOptions' => ['width' => '50'],
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($data) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $data, ['class' => 'updateButtonDocument submodal-button', 'data' => [
                                'modal' => 'modalUpDocument',
                            ]]);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'onclick' => "ajax_delete('".Url::toRoute($url)."','".Yii::t('yii', 'Are you sure you want to delete this item?')."', this); return false;",
                                ]);
                            },
                        ],
                        'visibleButtons' => [
                            'delete' => \Yii::$app->user->can('delete'),
                            'update' => \Yii::$app->user->can('update'),
                        ]
                    ],
                ],
            ]); ?>
            
            <?php if($documentHistoryDataProvider->count > 0): ?>
            <br>
            
            <?= GridView::widget([
                'dataProvider' => $documentHistoryDataProvider,
                'summary' => false,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'columns' => [
                    [
                        'header' => 'Документ',
                        'format' => 'raw',
                        'contentOptions' => [
                            'class' => 'name text-left'
                        ],
                        'value' => function ($data, $action, $model) {
                           if($data->document != null && $data->document->documentType != null){
                                return $data->document->documentType->doctype;
                           }
                           return '(нет данных)';
                        },
                    ],
                    [
                        'header' => 'Старый статус',
                        'attribute' => 'old_document_status_id',
                        'format' => 'raw',
                        'contentOptions' => [
                            'class' => 'name text-left'
                        ],
                        'value' => function ($data, $action, $model) {
                            if($data->oldDocumentStatus != null){
                               return $data->oldDocumentStatus->status;
                           }
                           return '(нет данных)';
                        },
                    ],
                    [
                        'header' => 'Новый статус',
                        'attribute' => 'new_document_status_id',
                        'format' => 'raw',
                        'contentOptions' => [
                            'class' => 'name text-left'
                        ],
                        'value' => function ($data, $action, $model) {
                            if($data->newDocumentStatus != null){
                               return $data->newDocumentStatus->status;
                           }
                           return '(нет данных)';
                        },
                    ],
                    [
                        'header' => 'Изменил',
                        'attribute' => 'user_id',
                        'format' => 'raw',
                        'contentOptions' => [
                            'class' => 'name text-left',
                            'style' => 'width: 120px;',
                        ],
                        'value' => function ($data, $action, $model) {
                            if($data->user != null){
                                if($data->user->staff == null){
                                    return $data->user->username;
                                }
                                else{
                                    return $data->user->staff->staff_name;
                                }
                           }
                           return '(нет данных)';
                        },
                    ],
                    [
                        'header' => 'Время изменения',
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:d.m.Y H:i'],
                        'contentOptions' => [
                            'style' => 'width: 150px;',
                        ],
                    ],
                ],
            ]); ?>
            
            <?php endif; ?>
            
        </div>
    </div>

</div>

</div>
