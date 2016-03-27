php-puush-upload
======

PHP script for uploading files to puush.me

#### Installation
You must set the configuration file in `settings.json`.

#### Configuration
`"dir"` is the folder where the files are.

`"backupDir"` is the folder where the files is transfered after uploading.

`"mimes"` is the type of file you accept, you can find a complete list  [here](http://www.sitepoint.com/web-foundations/mime-types-complete-list/).

`"ignoredFiles"` is the list of files you reject.

`"nameLogFile"` is the file that are stored urls.

`"apiKey"` is the key that will allow you to use API, you can find this key in [here](http://puush.me/account/settings)

The file should look like this:

```
{
  "dir": "files",
  "backupDir": "backups",
  "mimes": ["image/jpeg", "image/png"],
  "ignoredFiles": [".DS_Store"],
  "nameLogFile": "DB",
  "apiKey": "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
}
```

#### Usage
Just execute the file and drink your coffee.

```
php app.php
```
