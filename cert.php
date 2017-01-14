<?php
require('fpdf.php');
require('common.php');

class Runner
{
	public $name;
	public $distance;
	public $bibNo;
	public $time;
	public $rank;
	public $categoryRank;
	public $genderRank;
}

class PDF extends FPDF
{
	const DPI = 60;
    const MM_IN_INCH = 25.4;
	const A4_WIDTH = 297;
	const A4_HEIGHT = 210;
	const MAX_HEIGHT = 800;
	const MAX_WIDTH = 500;

    function pixelsToMM($val) {
        return $val * self::MM_IN_INCH / self::DPI;
    }
    function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return array(
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height))
        );
    }
    function centreImage($img) {
        list($width, $height) = $this->resizeToFit($img);
        // you will probably want to swap the width/height
        // around depending on the page's orientation
        /*
         * $this->Image(
            $img, (self::A4_HEIGHT - $width) / 2,
            (self::A4_WIDTH - $height) / 2,
            $width,
            $height
        );
        */
        $this->Image(
            $img, (self::A4_HEIGHT - $width) / 2,
            (self::A4_WIDTH - $height) / 2,
            $width,
            $height
        );
    }
}

function IsNullOrEmptyString($question){
    return (!isset($question) || trim($question)==='');
}

function test_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	if (IsNullOrEmptyString($data) == true) die("Argument missing.");
	if (ctype_alnum($data) !== true) die("Incorrect input. Expecting alphanumeric fields only. $data is not valid");
	return $data;
}

function GetResultLine($runName, $bibNo)
{
	$line_counter = 0;
	$filename = $GLOBALS['RESULTS_DIR'].$runName."/timing.csv";;
	$fh = fopen($filename,'r') or die("Could not open result file");
	//The file line is the column names.
	$firstLine = fgets($fh);
	$firstLine = trim($firstLine);
	$columns = explode(",",$firstLine);

	while (! feof($fh)) 
	{
		if ($line = fgets($fh)) 
		{
			$split = split(",", $line);
			if (strcmp($split[0],$bibNo) == 0)
			{
				$i = 0;
				foreach($columns as $column)
				{	
					$runner[$column] = $split[$i];	
					$i++;			
				}
				break;
			}
		}
	}
	fclose($fh);
	return $runner;
}

function LoadRunProperties($runName)
{
	$filename = $GLOBALS['RESULTS_DIR'].$runName."/properties.ini";
	return parse_ini_file($filename);
}

//?run={}&bibno={}
//parse_str(implode('&', array_slice($argv, 1)), $_GET);

$runName = test_input($_GET['run']);
$bibNo = test_input($_GET['bibno']);



//load run Properties
$runProperties = LoadRunProperties($runName);
if ($runProperties == false)
{
	die("Could not load properties file");
}

$distance = $runProperties["distance"];
$certBgImage = $runProperties["cert_bg_image"];

//Get the line from the results file.
$runner_array = GetResultLine($runName, $bibNo);
if (empty($runner_array) == true)
{
	echo "Runner details not found in result file.";
}


//Make a runner
$runner = new Runner();
$runner->name = $runner_array["First name"]." ".$runner_array["Last name"];
$runner->distance = $distance;
$runner->time = $runner_array["Timing (HH:MM:SS.SS)"];
$runner->bibNo = $runner_array["Bib Number"];
$runner->rank = 44;
$runner->categoryRank=38;
$runner->genderRank = 40;




$pdf = new PDF();	
$pdf->AliasNbPages();
$pdf->AddPage("P", "A4");
//Set the bg image.
$bgImageFile = $GLOBALS['RESULTS_DIR'].$runName."/".$certBgImage;
$pdf->centreImage($bgImageFile);

$pdf->Ln(110);

$pdf->SetFont('Helvetica','B',24);
$pdf->SetTextColor(0,200,0);
$pdf->Ln(10);

$pdf->SetFont('Times','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0, 10, "THIS IS TO CERTIFIY THAT", 0, 1, 'C');
$pdf->Cell(0, 10, "NAME: ".$runner->name, 0, 1, 'C');
$pdf->Cell(0, 10, "WITH BIB NUMBER: ".$runner->bibNo, 0, 1, 'C');
$pdf->Cell(0, 10, "PARTICIPATED IN THE ".$runner->distance." RUN CATEGORY", 0, 1, 'C');
$pdf->Cell(0, 10, "AND FINISHED WITH A TIMING OF ".$runner->time, 0, 1, 'C');

$pdf->Cell(0, 10, "(HH:MM:SS.SS)", 0, 1, 'C');
$pdf->Ln(40);

$pdf->Cell(0, 5, "Race Director", 0, 1, 'C');
$pdf->Cell(0, 5, "Shuva Brata Deb", 0, 1, 'C');

$pdf->Output();
?>
