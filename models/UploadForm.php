<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;
	public $idcurso;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf', 'checkExtensionByMimeType'=>false, 
				'maxSize' => 2048000, 'tooBig' => 'El límite es 2MB'],
			[['idcurso'], 'integer'],
        ];
    }
    
	public function attributeLabels()
    {
        return [
            'imageFile' => 'Archivo',
        ];
    }

    public function upload()
    {
		#$url = @app.'uploads/';
		$url = 'uploads/';
		if ($this->validate()) {
			#echo var_dump($this->imageFile->baseName, $this->idcurso); exit;
            #$this->imageFile->saveAs($url . $this->imageFile->baseName . '.' . $this->imageFile->extension);
			$this->imageFile->saveAs($url . $this->idcurso . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }
}
