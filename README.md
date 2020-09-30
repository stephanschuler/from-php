From PHP
========

This package aims to create config files in various formats out
of PHP code.

Example input file "config.json.php":

```
<?php
return [
    'foo' => 'bar',
    'now' => new \DateTimeImmutable(),
];
```

Example output file "config.json":

```
{
    "foo": "bar",
    "now": {
        "date": "2020-09-30 23:39:05.662150",
        "timezone_type": 3,
        "timezone": "UTC"
    }
}
```

Why?
----

Sometimes config files are short and simple. There is nothing
wrong with those. Stick to the original format.

Some config files are huge with lots of repeating fragments.
Those could be created programmatically, if only JSON or YAML
supported loops and conditions. Using not only the PHP array
format but actual PHP code introduces every control structure
the PHP language provides.

Sometimes configuration is complicated just because there are
so many options and option names and values don't stick to a
limited dictionary or grammar. Here using some interfaces and
serializable classes might come in handy.

What target formats are supported?
----------------------------------

Currently there's JSON and YAML.

How does it work?
-----------------

The `run` script just takes a file name that ends with
".something.php" and basically evaluates it. It's not actually
the PHP `eval` call but its really close.

The source file is supposed to `return` the configuration data
in a way that can be handled by `json_encode`.

Finally the result is dumped to another file named just like
the original one but without the ".php" file type.

Integration:
------------

My primary use case is using this file with PhpStorm. Ideally
it gets automatically executed whenever I edit a configuration
file.

Step one is to create a so called "scope", which is a pattern
matching some file and folder names.

See: https://www.jetbrains.com/help/phpstorm/settings-scopes.html#legend

The actual pattern is "file:\*.json.php" for JSON files and
"file:\*.yaml.php" for YAML files. I coll mine "JSON-PHP" and
"YAML-PHP".

Step two is creating a file watcher. Select "File Type" `PHP`
and set "Scope" to `JSON-PHP` to identify the file, point
"Program" to the run script of this repository and provide
`$FilePath$` as the "Argument".

See: https://www.jetbrains.com/help/phpstorm/using-file-watchers.html
