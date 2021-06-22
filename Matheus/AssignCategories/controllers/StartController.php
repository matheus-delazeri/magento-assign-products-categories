<?php

class Matheus_AssignCategories_StartController extends Mage_Adminhtml_Controller_Action{
	public function indexAction(){
              	  /** Set default timezone */
                  date_default_timezone_set('America/Bahia');
		  /** Import type */
		  $this->append = $this->getRequest()->getPost('import_type');
                  /** Set file directory */
                  $this->tmpDir = __DIR__.'/../temp/categories.csv';
                  $sheetName = basename($_FILES['file_to_upload']['name']);
                  $excelFileType = strtolower(pathinfo($sheetName,PATHINFO_EXTENSION));
		  /** Material Icons */
                  echo "<link href='https://fonts.googleapis.com/icon?family=Material+Icons' rel='stylesheet'>";
		  echo "<pre>";
                  $this->error_icon = "<i class='material-icons' style='font-size:22px;color:red;vertical-align: bottom;'>error_outline</i>";
                  $this->done_icon = "<i class='material-icons' style='font-size:22px;color:green;vertical-align: bottom;'>done</i>";
                  $this->loading_icon = "<i class='material-icons' style='font-size:22px;vertical-align: bottom;'>schedule</i>";
                  /** Process start */
                  if($excelFileType!='csv'){
                          echo "<p>".$this->error_icon."<b> ".date('H:i:s')." </b>Error: file format '".$excelFileType."' isn't accepted. You need to select a csv file.</p>";
                  }
                  elseif(move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $this->tmpDir)){
                          echo "<p>".$this->loading_icon."<b> ".date('H:i:s')." </b>Starting process...</p>";
			  $this->assignCategories();
                  }
                  $this->deleteTmpFile();
	}		
	private function assignCategories(){
   		  require_once dirname(__FILE__).'/../Classes/PHPExcel.php';
                  $inputFileType = PHPExcel_IOFactory::identify($this->tmpDir);
                  $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                  $objPHPExcel = $objReader->load($this->tmpDir);
                  $worksheet  = $objPHPExcel->getActiveSheet();
                  $highestRow = $worksheet->getHighestRow();
		  $errorLines = array();
		  $errorIndex = 0;
		  $successIndex = 0;
		  for($i=2; $i<=(int)$highestRow; $i++){
			  $sku = $worksheet->getCellByColumnAndRow(0, $i);
			  $categories = $worksheet->getCellByColumnAndRow(1, $i);
			  $categories = explode(",", $categories);
			  if($this->categoryExists($categories) && $this->skuExists($sku)){
				  if($this->append == 'append'){
					  $categories = $this->appendNewCategories($sku, $categories);
				  }
				  $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
				  $product->setCategoryIds($categories);
				  $product->save();
				  $successIndex += 1;
			  }else{
				  $errorLines[$errorIndex] = $i;
				  $errorIndex += 1;
			  }
		  }
		  echo "<p>".$this->done_icon."<b> ".date('H:i:s')." </b>Importation complete. <b>".$successIndex."</b> products were assigned to their categories.</p>";
		  if($errorIndex != 0){
			  $errorString = "{";
			  foreach($errorLines as $line){
				  $errorString .= $line.",";
			  }
			  $errorString = substr($errorString, 0, -1);
			  $errorString .= "}";
			  echo "<p>".$this->error_icon."<b> ".date('H:i:s')," </b>The following rows had invalid skus or categories and were ignored: <b>".$errorString."</b></p>";
		  }
	}
	private function categoryExists($categories){
		$ver = True;
		foreach($categories as $id){
			$category = Mage::getModel('catalog/category')->load($id);
			if(!$category->getId()){
				$ver = False;
			}
		}
		return $ver;
	}
	private function skuExists($sku){
		$ver = True;
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		if(!$product){
			$ver = False;
		}
		return $ver;
	}
	private function appendNewCategories($sku, $categories){
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		$oldCategories = $product->getCategoryIds();
		foreach($categories as $category){
			array_push($oldCategories, $category);
		}
		$allCategories = array_unique($oldCategories);
		return $allCategories;

	}
	private function deleteTmpFile(){
                  chmod($this->tmpDir,0755); //Change the file permissions if allowed
                  unlink($this->tmpDir); //remove the file
        }

}
