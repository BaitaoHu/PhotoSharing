To get started:

1. Clone this repo into the `public_html` directory on your ugrad account
2. Grant the repo directory the right permissions by running `chmod 711 ~/public_html/cpsc304project`.
3. `cd` into this directory, then open `sqlplus` and login with username `ora_a1b2@ug` and password `a12345678` (where `a1b2` is your ugrad id and `12345678` is your student number)
4. Run `start database.sql` to generate your copy of the database
5. Run `chmod 755 *.php` to grant executable permissions to each PHP file. You need to do this for any PHP files you create too.
6. Change `db_username` and `db_password` in `config.php` to point to your SQL username/password
7. Visit www.ugrad.cs.ubc.ca/~csid/cpsc304project/filename.php
