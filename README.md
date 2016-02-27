EonDiceWebsite
==============

This is a game room inspired website for EonDice.

To use this code, you'll need to do a few things:

* Your system needs to have the file `/dev/urandom`.
* In `database.php` you need to change the following things:
  * On line 10: Set `masterPasscode` to your master passcode.
  * On line 21: Set `host` to your psql host name.
  * On line 24: Set `dbname` to your psql database name.
  * On line 27: Set `user` to your psql user name.
  * On line 30: Set `password` to your psql password.
* In your psql database, load the file `schema.sql`.
