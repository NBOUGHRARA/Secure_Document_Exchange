# Secure_Document_Exchange

# Installation

Clone the répository:
* git clone https://github.com/NBOUGHRARA/Secure_Document_Exchange.git

get Into project:
* cd Secure_Document_Exchange

Install  dependances:
* composer install

update .env file with your identifiers for MySQL

Create the database:
* php bin/console doctrine:database:create

Make migrations:
* php bin/console doctrine:migrations:migrate

Loading data into database, Required for use of the application:
* php bin/console doctrine:fixtures:load --no-interaction

Launch the serveur:
* php -S 127.0.0.1:8000 -t public

#Enjoy !
