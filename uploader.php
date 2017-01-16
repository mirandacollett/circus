<?php
$message="Starting uploader.php. The superglobal contains "
        .sizeof($_FILES)
        ." file(s): "
        .$_FILES["Filedata"]["name"]
        ."<br>";
$AllowedExtensions = array('pdf');
$AllowedFileTypes = array('application/pdf');
if (!empty($_FILES)) {
    $tempFile   = $_FILES['Filedata']['tmp_name'];
    /* ================================================ Validate the filetype */
    $OKtoUpload=TRUE;
    $fileParts = pathinfo($_FILES['Filedata']['name']);
    if (!in_array(strtolower($fileParts['extension']), $AllowedExtensions) ) {
        $OKtoUpload=false;
        $message.='<br>Invalid file extension: '.strtolower($fileParts['extension']);
    }
    if (!in_array($_FILES["Filedata"]["type"],$AllowedFileTypes) ) {
        $OKtoUpload=false;
        $message.='<br>Invalid file type: '.$_FILES["Filedata"]["type"];
    }
    /* ====================================================== Upload the file */
    if ($OKtoUpload) {
        $message.='<br> Moving '.$tempFile;
        $DT = filectime($tempFile);
        $upload_dir = realpath(dirname(__FILE__)) . "/uploads/";
        if (!file_exists($upload_dir)) {echo 'Upload directory '.$upload_dir.' does not exist. ';}
        if (!is_writable($upload_dir)) {echo 'Upload directory '.$upload_dir.' is not writable. ';}
        $targetFile = $upload_dir.$_FILES['Filedata']['name']; 
        $counter=1;
        while (file_exists($targetFile)){
            $targetFile=$upload_dir.$fileParts['filename'].' Copy('.$counter.').'.$fileParts['extension'];
            $counter++;
        }
        $message.='<br> Moving to '.$targetFile;
        if (move_uploaded_file($tempFile, $targetFile)){
            $message.="<br>Moved the file<br>";
        } else {
            $message.="<br>Move file failure<br>";
        }
    }
    else {
        $message.='<br>No files found';
    }
}
echo $message;
