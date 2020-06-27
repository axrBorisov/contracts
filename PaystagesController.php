<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Project;
use yii\data\SqlDataProvider;
use frontend\models\Paystages;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\Document;
use frontend\models\DocumentEvent;
use yii\data\ActiveDataProvider;


class PaystagesController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'error'],
                        'roles' => ['user'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['moderator'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($id)
    {
        $project = Project::findOne($id);

        $provider = new SqlDataProvider([
            'sql' =>
                'SELECT `project`.`project_name`, `stage`.`stage_name`, `type_payments`.`type_name`, `payment_stages`.`id`, `payment_stages`.`date_plan`, `payment_stages`.`sum`, `status`.`name_status`,  `payment_stages`.`date_fact`, `payment_stages`.`comment` 
                 FROM `payment_stages`
                 LEFT JOIN `project` ON `payment_stages`.`id_project` = `project`.`id`
                 LEFT JOIN `type_payments` ON `payment_stages`.`id_type_payments` = `type_payments`.`id`
                 LEFT JOIN `status` ON `payment_stages`.`id_status` = `status`.`id`
                 LEFT JOIN `stage` ON `payment_stages`.`id_stage` = `stage`.`id`
                 WHERE project.id = :id',
            'params' => [
                ':id' => $id,
            ],
            'totalCount' => 50,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'attributes' => [
                    'title',
                    'view_count',
                    'created_at',
                ],
            ],
        ]);

        $provider_plan = new SqlDataProvider([
            'sql' =>
                'SELECT `calendar_plan`.`id`, `project`.`project_name`, `stage`.`stage_name`, `calendar_plan`.`date_begin`, `calendar_plan`.`date_end`, `calendar_plan`.`sum`, `plan_status`.`plan_status_name`,  `calendar_plan`.`fact_date`, `calendar_plan`.`comment` 
                 FROM `calendar_plan`
                 LEFT JOIN `project` ON `calendar_plan`.`id_project` = `project`.`id`
                 LEFT JOIN `plan_status` ON `calendar_plan`.`id_plan_status` = `plan_status`.`id`
                 LEFT JOIN `stage` ON `calendar_plan`.`id_stage` = `stage`.`id`
                 WHERE project.id = :id',
            'params' => [
                ':id' => $id,
            ],
            'totalCount' => 50,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'attributes' => [
                ],
            ],
        ]);


        $models = $provider->getModels();

        $documentsDataProvider = new ActiveDataProvider([
            'query' => Document::find()->where(['project_id' => $project->id]),
            'sort' => false,
            'pagination' => [
                'pageSize' => 0,
            ],
        ]);
        
        $documentHistoryDataProvider = new ActiveDataProvider([
           'query' => DocumentEvent::find()->joinWith('document')->where(['document.project_id' => $project->id]),
           'sort' =>  ['defaultOrder' => ['created_at'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => 0,
            ],
        ]);

        
        
        return $this->renderAjax('index',
            ['provider' => $provider,
                'provider_plan' => $provider_plan,
                'project' => $project,
                'documentsDataProvider' => $documentsDataProvider,
                'documentHistoryDataProvider' => $documentHistoryDataProvider,
            ]);
    }

    public function actionCreate($id_project)
    {
        $model = new Paystages();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            echo "<script>$('#modalCr').modal('hide');</script>";
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
                'id_project' => $id_project,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            echo "<script>$('#modalUp').modal('hide');</script>";
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
                'id_project' => $model->id_project
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = Paystages::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
    }
}