# SQLDocs

Parses SQL files which contain `CREATE TABLE` statements and generates docblocks and/or HTML documentation.

### Shout Out to Skeema

We manage our MySQL database schemas using [Skeema](https://www.skeema.io/). This allows us to define and manage our database in a declarative manner. It lends itself well to documenting each table with a doc block and in turn generating documentation
from those docblocks.

## Requirements

* SQLDocs assumes that each `CREATE TABLE` statement is defined in a separate .sql file.

## Database Support

* MySQL

## Example

This repo contains an example directory with a `.sqldoc.yaml` file and two schema directories. This is the recommended way to configure your schema files.

The file `example/users/users.sql` has a pre-defined docblock with column descriptions defined.

The file `example/content/content.sql` does not have a doc block.

To see the doc block that would be generated for these files, the following command can be used.

```sh
$ ./bin/sqldoc --dir example/ --config example/.sqldoc.yaml --test
```

The output would be similar to this.

```
Using config file example/.sqldoc.yaml
Parsing example/cms/content.sql...
/**
 * Table content
 *
 * @column   bigint(unsigned)   content_id   Not Null
 * @column   varchar(64)        author       Not Null
 * @column   varchar(150)       headline     Not Null
 * @column   varchar(255)       summary      Not Null
 * @column   TEXT               body         Not Null
 *
 * @key   unique   primary    (content_id)
 *
 * @schema            cms
 * @name              content
 * @engine            InnoDB
 * @default_charset   utf8
 */
Parsing example/users/users.sql...
/**
 * Table users
 *
 * @column   int(unsigned)   user_id          Not Null   Unique User Id
 * @column   varchar(255)    email            Nullable   User's email address. Default: NULL
 * @column   varchar(255)    email_hash       Nullable   Hash of the user's email address. Default: NULL
 * @column   varchar(255)    password         Not Null   User's encrypted password.
 * @column   datetime        wh_update_date   Nullable   Date the record was last updated. Default: NULL
 * @column   datetime        wh_insert_date   Not Null   Date the recorde was created. Default: CURRENT_TIMESTAMP
 *
 * @key   unique   primary              (user_id)
 * @key   unique   email                (email)
 * @key   unique   username             (username)
 * @key   unique   email_hash           (email_hash)
 * @key            by_wh_update_date    (wh_update_date)
 * @key            by_wh_insert_date    (wh_insert_date)
 *
 * @schema            users
 * @name              users
 * @engine            InnoDB
 * @default_charset   utf8mb4
 * @collation         utf8mb4_unicode_ci
 */
Test mode. No files updated.
Test mode. No HTML generated.
```

## Command Line Options

All options with the exception of `config` can be set in a `.sqldoc.yaml` file. Command line options override options defined in the config file.

```
--config              CONFIG_FILE  Location of sqldoc YAML file. If a file
                                   named .sqldoc.yaml is found in the
                                   current directory, it will be used by
                                   default.

--dir                 DIR[,DIR]    Directory containing .sql files.

--file                FILE[,FILE]  File

--generate-docblocks               If true, doc blocks will be added/updated
                                   in .sql files.

--generate-html                    If true, HTML documentation will be
                                   generated

--generate-html-dir   DIR          Directory where HTML documentation is
                                   generated (default 'html')

 -h                                Shows this help

--index-template      FILENAME     Name of template file for generating the
                                   index page. Default: default.twig

--project-name        NAME         Project name to be used when generating
                                   documentation.

--schema              SCHEMA       Schema (aka database) name to add to doc
                                   blocks

--schema-dir                       Uses the parent directory name of the
                                   file as the schema name

--schema-template     FILENAME     Name of template file for generating the
                                   schema page. Default: default.twig

--table-template      FILENAME     Name of template file for generating the
                                   table page. Default: default.twig

--template-dir        DIR          Location where template files are
                                   located. Default: src/template

--test                             Doc blocks are output and files are not
                                   updated
```

## Running via Docker

There is a docker image available for SQLDocs.

```sh
docker run --rm \
  -v /path/to/schema:/sqldoc \
  dealnews/sqldoc:latest [options]
```

## Custom Templates

[Twig](https://twig.symfony.com/doc/3.x/) templates are used to generate HTML documentation.

The CSS framework [Bulma](https://bulma.io/) is used in the default HTML template.

## TODO

* Create a phar executable
* Add to Packagist to allow installing via Composer
* PostgreSQL support
* More test coverage
