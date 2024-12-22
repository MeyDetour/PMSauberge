GREEN = /bin/echo -e "\x1b[32m\#\# $1\x1b[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

# Fonction pour afficher des messages colorés
define color_echo
    @$(call $(1), $(2))
endef

# ----- Programs -----
COMPOSER = composer
PHP = php
SYMFONY = symfony
SYMFONY_CONSOLE = symfony console

init: ## Initialize project
	$(MAKE) db-create
ip:
	hostname -I


## -------- SYMFONY COMMAND --------

compile :
	$(SYMFONY_CONSOLE) asset-map:compile
entity:
	$(SYMFONY_CONSOLE) make:entity
controller:
	$(SYMFONY_CONSOLE) make:controller

save:
	$(call color_echo, GREEN, "Lets go to make migration and migrate to the database...")
	$(SYMFONY_CONSOLE) make:migration
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate
form :
	$(SYMFONY_CONSOLE) make:form

reload-migrations:
	rm -R migrations/
	mkdir migrations

cc:
	$(SYMFONY_CONSOLE) cache:clear
user:
	$(SYMFONY_CONSOLE) make:user
	$(SYMFONY_CONSOLE) make:registration-form
	$(SYMFONY_CONSOLE) make:auth

## -------- fixtures --------
load-fixtures:
	$(call color_echo, GREEN, "Fixtures loaded")
	$(SYMFONY_CONSOLE) doctrine:fixtures:load
install-fixtures:
	$(COMPOSER) require --dev orm-fixtures
reset:
	$(MAKE) db-reload
	$(MAKE) reload-migrations
	$(MAKE) save
	$(MAKE) load-fixtures


## -------- faker --------
install-faker:
	$(COMPOSER) require fakerphp/faker



## -------- liip --------
liip-cache:
	$(SYMFONY_CONSOLE) liip:imagine:cache:remove



## -------- JWT --------
jwt-key:
	$(SYMFONY_CONSOLE) lexik:jwt:generate-keypair

## -------- database --------
db-create: ## Create database
	$(SYMFONY_CONSOLE) doctrine:database:create

db-drop: ## Drop the database
	$(call color_echo, GREEN, "Dropping database...")
	$(SYMFONY_CONSOLE) doctrine:database:drop --force --if-exists

db-reload: ## Drop and create the database
	$(MAKE) db-drop
	$(MAKE) db-create


## -------- Install Symfony --------
symfony:
	$(MAKE) symfony-cli
	$(MAKE) install-composer
	$(SYMFONY) check:requirements

symfony-cli:
	curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
	sudo apt install symfony-cli
prod:
	$(COMPOSER) dump-env prod
init-on-server:
	$(MAKE) db-create
	$(MAKE) reload-migrations
	$(MAKE) save
	$(MAKE) apache-pack

apache-pack:
	$(COMPOSER) require symfony/apache-pack
## -------- Get PHp 8.3 on symfony --------
php-version:
	sudo update-alternatives --install /usr/bin/php php /usr/bin/php8.3 1
	sudo update-alternatives --config php
	symfony local:php:list
	sudo a2dismod php8.1
	sudo a2enmod php8.3
	sudo systemctl restart apache2



## -------- Installer --------
install-composer:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"
	sudo mv composer.phar /usr/local/bin/composer
	composer update

install-webpack:
	$(COMPOSER) require symfony/webpack-encore-bundle
	sudo apt install npm
	npm install bootstrap --save-dev
	#import 'bootstrap/dist/css/bootstrap.min.css'; {% block stylesheets %} {{ encore_entry_link_tags('app') }} {% endblock %}   {% block javascripts %}{{ encore_entry_script_tags('app') }}  {% endblock %}

	npm install bootstrap --save-dev
	npm install jquery @popperjs/core --save-dev
	npm run build
	npm run dev
install-vich:
	$(COMPOSER) require vich/uploader-bundle
install-liip:
	$(COMPOSER) require liip/imagine-bundle

install-dropzone:
	$(COMPOSER) require symfony/ux-dropzone

# liip_imagine:
#     driver: "gd"
#     resolvers:
#         default:
#             web_path: ~
#
#     filter_sets:
#         cache: ~
#         projet:
#             quality: 45
#             filters:
#                 thumbnail: { size: [900, 400], mode: outbound }
#
#         icone:
#             quality: 80
#             filters:
#                 thumbnail: { size: [60, 60], mode: outbound }


