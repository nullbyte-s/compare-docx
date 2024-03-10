<?php

require 'vendor/autoload.php';
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

if(isset($_FILES['fileToUpload'])) {
    $targetDir = "uploads/";
    $originalFileName = basename($_FILES["fileToUpload"]["name"]);
    $docxFileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
    $files = glob($targetDir . '*.docx');
    $totalFiles = count($files);
    $uploadOk = 1;
    $docxFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
    $newFileName = generateUniqueFileName($targetDir, $originalFileName);
    $targetFile = $targetDir . $newFileName;
    
    if($docxFileType != "docx") {
        echo "Apenas arquivos .docx são permitidos.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 1 && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
        echo "O arquivo ". basename( $_FILES["fileToUpload"]["name"]). " foi enviado com sucesso.";
             
        $numSimilar = compareFiles($targetFile, $targetDir);
        $totalFiles++;
        
        echo "<br>Total de arquivos encontrados: " . $totalFiles;
        echo "<br>Número de arquivos semelhantes: " . $numSimilar;
    } else {
        echo "Houve um erro ao enviar o arquivo.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteFile"])) {
    $fileToDelete = $_POST["deleteFile"];
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        echo "Arquivo excluído com sucesso: " . $fileToDelete;
    }
}

function getWordText($element) {
    $result = '';
    if ($element instanceof AbstractContainer) {
        foreach ($element->getElements() as $element) {
            $result .= getWordText($element);
        }
    } elseif ($element instanceof Text) {
        $result .= $element->getText();
    }
    return $result;
}

function generateUniqueFileName($targetDir, $originalFileName) {
    $filenameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
    $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);

    $newFileName = $filenameWithoutExtension . '_' . time() . '.' . $extension;

    while (file_exists($targetDir . $newFileName)) {
        $newFileName = $filenameWithoutExtension . '_' . time() . '_' . rand(1, 1000) . '.' . $extension;
    }

    return $newFileName;
}

function compareFiles($fileToCompare, $targetDir) {
    $objReader = WordIOFactory::createReader('Word2007');    
    $phpWordToCompare = $objReader->load($fileToCompare);
    $files = glob($targetDir . '*.docx');
    $textToCompare = '';
    $numSimilar = 0;
    $similarityPercentage = 0;

    foreach ($phpWordToCompare->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            $textToCompare .= getWordText($element);
        }
    }
    
    foreach ($files as $file) {
        if ($file != $fileToCompare) {
            $phpWord = $objReader->load($file);
            
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= getWordText($element);
                }
            }
            
            similar_text($textToCompare, $text, $similarityPercentage);
            
            if ($similarityPercentage >= 90) {
                unlink($file);
                echo '<br>Elevada semelhança encontrada, o documento armazenado foi substituído pelo recém-enviado';
            } elseif ($similarityPercentage > 70 && $similarityPercentage < 90) {
                $numSimilar++;
            }
        }
    }

    if ($numSimilar > 0) {
        echo '<br><br><form method="post">';
        echo '<table border="1">';
        echo '<tr><th>Documento enviado</th><th>Documento comparado</th><th>Percentual de Semelhança</th><th>Excluir</th></tr>';
        
        foreach ($files as $file) {
            if ($file != $fileToCompare) {
                $phpWord = $objReader->load($file);
                
                $text = '';

                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        $text .= getWordText($element);
                    }
                }
                
                similar_text($textToCompare, $text, $similarityPercentage);
                
                if ($similarityPercentage > 70 && $similarityPercentage < 90) {
                    echo '<tr>';
                    echo '<td>' . basename($fileToCompare) . '</td>';
                    echo '<td>' . basename($file) . '</td>';
                    echo '<td>' . round($similarityPercentage, 2) . '%</td>';
                    echo '<td><button type="submit" name="deleteFile" value="' . $fileToCompare . '">Excluir</button></td>';
                    echo '</tr>';
                }
            }
        }
        echo '</table>';
        echo '</form>';
    }
    
    return $numSimilar;
}
?>