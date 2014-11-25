gerrie:create-database
########################

The `gerrie:create-database` command is responsible to setup the database scheme for the `gerrie:crawl` command.
The `gerrie:create-database` command won`t create the database it selfs.
The database have to already exist.

If the database contains tables the command won`t overwrite something.
The command checks if a table with the same name already exists (this is done by `SHOW TABLES LIKE ...`).
If yes, the command does nothing and will execute the same procedure for the next table.
If the requested table does not exist it will be created (this is done by `CREATE TABLE ...`).

What does this mean?
This means that if you will upgrade from an old version of Gerrie to a newer one and you know that there were database schema changes that this changes won`t be applied to your already existing scheme.
Database scheme changes has to be applied by a different way.

In the normal world you can apply the `gerrie:create-database` to every existing database which contains various tables already.
All tables which will be created by *Gerrie* as prefixed by *gerrie_*.
If you do not get any tables which got the prefix *gerrie_* you can just apply this command to your database.
