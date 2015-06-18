<?php 
namespace Users\Models;

class Avatars extends \Dsc\Mongo\Collections\Assets 
{
		

	public static function createFromUpload( array $file_upload, $options=array() )
	{
		if (!isset($file_upload['error']) || is_array($file_upload['error']))
		{
			throw new \Exception('Invalid Upload');
		}
	
		switch ($file_upload['error'])
		{
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new \Exception('No file sent.');
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new \Exception('Exceeded filesize limit.');
			default:
				throw new \Exception('Unknown errors.');
		}
	
		if (empty($file_upload['tmp_name']) || empty($file_upload['name']))
		{
			throw new \Exception('Invalid Upload Properties');
		}
	
		if (empty($file_upload['size']))
		{
			throw new \Exception('Invalid Upload Size');
		}
	
		$app = \Base::instance();
		$options = $options + array('width'=>460, 'height'=>308);
	
		// Do the upload
		$model = new static;
		$grid = $model->getDb()->getGridFS( $model->collectionNameGridFS() );
		$file_path = $model->inputFilter()->clean($file_upload['tmp_name']);
		$name = $model->inputFilter()->clean($file_upload['name']);
		//$buffer = file_get_contents($file_upload['tmp_name']);
		
		
		//TODO MOVE THIS TO A LISTENER
		$image = new \Dsc\Image($file_upload['tmp_name']);
		
		
		
		$cropped = $image->cropResize(250,250);
		$cropped = $cropped->crop(250,250);
		$buffer = $cropped->toBuffer();
		$options['name'] = $file_upload['name'];
		return static::createFromBuffer($buffer,$options);
	}
	
	
	
	
}