#VgaDatabase - Yet Another PDO Abstraction Layer

Just use this to save some code. This is not a query builder, ypu still have to write your own 
SQL commands.

- only tested with MySQL, but is based on PDO so it might just work.

## Setup
Copy the database_example.ini placed in root folder to wherever, and edit it to fit your needs. 
Then just point to this file when creating the DatabaseConnection instance.

## Usage
From the DatabaseConnection instance us getQuery() and pass a string to it. Then use in various 
ways:
> $dbInstance->connect();
> $dbInstance->getQuery($sql)->execute();

Also works with prepared statements, just use the names as keys in an array of values.
> $fetchedRows = $dbInstance->getQuery($sql)->setValuesSingle($values)->fetchAsArray()->execute();

For repeated executions, put the arrays of values in an array.
> $success = $dbInstance->getQuery($sql)->setValuesMulti($values)->inputQuery()->execute();

## Conclusion
> Available as package through composer, just require "vgait/vgadatabase" in your project.

The class manages the connection, thus usage is pretty simple. Fixes will be made as I try this 
one out in other projects. Also, documentation will be improved over time.

##### Disclaimer: 
I'm just at hobby programmer, and will take no responsibility for how this is used, nor for how 
well it works. But you are free to use this code anyhow you wish, improve upon it and copy it as 
you feel fitting. The only restrictions I have is that this code is not just rebranded and sold 
"as is". Also, this code may never be copyrighted. Other than that, misuse it anyhow you wish.  

