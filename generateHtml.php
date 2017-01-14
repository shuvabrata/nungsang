<?php
require('common.php');



function GeneratePageLinks($page, $pages)
{
	$line = "";
    for ($i = 1; $i <= $pages; $i++)
		{
        if ($i == $page)
        {
            $line .= "<b>".$i."</b> ";
        }
        else
        {
            $line .= "<a href=\"run_".$i.".html\">".$i."</a> ";
        }
	}
	return $line;
}

function GenerateTableHeaders($columns)
{
	/*
	 *  <tr>
    <th>Rank</th>
    <th>Name</th>
    <th>Bib #</th>
    <th>Gender</th>
    <th>Gender Rank</th>
    <th>Category</th>
    <th>Category Rank</th>
    <th>Net Time</th>
    <th>Gross Time</th>
  </tr>
  * */
	$header = "";
	$header .= "<tr>";
	
	foreach($columns as $column)
	{
		$header .= "<th>".$column."</th>";
	}
	
	$header .= "</tr>";
	return $header;
}

function WriteTableRows($myfile, $startIndex, $endIndex, $runners)
{
	for ($i = $startIndex; $i <= $endIndex; $i++)
	{
		$row = "";
		$row .= "<tr>";
		$runner = $runners[$i];
		foreach($runner as $cell)
		{
			$row .= "<td>".$cell."</td>";
		}
		$row .= "</tr>";
		fwrite($myfile, $row);
	}
}

function GeneratePage($page, $pages, $runners, $entriesPerPage, $columns, $runName)
{
	
	//Read the template.
	$templateFile = $GLOBALS['HTML_TEMPLATE_FILE'];
	if (!file_exists($templateFile))
	{
		exit("Template file $templateFile not found. \n");
	}
	$templateData = file_get_contents($templateFile);
	
	$templateDataSplit = explode("$", $templateData);
	
	$startIndex = ($page-1) * $entriesPerPage;
	$endIndex = $startIndex + $entriesPerPage - 1;
	//if last page, then we dont have all elements
    if ($page == $pages)
    {
		$endIndex = $startIndex + (sizeof($runners) % $entriesPerPage) - 1;
	}
	$file = $GLOBALS['RESULTS_DIR'].$runName."/run_".$page.".html";
	
	print ("Generating page $page  with startIndex = $startIndex endIndex = $endIndex to file $file \n");
	$myfile = fopen($file, "w") or die("Unable to open file!");
	
	if (!is_resource($myfile))
	{
		die ("Could not open file $myfile to write\n");
	}
		
	//Write the first part of the page. The header of the HTML page.
	fwrite($myfile, $templateDataSplit[0]);
	
	//Write the page links
	$pageLinks = GeneratePageLinks($page, $pages);
	fwrite($myfile, $pageLinks);
	
	//Write the second part. some html
	fwrite($myfile, $templateDataSplit[1]);
	
	//Write the table headers
	$tableHeaders = GenerateTableHeaders($columns);
	fwrite($myfile, $tableHeaders);
	
	//Write the 3rd part. Table rows
	WriteTableRows($myfile, $startIndex, $endIndex, $runners);
	
	//Write the HTML after the table.
	fwrite($myfile, $templateDataSplit[2]);
	
	//Write page links after the table.
	//fwrite($myfile, $pageLinks);
	
	//Write the rest of html
	fwrite($myfile, $templateDataSplit[3]);
	
	//Write the page links
	fwrite($myfile, $pageLinks);
	
	//Write rest of html
	fwrite($myfile, $templateDataSplit[4]);
	fclose($myfile);
	

}

function GenerateCertLink($run, $bibNo)
{
	$link = "../../cert.php?run=".$run."&bibno=".$bibNo;
	return "<a href='".$link."'>Certificate</a>";
}

parse_str(implode('&', array_slice($argv, 1)), $_GET);

$runName = $_GET['run'];

//Validate input
if (!ctype_alnum($runName))    
        exit("The run name $runName does not consist of all letters or digits.\n");
    

//check if CSV file exists.
$resultFile = $RESULTS_DIR.$runName."/timing.csv";
if (!file_exists($resultFile))
{
	exit("File $resultFile not found. \n");
}

$outputDir = $RESULTS_DIR.$runName."/";

//Load CSV file into runner objects.
$coulmns = array();
$fileHandle = fopen($resultFile, "r") or die("Could not open file $resultFile");

//The file line is the column names.
$firstLine = fgets($fileHandle);
$firstLine = trim($firstLine);
$firstLine .= ",Certificate";
$columns = explode(",",$firstLine);

$runners= array();
$numberOfRunners = 0;
while (($line = fgets($fileHandle)) !== false)
{
		$line = trim($line);
		$line .= ",1";
		$runner = array();
		$split = explode(",", $line);
		$i =0;
		foreach($columns as $column)
		{	
			if (strcmp($column,"Certificate") == 0)
			{
				$certLink = GenerateCertLink($runName, $split[0]);
				$runner[$column] = $certLink;
			}
			else
			{
				$runner[$column] = $split[$i];				
			}
			$i++;
		}
		$runners[$numberOfRunners] = $runner;
		$numberOfRunners++;
		
}
fclose($fileHandle);

$numberOfPages = ceil(sizeof($runners)/$ENTRIES_PER_PAGE);

print "No of pages = $numberOfPages \n";
if ($numberOfPages == 0)
{
	die("Number of pages is zero. Nothing to generate \n");
}

for ($page = 1; $page <= $numberOfPages; $page++)
{
	GeneratePage($page, $numberOfPages, $runners, $ENTRIES_PER_PAGE, $columns, $runName);
}


?>
