<?php

namespace frontend\controllers;

use frontend\models\Filter;
use Yii;
use yii\data\SqlDataProvider;
use frontend\models\Project;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class ProjectController extends \yii\web\Controller
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

    /**
     * @return string
     */
    public function actionIndex()
    {
        $filter = new Filter();
        $filter->attributes = Yii::$app->request->post('Filter');

        $query = Project::find();
        $count = $query->count();

        $provider = new SqlDataProvider([
            'sql' =>
                'SELECT `project`.`id`, `implementer`.`name_implementer`, `project`.`project_name`, `partner`.`name_partner`, `project`.`sum`, `staff`.`staff_name`, `project`.`comment`, `project`.`n_contract`
                 FROM `project`
                 JOIN `partner` ON `project`.`id_partner` = `partner`.`id`
                 JOIN `implementer` ON `project`.`id_implementer` = `implementer`.`id`
                 JOIN `staff` ON `project`.`id_staff` = `staff`.`id`
                 LEFT JOIN `user` ON `user`.staff_id = `staff`.`id` 
                 WHERE (id_implementer = :id_implementer OR :id_implementer = -1)
                 AND (id_partner = :id_partner OR :id_partner = -1) 
                 AND (id_staff = :id_staff OR :id_staff = -1)
                 AND (user.id = :id OR :role_id IN (2,3))',
            'params' => [
                'id_implementer' => empty($filter->id_implementer) ? -1 : $filter->id_implementer,
                'id_partner' => empty($filter->id_partner) ? -1 : $filter->id_partner,
                'id_staff' => empty($filter->id_staff) ? -1 : $filter->id_staff,
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
                    'n_contract',
                ],
            ],
        ]);


        $models = $provider->getModels();

        return $this->render('index',
            [
                'provider' => $provider,
                'model' => $filter,
            ]);
    }

    public function actionCreate()
    {
        $model = new Project();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Project model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionFilter()
    {
        $model = new Filter();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            return $this->render('entry-confirm', ['model' => $model]);
        } else {
            return $this->render('entry', ['model' => $model]);
        }
    }
}





