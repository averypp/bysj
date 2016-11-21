


<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'tag-form',
    'enableAjaxValidation'=>false,
)); ?>
    <div class="row">
        <?php echo $form->labelEx($model,'tagname'); ?>
        <?php echo $form->textField($model,'tagname',array('size'=>20,'maxlength'=>32)); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'tagtype'); ?>
        <?php echo $form->radioButtonList($model,'tagtype',array(1=>"普通TAG"，2=>"系统默认TAG"),array('separator'=>'','labelOptions'=>array('class'=>'tagtypelabel'))); ?>
    </div>
    <?php echo $form->errorSummary($model); ?>
    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? '添加' : '修改'); ?>
    </div>
<?php $this->endWidget(); ?>



