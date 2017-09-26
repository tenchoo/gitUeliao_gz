<?php
/**
 * 个推接口参数设置
 * User: yagas
 * Date: 2016/5/4
 * Time: 16:56
 */
class GetuiController extends Controller {

    public function actionIndex() {
        if(Yii::app()->request->getIsPostRequest()) {
            return $this->saveConfig();
        }

        $conf = (array)json_decode( tbConfig::model()->get('getui_config') );
        return $this->render('index', ['data'=>$conf]);
    }

    private function saveConfig() {
        $data = Yii::app()->request->getPost('data');
        if(!is_null($data)) {
            $conf = tbConfig::model()->find("`key`=:key", [':key'=>'getui_config']);

            if(!is_null($conf)) {
                $conf->value = json_encode($data);

                if($conf->save()) {
                    Yii::app()->session->add('alertSuccess',1);
                }
                else {
                    $this->setError([Yii::t('base','Faild to save config')]);
                    goto redirect;
                }
            }
            else {
                $this->setError([Yii::t('base','Not found config:{attribute}',['{attribute}'=>'getui_config'])]);
                goto redirect;
            }
        }

        redirect:
        $this->redirect( $this->createUrl('index') );
    }
}