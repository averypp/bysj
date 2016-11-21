<?php 
namespace app\models;
use Yii;


/*
* add by echo 2016-06-02
* 
*/

Class PublicProduct extends Model{

	public static function tableName(){
        return 'sea_goods_info';
    }

	public function select($param){
		$PublicProduct = new PublicProduct();
	}

}



?>