# cs4626_fall24_project
"Hello group!" This is the repository for our project files for the semester project.

Here's how to run it!

1. Download XAMPP for your given Operating System.

2. If using VS Code, then download if you haven't already and connect your account to your github account that has edit permissions for this project.
  Then in either case, you must put this repository into your XAMPP htdocs folder, such as C:\\xampp\htdocs. If using VS Code then you will be cloning the repository into this folder and sourcing it from there as 
  your document root.
  
3. Launch the XAMPP Control Panel and start the Apache and mySQL servers
4. You will need to go to localhost/myphpadmin in order to create your localhost database. Create your database and create a table called users, with the following variables.
   id as the primary key, set to an int with a length of 100, also set to auto increment, email varchar(255), password varchar(255), firstName varchar(255), lastName varchar(255), and iv varchar(255)
5. Create a table called accountbalance and create the following columns: account_id int(100), balance(decimal), cardNumber var(255), cardExpiry(date)
6. Create a key dependency between the users id key and the accountbalance account_id key to make sure that users are linked with the two tables

7. Then go to "localhost/index.html" in your URL bar to run the website locally on your computer. You will need to create a few users with the register page, just follow the link on the top right on the index.html page.
   
8.Once you have created a few users you can then log in and mess around with the demo.

  
    
