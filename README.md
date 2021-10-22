# php-parse-git-commands
PHP parsers and formatters for output from Git commands, eg. `git show`

## Installation

Using [composer](https://getcomposer.org/) install the [composer package]:

    composer require winternet-studio/php-parse-git-commands "*"

[composer package]: https://packagist.org/packages/winternet-studio/php-parse-git-commands "This package on packagist.org"

## Example

```php
// Get the output from the `git show` command
$output = [];
exec('git show -999', $output);
$output = implode("\n", $output);

// Parse it
$parser = new \winternet\phpGit\GitShow();
$parsedData = $parser->parse($output);
```

`$parsedData` will now contain an array of commits, eg.:

```json
[
    {
        "hash": "65ee18011b6cd82b86480e983c918b5581af16e2",
        "author": "John Doe <john.doe@example.com>",
        "timestamp": "Mon Nov 25 07:57:48 2019 +0100",
        "message": "Some changes, etc\n\nFurther description",
        "unifiedDiff": "diff --git a/about.php b/about.php\nindex 8c7481d..037fa59 100644\n--- a/about.php\n+++ b/about.php\n@@ -1 +1,4 @@\n-This is an About Us page\n+This is an About us page\n+laksdfkajsdf\n+lasjldkfjasdf\n+laskdjfsd\ndiff --git a/index.php b/index.php\nindex 22d90cd..9f87cd5 100644\n--- a/index.php\n+++ b/index.php\n@@ -5,3 +5,5 @@ echo '<div>Date: '. date('Y-m-d H:i') .'</div>';\n function nice_little_function() {\n \treturn 5 * 6;\n }\n+\n+echo nice_little_function();\n\n"
    }
]
```

If you want to parse the unified diff you could use [ptlis/diff-parser](https://github.com/ptlis/diff-parser).
