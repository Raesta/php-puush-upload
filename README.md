php-puush-upload
======

PHP script for uploading files to puush.me

#### Installation/Configuration
You must set the configuration file in
```
settings.json
```

The file should look like this:

```
{
  "backupDir": "backups",
  "mimes": ["image/jpeg", "image/png"],
  "ignoredFiles": [".DS_Store"],
  "nameLogFile": "DB",
  "apiKey": "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
}
```

#### Usage
Just execute the file and drink your coffee

```
php app.php
```
