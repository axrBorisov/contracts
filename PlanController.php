<?php

namespace frontend\controllers;

use frontend\models\Plan;
use Yii;
use yii\data\SqlDataProvider;
use yii\web\Controller;
use frontend\models\Filter;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;

/**
 * VarController implements the CRUD actions for Plan model.
 */
class PlanController extends Controller
{
    /**
     * @inheritdoc
     */

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

    public function actionIndex()
    {
        $filter = new Filter();
        $filter->attributes = Yii::$app->request->post('Filter');

        $query = Plan::find();
        $count = $query->count();

        $provider = new SqlDataProvider([
            'sql' =>
                'SELECT project.project_name, project.id, partner.name_partner, payment_stages.id payment_stage_id, id_stage, stage.stage_name, type_payments.type_name, payment_stages.sum, payment_stages.date_plan, staff.staff_name, project.n_contract
                 FROM payment_stages
                 LEFT JOIN project ON payment_stages.id_project = project.id
                 LEFT JOIN type_payments ON payment_stages.id_type_payments = type_payments.id
                 LEFT JOIN partner ON project.id_partner = partner.id
                 LEFT JOIN stage ON payment_stages.id_stage = stage.id
                 LEFT JOIN staff ON project.id_staff = staff.id
                 LEFT JOIN user ON user.staff_id = staff.id
                 WHERE (payment_stages.id_status = 2) 
                 AND (id_implementer = :id_implementer OR :id_implementer = -1)
                 AND (id_partner = :id_partner OR :id_partner = -1)
                 AND (id_staff = :id_staff OR :id_staff = -1)
                 AND payment_stages.date_plan BETWEEN :from_date AND :to_date
                 AND (user.id = :id OR :role_id in (2,3))',
            'params' => [
                'id_implementer' => empty($filter->id_implementer) ? -1 : $filter->id_implementer,
                'id_partner' => empty($filter->id_partner) ? -1 : $filter->id_partner,
                'id_staff' => empty($filter->id_staff) ? -1 : $filter->id_staff,
                'from_date' => empty($filter->from_date) ? '1999-01-01' : $filter->from_date,
                'to_date' => empty($filter->to_date) ? '2099-01-01' : $filter->to_date,
                ':id' => Yii::$app->user->id,
                ':role_id' => Yii::$app->user->identity->id_role,
            ],
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'attributes' => [
                    'name_implementer',
                    'project_name',
                    'name_partner',
                    'sum',
                    'staff_name',
                    'stage_name',
                    'date_plan',
                    'n_contract',
                    'type_name',
                ],
            ],
        ]);

        $models = $provider->getModels();

        return $this->render('index',
            ['provider' => $provider,
                'model' => $filter]);
    }
}
