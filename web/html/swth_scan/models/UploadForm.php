<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $image;
    public $denom;

    public function rules()
    {
        return [
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->image->saveAs(Yii::getAlias('@app').'/web/img/tokens/' . $this->denom . '.' . $this->image->extension);
            return true;
        } else {
            return false;
        }
    }
}