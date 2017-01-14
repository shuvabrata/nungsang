Description


Requirements
Any web server install supporting PHP. Apache with PHP on Ubuntu was used for testing.

How to Install
1. Copy the entire repo into any directory inside your DocumentRoot. Exmaple: /var/www/html/.
2. You will have the following directory structure:
<<<<<<< HEAD

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




How to change HTML page look?


How to customize the certificate?
=======
>>>>>>> 3e021501548dd483ceefd8105843b3d30940fc4b
