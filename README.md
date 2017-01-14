Description


Requirements
Any web server install supporting PHP. Apache with PHP on Ubuntu was used for testing.

How to Install
1. Copy the entire repo into any directory inside your DocumentRoot. Exmaple: /var/www/html/.
2. You will have the following directory structure:

ubuntu@ubuntu:/var/www/html$ tree nungsang/
nungsang/
├── cert.php 
├── common.php
├── font
│   ├── courierbi.php
│   ├── courierb.php
│   ├── courieri.php
│   ├── courier.php
│   ├── helveticabi.php
│   ├── helveticab.php
│   ├── helveticai.php
│   ├── helvetica.php
│   ├── symbol.php
│   ├── timesbi.php
│   ├── timesb.php
│   ├── timesi.php
│   ├── times.php
│   └── zapfdingbats.php
├── fpdf.php
├── generateHtml.php
├── html_template.html
├── README.md
└── results
    └── samplerun
        ├── bg.jpg
        ├── properties.ini
        └── timing.csv


Test the install
1. Run the following commands

ubuntu@ubuntu:/var/www/html/nungsang$ php -f generateHtml.php run=samplerun
No of pages = 1 
Generating page 1  with startIndex = 0 endIndex = 1 to file results/samplerun/run_1.html 

2. Go to your browser and open run_1.html. The URL will differ based on your server's DocumentRoot.
In my case its http://localhost/nungsang/results/samplerun/run_1.html

3. This should open a HTML page with two results. 

4. Click on the certificate link to check that certificate generation is working fine. It should open a page with a PDF document.

How to add a new run?
1. Create a CSV file in the format as you see in the sample folder.
2. Create a new directory under the "result/" folder. Lets call it "results/myrun/". Place your CSV file as timing.csv into this folder.
3. Copy the results/samplerun/properties.ini into results/myrun/properties.ini
4. Copy the results/samplerun/bg.jpg into results/myrun/bg.jpg
5. Edit the properties.ini file. Its self explanatory. This is used by the programs.
6. Run the following command to generate the run results HTML pages.

 php -f generateHtml.php run=myrun

7. If everything went well, you will see HTML files created in results/myrun/ directory as run_*.html

8. Go to your browser and open run_1.html. The URL will differ based on your server's DocumentRoot.
In my case its http://localhost/nungsang/results/myrun/run_1.html

9. This should open a HTML page with all results. 

10. Click on the certificate link to check that certificate generation is working fine. It should open a page with a PDF document.

11. Note that only 500 entries are generated per HTML pages. Click on the link on the top of page to go to next page.



How to change HTML page look?
Edit the file html_template.html to customize the HTML page. Take a backup.Dont replace the "$" characters in that file. Dont add any "$" characters in that file. The "$" will be replaced by your content. 


How to customize the certificate?
Create a A4 size image (2480 x 3508 pixels) with web-quality resolution (for small size) and replace results/myrun/bg.jpg with it.

If you want to change the Text content of the certficate, edit the file cert.php. Do it only if you know PHP. Its simple.

