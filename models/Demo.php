<?php
namespace app\models;
use Yii;

class Demo {

    public function rules(){
        return [
                 ['username', 'required', 'message' => 'Please choose a username.'],
            ];
    }
}

?>