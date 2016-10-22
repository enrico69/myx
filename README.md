# MyX
An educational Symfony 3 based project to administrate and record all your media.
Currently, only books are managed, but the next version will handle movies, games...

Whatever your level in Symfony, JS, HTML5, join the team to work on a real project and to get a reference.

##How to install it?

*First method: clone this repository and make a composer install.
*Or download the full package here

##Setup database connexion

Edit the file app/config/parameters.yml

##Create the database

As ther is no automated installation process yet, you have to use the myx.sql script in the root folder.

##Create the first user account

To create an account, you have to go to this URL : yourdomain.com/register.
Fill the form and register. As there is no admin section at the moment, you have
to activate the account manually. In the database, go to the myx_user table, and in the proper line, set the "enabled" to 1.
Now you can connect to site.

##Edit the welcome message

Currently, as the is no back-office, you can edit the message directly in to the following file app/config/congif.yml. The variable name is: "site_welcome_message"

## What's next?

Here is a short list of the features to be added in the future:

* Management of other media (movies, documentaries, video games)
* Asynchronous home widget
* Webservice
* More control on frontend
* Comments handle in JS
* Possibility to add linked element directly in the book form
* Admin and user space
* Installation process
* SEO Management
* User rights
