This is just an layer to save some code when using PDO.

Just copy the config_test.ini to anywhere, and name it whatever you want.
When creating the connection instance just use this file, and your settings will be used.

The class has two public functions, read and write.

Read is basically when you expect som results from the database, and write is when you want to insert & edit, and don't
expect any result to be returned.

The class will only create a single connection to the database server, and reuse it for every call you make.
You should call the close() method whn you are sure you do not want to use the instance anymore.
